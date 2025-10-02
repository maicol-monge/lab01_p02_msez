<?php
require_once 'modelos/estadisticasmodel.php';

class EstadisticasController
{
    private $model;

    public function __construct()
    {
        $this->model = new EstadisticasModel();
    }

    public function index()
    {
        $stats = $this->model->obtenerEstadisticas();
        // Usuarios para filtros
        $usuarios = $this->model->obtenerUsuariosActivos();
        include 'vistas/estadisticas/index.php';
    }

    // Endpoint para generar PDF que recibe JSON con image (data URI) y filtros
    public function exportar_pdf()
    {
        // Leer filtros (JSON o POST/GET)
        $raw = file_get_contents('php://input');
        $data = json_decode($raw, true);
        if (is_array($data) && isset($data['filtros'])) {
            $filtros = [
                'filtroTipo'   => $data['filtros']['filtroTipo']   ?? 'todos',
                'filtroEstado' => $data['filtros']['filtroEstado'] ?? 'todos',
                'tipoVista'    => $data['filtros']['tipoVista']    ?? 'combinado',
            ];
        } else {
            $filtros = [
                'filtroTipo'   => $_POST['filtroTipo']   ?? $_GET['filtroTipo']   ?? 'todos',
                'filtroEstado' => $_POST['filtroEstado'] ?? $_GET['filtroEstado'] ?? 'todos',
                'tipoVista'    => $_POST['tipoVista']    ?? $_GET['tipoVista']    ?? 'combinado',
            ];
        }

        // Datos filtrados para resumen + imagen
        [$labels, $values, $titulo, $total] = $this->prepararDatosGrafico($filtros);
        if ($total <= 0) {
            http_response_code(400);
            header('Content-Type: text/plain; charset=utf-8');
            echo 'No hay datos para generar el gráfico con los filtros aplicados.';
            return;
        }

        $imageData = $this->generarImagenGrafico($filtros);
        if ($imageData === false) {
            http_response_code(500);
            header('Content-Type: text/plain; charset=utf-8');
            echo 'No se pudo generar la imagen del gráfico.';
            return;
        }

        $imgDataUri = 'data:image/png;base64,' . base64_encode($imageData);

        // Subtítulo con filtros aplicados
        $sub = [];
        $sub[] = ($filtros['filtroTipo']==='todos' ? 'Todos los tipos'   : 'Tipo: '   . htmlspecialchars($filtros['filtroTipo']));
        $sub[] = ($filtros['filtroEstado']==='todos' ? 'Todos los estados' : 'Estado: ' . htmlspecialchars($filtros['filtroEstado']));
        $sub[] = 'Vista: ' . htmlspecialchars($filtros['tipoVista']);
        $subtitle = implode(' · ', $sub);

        // Resumen por segmento (solo del subconjunto filtrado)
        $rows = '';
        foreach ($labels as $i => $label) {
            $val = (int)$values[$i];
            $pct = $total ? round(($val/$total)*100, 1) : 0;
            $rows .= '<tr><td>'.htmlspecialchars($label).'</td>'
                   . '<td style="text-align:right">'.number_format($val).'</td>'
                   . '<td style="text-align:right">'.$pct.'%</td></tr>';
        }

        $html = '<html><head><meta charset="utf-8"><style>
            body{font-family:Arial,Helvetica,sans-serif;font-size:12px;color:#222;margin:22px}
            h1{font-size:18px;margin:0 0 6px 0}
            .muted{color:#666;margin:0 0 12px 0}
            .imgbox{border:1px solid #eee;padding:6px;border-radius:6px}
            table{width:100%;border-collapse:collapse;margin-top:14px}
            th,td{border:1px solid #e5e5e5;padding:6px 8px}
            th{background:#f6f8fa;text-align:left}
        </style></head><body>
        <h1>Gráfico — Distribución de mascotas</h1>
        <div class="muted">'.$subtitle.' · Generado: '.date('Y-m-d H:i').'</div>
        <div class="imgbox"><img src="'.$imgDataUri.'" style="width:100%;max-width:720px;height:auto"/></div>
        <h2 style="margin-top:16px;font-size:16px">Resumen (filtros aplicados)</h2>
        <table>
          <thead><tr><th>Segmento</th><th style="text-align:right">Cantidad</th><th style="text-align:right">% del total</th></tr></thead>
          <tbody>'.$rows.'<tr><th>Total</th><th style="text-align:right">'.number_format($total).'</th><th></th></tr></tbody>
        </table>
        </body></html>';

        // Dompdf
        if (file_exists(__DIR__ . '/../vendor/autoload.php')) { require_once __DIR__ . '/../vendor/autoload.php'; }
        if (class_exists('Dompdf\\Dompdf')) {
            $dompdf = new \Dompdf\Dompdf();
            $dompdf->loadHtml($html, 'UTF-8');
            $dompdf->setPaper('A4','portrait');
            $dompdf->render();
            header('Content-Type: application/pdf');
            header('Content-Disposition: attachment; filename="grafico_distribucion.pdf"');
            echo $dompdf->output();
            return;
        }

        // Fallback HTML
        header('Content-Type: text/html; charset=utf-8');
        echo $html;
    }

    // Nuevo: Endpoint para exportar solo datos en PDF (sin gráfico)
    public function exportar_datos_pdf()
    {
        $filtroTipo   = $_POST['filtroTipo']   ?? $_GET['filtroTipo']   ?? 'todos';
        $filtroEstado = $_POST['filtroEstado'] ?? $_GET['filtroEstado'] ?? 'todos';
        $tipoVista    = $_POST['tipoVista']    ?? $_GET['tipoVista']    ?? 'combinado';

        [$labels, $values, $titulo, $total] = $this->prepararDatosGrafico([
            'filtroTipo' => $filtroTipo, 'filtroEstado' => $filtroEstado, 'tipoVista' => $tipoVista
        ]);

        // Construir tabla de datos (aunque no haya datos, se genera PDF con "Sin datos")
        $rows = '';
        if ($total > 0) {
            foreach ($labels as $i => $label) {
                $val = (int)$values[$i];
                $pct = $total ? round(($val/$total)*100, 1) : 0;
                $rows .= '<tr><td>'.htmlspecialchars($label).'</td>'
                       . '<td style="text-align:right">'.number_format($val).'</td>'
                       . '<td style="text-align:right">'.$pct.'%</td></tr>';
            }
        } else {
            $rows = '<tr><td colspan="3">Sin datos con los filtros aplicados.</td></tr>';
        }

        $html = '<html><head><meta charset="utf-8"><style>
            body{font-family:Arial,Helvetica,sans-serif;font-size:12px;color:#222;margin:22px}
            h1{font-size:18px;margin:0 0 6px 0}
            .muted{color:#666;margin:0 0 12px 0}
            table{width:100%;border-collapse:collapse;margin-top:8px}
            th,td{border:1px solid #e5e5e5;padding:6px 8px}
            th{background:#f6f8fa;text-align:left}
        </style></head><body>
        <h1>Reporte de datos — Distribución de mascotas</h1>
        <div class="muted">Filtros: '.
            ($filtroTipo==='todos'?'Todos los tipos':('Tipo: '.htmlspecialchars($filtroTipo))).' · '.
            ($filtroEstado==='todos'?'Todos los estados':('Estado: '.htmlspecialchars($filtroEstado))).' · '.
            'Vista: '.htmlspecialchars($tipoVista).' · Generado: '.date('Y-m-d H:i').
        '</div>
        <table><thead><tr><th>Segmento</th><th style="text-align:right">Cantidad</th><th style="text-align:right">% del total</th></tr></thead>
        <tbody>'.$rows.($total>0?('<tr><th>Total</th><th style="text-align:right">'.number_format($total).'</th><th></th></tr>'):'').'</tbody></table>
        </body></html>';

        // Dompdf
        $autoload = __DIR__ . '/../vendor/autoload.php';
        if (file_exists($autoload)) require_once $autoload;
        if (class_exists('Dompdf\\Dompdf')) {
            $dompdf = new \Dompdf\Dompdf();
            $dompdf->loadHtml($html, 'UTF-8');
            $dompdf->setPaper('A4','portrait');
            $dompdf->render();
            header('Content-Type: application/pdf');
            header('Content-Disposition: attachment; filename="reporte_distribucion.pdf"');
            echo $dompdf->output();
            return;
        }

        // Fallback HTML
        header('Content-Type: text/html; charset=utf-8');
        echo $html;
    }

    // Endpoint para exportar Excel (simplemente HTML con headers para Excel)
    public function exportar_excel()
    {
        $filtroTipo   = $_POST['filtroTipo']   ?? $_GET['filtroTipo']   ?? 'todos';
        $filtroEstado = $_POST['filtroEstado'] ?? $_GET['filtroEstado'] ?? 'todos';
        $tipoVista    = $_POST['tipoVista']    ?? $_GET['tipoVista']    ?? 'combinado';

        [$labels, $values, $titulo, $total] = $this->prepararDatosGrafico([
            'filtroTipo' => $filtroTipo, 'filtroEstado' => $filtroEstado, 'tipoVista' => $tipoVista
        ]);

        $filename = 'reporte_distribucion_' . date('Ymd_His') . '.csv';
        header('Content-Type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment; filename="'.$filename.'"');
        echo "\xEF\xBB\xBF"; // UTF-8 BOM
        echo "sep=,\r\n";
        $out = fopen('php://output', 'w');

        fputcsv($out, ['Reporte','Generado']);
        fputcsv($out, ['Distribución de mascotas', date('Y-m-d H:i')]);
        fputcsv($out, ['Filtros', ($filtroTipo==='todos'?'Todos los tipos':"Tipo: $filtroTipo").' · '.($filtroEstado==='todos'?'Todos los estados':"Estado: $filtroEstado").' · Vista: '.$tipoVista]);
        fputcsv($out, []);
        fputcsv($out, ['Segmento','Cantidad','% del total']);

        if ($total > 0) {
            foreach ($labels as $i=>$label) {
                $val = (int)$values[$i];
                $pct = $total ? round(($val/$total)*100, 2) : 0;
                fputcsv($out, [$label, $val, $pct]);
            }
            fputcsv($out, ['Total', $total, '']);
        } else {
            fputcsv($out, ['Sin datos con los filtros aplicados', '', '']);
        }
        fclose($out);
        exit;
    }


    // Genera y retorna PNG binario del gráfico según filtros
    private function generarImagenGrafico($filtros = [])
    {
        // Construir series filtradas
        [$labels, $values, $titulo, $total] = $this->prepararDatosGrafico([
            'filtroTipo'   => $filtros['filtroTipo']   ?? 'todos',
            'filtroEstado' => $filtros['filtroEstado'] ?? 'todos',
            'tipoVista'    => $filtros['tipoVista']    ?? 'combinado',
        ]);
        if ($total <= 0) return false;

        // Intentar JPGraph
        $jpgraphAutoload = __DIR__ . '/../vendor/jpgraph/src/jpgraph.php';
        $jpgraphDir = __DIR__ . '/../vendor/jpgraph/src';
        if (file_exists($jpgraphAutoload) || class_exists('Graph')) {
            try {
                if (file_exists($jpgraphAutoload)) require_once $jpgraphAutoload;
                else @require_once 'jpgraph/jpgraph.php';
                if (!class_exists('PiePlot')) @require_once $jpgraphDir . '/jpgraph_pie.php';

                // NOTA: algunos paquetes usan PieGraph, otros Graph. Si tienes PieGraph, úsalo:
                if (class_exists('PieGraph')) {
                    $graph = new \PieGraph(700,400);
                } else {
                    $graph = new \Graph(700,400,"auto");
                    $graph->SetScale('lin');
                }
                $graph->img->SetMargin(40,30,20,80);
                $graph->title->Set($titulo);

                $p1 = new \PiePlot($values);
                $p1->SetLegends($labels);
                $graph->Add($p1);

                ob_start();
                $graph->Stroke(_IMG_HANDLER);
                $gdImg = $graph->img->img;
                imagepng($gdImg);
                $png = ob_get_clean();
                return $png;
            } catch (\Throwable $e) {
                // fallback a GD
            }
        }

        // Fallback GD
        if (!function_exists('imagecreatetruecolor')) return false;
        $width = 700; $height = 400;
        $im = imagecreatetruecolor($width,$height);
        $white = imagecolorallocate($im,255,255,255);
        imagefill($im,0,0,$white);

        $palette = [
            imagecolorallocate($im,54,162,235),
            imagecolorallocate($im,255,99,132),
            imagecolorallocate($im,255,205,86),
            imagecolorallocate($im,75,192,192),
            imagecolorallocate($im,153,102,255),
            imagecolorallocate($im,255,159,64),
        ];

        $cx = 250; $cy = 200; $r = 150;
        $start = 0; $i = 0;
        foreach ($values as $v) {
            $angle = ($v / $total) * 360;
            $color = $palette[$i % count($palette)];
            imagefilledarc($im, $cx, $cy, $r*2, $r*2, $start, $start + $angle, $color, IMG_ARC_PIE);
            $start += $angle; $i++;
        }

        // Leyenda
        $x = 480; $y = 40; $i = 0;
        $fontColor = imagecolorallocate($im,0,0,0);
        foreach ($labels as $idx => $label) {
            $color = $palette[$i % count($palette)];
            imagefilledrectangle($im, $x, $y+$i*28, $x+20, $y+16+$i*28, $color);
            $val = (int)$values[$idx];
            imagestring($im, 3, $x+28, $y-2+$i*28, $label . ' (' . $val . ')', $fontColor);
            $i++;
        }

        // Título
        imagestring($im, 5, 10, 10, $titulo, $fontColor);

        ob_start(); imagepng($im); $png = ob_get_clean(); imagedestroy($im);
        return $png;
    }

    /**
     * Devuelve [labels, values, titulo, total] aplicando filtroTipo, filtroEstado y tipoVista
     */
    private function prepararDatosGrafico(array $filtros): array
    {
        $filtroTipo   = $filtros['filtroTipo']   ?? 'todos';
        $filtroEstado = $filtros['filtroEstado'] ?? 'todos';
        $tipoVista    = $filtros['tipoVista']    ?? 'combinado';

        $stats = $this->model->obtenerEstadisticas();
        $dist  = isset($stats['distribucion']) && is_array($stats['distribucion']) ? $stats['distribucion'] : [];

        // Filtrar por tipo/estado
        $filtrada = array_values(array_filter($dist, function($it) use ($filtroTipo,$filtroEstado){
            $okTipo   = ($filtroTipo==='todos')   || (($it['tipo_nombre'] ?? '') === $filtroTipo);
            $okEstado = ($filtroEstado==='todos') || (($it['estado_adopcion'] ?? '') === $filtroEstado);
            return $okTipo && $okEstado;
        }));

        // Agrupar según vista
        $grupos = [];
        if ($tipoVista === 'porEstado') {
            foreach ($filtrada as $it) {
                $k = $it['estado_adopcion'] ?: 'Sin estado';
                $grupos[$k] = ($grupos[$k] ?? 0) + (int)$it['cantidad'];
            }
        } else { // combinado/porTipo => por tipo
            foreach ($filtrada as $it) {
                $k = $it['tipo_nombre'] ?: 'Sin tipo';
                $grupos[$k] = ($grupos[$k] ?? 0) + (int)$it['cantidad'];
            }
        }

        $labels = array_keys($grupos);
        $values = array_values($grupos);
        $total  = array_sum($values);

        $parts = [];
        if ($filtroTipo !== 'todos')   $parts[] = "Tipo: $filtroTipo";
        if ($filtroEstado !== 'todos') $parts[] = "Estado: $filtroEstado";
        $titulo = 'Distribución de mascotas' . ($parts ? ' ('.implode(' · ', $parts).')' : '');

        return [$labels, $values, $titulo, $total];
    }
}
?>