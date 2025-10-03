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

        // Hoja 1: Resumen con fórmulas
        $rowsResumen = [
            ['Reporte','Distribución de mascotas'],
            ['Generado', date('Y-m-d H:i')],
            ['Filtros', ($filtroTipo==='todos'?'Todos los tipos':"Tipo: $filtroTipo").' · '.($filtroEstado==='todos'?'Todos los estados':"Estado: $filtroEstado").' · Vista: '.$tipoVista],
            [],
            ['Segmento','Cantidad','% del total']
        ];
        $filaInicio = count($rowsResumen) + 1; // 1-based
        foreach ($labels as $i=>$label) {
            // PCT formula uses row index
            $rowIdx = $filaInicio + $i;
            $rowsResumen[] = [
                $label,
                (int)$values[$i],
                $total > 0 ? '=RC[-1]/R'.($filaInicio+count($labels)).'C[-1]' : 0
            ];
        }
        $rowsResumen[] = ['Total', $total, ''];

        // Hoja 2: Datos crudos
        $rowsData = [['Etiqueta','Valor']];
        foreach ($labels as $i=>$l) { $rowsData[] = [$l, (int)$values[$i]]; }

        $this->renderExcelXml('reporte_distribucion_'.date('Ymd_His').'.xls', [
            ['name' => 'Resumen', 'rows' => $rowsResumen],
            ['name' => 'Datos',    'rows' => $rowsData],
        ]);
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

    // No se usa salida bufferizada; el enrutador despacha estas rutas antes del layout

    // Imagen: Embudo del proceso (JPGraph)
    public function grafico_funnel()
    {
        $anio = isset($_GET['anio']) ? (int)$_GET['anio'] : null;
        $mes  = isset($_GET['mes'])  ? (int)$_GET['mes']  : null;
        [$labels, $values] = $this->datosEmbudo($anio, $mes);
        $png = $this->generarImagenFunnel($labels, $values);
        if ($png === false) { http_response_code(500); echo 'No se pudo generar el gráfico.'; return; }
        header('Content-Type: image/png');
        header('Content-Length: '.strlen($png));
        header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
        echo $png;
        exit;
    }

    // Imagen: Columnas agrupadas Aprobadas vs Rechazadas por mes (JPGraph)
    public function grafico_apr_rech_mensual()
    {
        $anio = isset($_GET['anio']) ? (int)$_GET['anio'] : (int)date('Y');
        [$mesLabels, $serieAprob, $serieRech, $titulo] = $this->datosAprobRechMes($anio);
        $png = $this->generarImagenColsAprobRech($mesLabels, $serieAprob, $serieRech, $titulo);
        if ($png === false) { http_response_code(500); echo 'No se pudo generar el gráfico.'; return; }
        header('Content-Type: image/png');
        header('Content-Length: '.strlen($png));
        header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
        echo $png;
        exit;
    }

    // Exportación PDF: Embudo
    public function exportar_pdf_funnel()
    {
        $anio = isset($_GET['anio']) ? (int)$_GET['anio'] : null;
        $mes  = isset($_GET['mes'])  ? (int)$_GET['mes']  : null;
        [$labels, $values] = $this->datosEmbudo($anio, $mes);
        $total = array_sum($values);
        $png = $this->generarImagenFunnel($labels, $values);
        if ($png === false) { http_response_code(500); echo 'No se pudo generar el gráfico.'; return; }

        $rows = '';
        foreach ($labels as $i=>$l) {
            $v = (int)$values[$i]; $pct = $total ? round($v*100/$total,1):0;
            $rows .= '<tr><td>'.htmlspecialchars($l).'</td><td style="text-align:right">'.number_format($v).'</td><td style="text-align:right">'.$pct.'%</td></tr>';
        }
        $imgDataUri = 'data:image/png;base64,'.base64_encode($png);
        $periodoTxt = '';
        if ($anio) { $periodoTxt = 'Año ' . $anio . ($mes ? (' · Mes ' . $mes) : ''); }
        $html = '<html><head><meta charset="utf-8"><style>
        body{font-family:Arial,Helvetica,sans-serif;font-size:12px;margin:22px;color:#222}
        h1{font-size:18px;margin:0 0 6px} .muted{color:#666;margin-bottom:12px}
        table{width:100%;border-collapse:collapse;margin-top:10px} th,td{border:1px solid #e5e5e5;padding:6px 8px} th{background:#f6f8fa}
        .imgbox{border:1px solid #eee;padding:6px;border-radius:6px}
        </style></head><body>
        <h1>Embudo del Proceso de Adopción</h1>
        <div class="muted">'.($periodoTxt ? ($periodoTxt.' · ') : '').'Generado: '.date('Y-m-d H:i').'</div>
        <div class="imgbox"><img src="'.$imgDataUri.'" style="width:100%;max-width:760px;height:auto"/></div>
        <h2 style="font-size:16px;margin-top:14px">Resumen</h2>
        <table><thead><tr><th>Estado</th><th style="text-align:right">Cantidad</th><th style="text-align:right">% del total</th></tr></thead>
        <tbody>'.$rows.'<tr><th>Total</th><th style="text-align:right">'.number_format($total).'</th><th></th></tr></tbody></table>
        </body></html>';

        $suf = $anio ? ('_'.$anio.($mes ? '_'.$mes : '')) : '';
        $this->renderPdfOrHtml($html, 'embudo_adopcion'.$suf.'.pdf');
    }

    // Exportación PDF: Aprobadas vs Rechazadas por mes
    public function exportar_pdf_apr_rech()
    {
        $anio = isset($_GET['anio']) ? (int)$_GET['anio'] : (int)date('Y');
        [$mesLabels, $serieAprob, $serieRech, $titulo] = $this->datosAprobRechMes($anio);
        $png = $this->generarImagenColsAprobRech($mesLabels, $serieAprob, $serieRech, $titulo);
        if ($png === false) { http_response_code(500); echo 'No se pudo generar el gráfico.'; return; }

        $imgDataUri = 'data:image/png;base64,'.base64_encode($png);

        // Tabla de datos
        $rows = '';
        $totalA = array_sum($serieAprob);
        $totalR = array_sum($serieRech);
        foreach ($mesLabels as $i=>$m) {
            $a = $serieAprob[$i]; $r = $serieRech[$i];
            $rows .= '<tr><td>'.$m.'</td><td style="text-align:right">'.number_format($a).'</td><td style="text-align:right">'.number_format($r).'</td></tr>';
        }

        $html = '<html><head><meta charset="utf-8"><style>
        body{font-family:Arial,Helvetica,sans-serif;font-size:12px;margin:22px;color:#222}
        h1{font-size:18px;margin:0 0 6px} .muted{color:#666;margin-bottom:12px}
        table{width:100%;border-collapse:collapse;margin-top:10px} th,td{border:1px solid #e5e5e5;padding:6px 8px} th{background:#f6f8fa}
        .imgbox{border:1px solid #eee;padding:6px;border-radius:6px}
        </style></head><body>
        <h1>Aprobadas vs Rechazadas por Mes</h1>
        <div class="muted">'.$titulo.' · Generado: '.date('Y-m-d H:i').'</div>
        <div class="imgbox"><img src="'.$imgDataUri.'" style="width:100%;max-width:900px;height:auto"/></div>
        <h2 style="font-size:16px;margin-top:14px">Resumen</h2>
        <table><thead><tr><th>Mes</th><th style="text-align:right">Aprobadas</th><th style="text-align:right">Rechazadas</th></tr></thead>
        <tbody>'.$rows.'<tr><th>Total</th><th style="text-align:right">'.number_format($totalA).'</th><th style="text-align:right">'.number_format($totalR).'</th></tr></tbody></table>
        </body></html>';

        $this->renderPdfOrHtml($html, 'aprobadas_vs_rechazadas_'.$anio.'.pdf');
    }

    // Helpers de datos
    private function datosEmbudo(?int $anio = null, ?int $mes = null): array
    {
        $map = $this->model->obtenerConteoAdopcionesPorEstado($anio, $mes);
        $labels = ['Pendiente','Aprobada','Rechazada','Finalizada'];
        $values = array_map(fn($k)=> (int)($map[$k] ?? 0), $labels);
        return [$labels, $values];
    }

    private function datosAprobRechMes(int $anio): array
    {
        $data = $this->model->obtenerAprobadasRechazadasPorMes($anio);
        $mesLabels = ['Ene','Feb','Mar','Abr','May','Jun','Jul','Ago','Sep','Oct','Nov','Dic'];
        // Reindexar a 0..11
        $serieAprob = array_values($data['aprobadas']);
        $serieRech  = array_values($data['rechazadas']);
        $titulo = 'Año '.$anio;
        return [$mesLabels, $serieAprob, $serieRech, $titulo];
    }

    // Helpers de imagen JPGraph
    private function cargarJpGraph(): bool
    {
        $base = __DIR__ . '/../vendor/jpgraph/src';
        if (file_exists($base.'/jpgraph.php')) {
            require_once $base.'/jpgraph.php';
            return true;
        }
        // Intento alternativo (si estuviera global)
        if (@include_once 'jpgraph/jpgraph.php') return true;
        return class_exists('Graph');
    }

    private function generarImagenFunnel(array $labels, array $values)
    {
        $total = array_sum($values);
        if ($total <= 0) return false;
        if (!$this->cargarJpGraph()) return $this->fallbackFunnelPng($labels, $values);

        // Preferir funnel nativo si existe
        $base = __DIR__ . '/../vendor/jpgraph/src';
        if (file_exists($base.'/jpgraph_funnel.php')) {
            require_once $base.'/jpgraph_funnel.php';
        }
        $hasFunnel = class_exists('FunnelPlot');

        if ($hasFunnel) {
            $g = new \Graph(760, 420, 'auto');
            $g->SetScale('textlin');
            $g->title->Set('Embudo del Proceso de Adopción');

            $fp = new \FunnelPlot($values);
            $fp->SetCenter(0.45, 0.5);
            $fp->SetLegends($labels);
            $fp->SetColor('white');
            $fp->SetFillColor('orange');
            $g->Add($fp);

            ob_start(); $g->Stroke(); return ob_get_clean();
        } else {
            // Horizontal bars simulando embudo
            require_once $base.'/jpgraph_bar.php';
            $g = new \Graph(760, 420, 'auto');
            $g->SetScale('textlin');
            $g->SetMargin(120, 40, 40, 60);
            $g->Set90AndMargin(140, 30, 40, 60);
            $g->title->Set('Embudo del Proceso de Adopción');
            $g->xaxis->SetTickLabels($labels);
            $g->xaxis->SetLabelAlign('right','center','right');
            $g->yaxis->HideZeroLabel();

            $b = new \BarPlot($values);
            $b->SetFillGradient('#5DADE2', '#2E86C1', GRAD_HOR);
            $b->SetColor('#1F618D');
            $b->value->Show();
            $b->value->SetFormat('%d');
            $b->SetWidth(0.8);

            $g->Add($b);
            ob_start(); $g->Stroke(); return ob_get_clean();
        }
    }

    private function generarImagenColsAprobRech(array $mesLabels, array $serieA, array $serieR, string $titulo)
    {
        $sum = array_sum($serieA) + array_sum($serieR);
        // Si no hay datos, devolver una imagen simple informativa en lugar de fallar
        if ($sum <= 0) {
            return $this->pngNoData('Sin datos para '.$titulo);
        }
        if (!$this->cargarJpGraph()) return $this->fallbackColsPng($mesLabels, $serieA, $serieR, $titulo);

        $base = __DIR__ . '/../vendor/jpgraph/src';
        require_once $base.'/jpgraph_bar.php';

        $g = new \Graph(920, 460, 'auto');
        $g->SetScale('textlin');
        $g->SetMargin(60, 30, 40, 80);
        $g->title->Set('Aprobadas vs Rechazadas por Mes — '.$titulo);
        $g->xaxis->SetTickLabels($mesLabels);

        $bA = new \BarPlot($serieA);
        $bA->SetLegend('Aprobadas');
        $bA->SetFillColor('#28a745');
        $bA->SetColor('#1e7e34');

        $bR = new \BarPlot($serieR);
        $bR->SetLegend('Rechazadas');
        $bR->SetFillColor('#dc3545');
        $bR->SetColor('#a71d2a');

        $gb = new \GroupBarPlot([$bA, $bR]);
        $g->Add($gb);
        $g->legend->SetPos(0.5,0.98,'center','bottom');

        ob_start(); $g->Stroke(); return ob_get_clean();
    }

    private function pngNoData(string $texto, int $w = 920, int $h = 460)
    {
        if (!function_exists('imagecreatetruecolor')) return false;
        $im = imagecreatetruecolor($w, $h);
        $white = imagecolorallocate($im, 255,255,255);
        $gray  = imagecolorallocate($im, 120,120,120);
        imagefill($im, 0, 0, $white);
        // Marco suave
        $light = imagecolorallocate($im, 230,230,230);
        imagerectangle($im, 10, 10, $w-10, $h-10, $light);
        // Texto centrado aproximado
        $msg1 = 'Aprobadas vs Rechazadas por Mes';
        $msg2 = $texto;
        imagestring($im, 5, (int)($w/2 - strlen($msg1)*4), (int)($h/2 - 12), $msg1, $gray);
        imagestring($im, 5, (int)($w/2 - strlen($msg2)*4), (int)($h/2 + 8),  $msg2, $gray);
        ob_start(); imagepng($im); $png = ob_get_clean(); imagedestroy($im); return $png;
    }

    private function fallbackFunnelPng(array $labels, array $values)
    {
        // GD simple horizontal bars
        if (!function_exists('imagecreatetruecolor')) return false;
        $w = 760; $h = 420; $left = 220; $right = 40; $top = 40; $rowH = 60;
        $im = imagecreatetruecolor($w,$h);
        $white = imagecolorallocate($im,255,255,255);
        $black = imagecolorallocate($im,30,30,30);
        $green = imagecolorallocate($im,46,134,193);
        imagefill($im,0,0,$white);
        imagestring($im,5,10,10,'Embudo del Proceso de Adopción',$black);

        $max = max(1, max($values));
        foreach ($values as $i=>$v) {
            $y = $top + 30 + $i*$rowH;
            $len = (int)(($w-$left-$right) * ($v/$max));
            imagefilledrectangle($im, $left, $y, $left+$len, $y+24, $green);
            imagestring($im,4,10,$y, $labels[$i], $black);
            imagestring($im,4,$left+$len+6,$y, (string)$v, $black);
        }
        ob_start(); imagepng($im); $png = ob_get_clean(); imagedestroy($im); return $png;
    }

    private function fallbackColsPng(array $mesLabels, array $serieA, array $serieR, string $titulo)
    {
        if (!function_exists('imagecreatetruecolor')) return false;
        $w=920;$h=460;$im=imagecreatetruecolor($w,$h);
        $white=imagecolorallocate($im,255,255,255);
        $black=imagecolorallocate($im,30,30,30);
        $green=imagecolorallocate($im,40,167,69);
        $red=imagecolorallocate($im,220,53,69);
        imagefill($im,0,0,$white);
        imagestring($im,5,10,10,'Aprobadas vs Rechazadas — '.$titulo,$black);

        $left=60;$bottom=$h-60;$top=50;$right=$w-30;
        $cols = count($mesLabels); $groupW = ($right-$left)/$cols;
        $barW = max(6, (int)($groupW/3));
        $max = max(1, max($serieA)+max($serieR)); // simple max

        // eje x labels
        foreach ($mesLabels as $i=>$m) {
            $x = (int)($left + $i*$groupW + $groupW/2);
            imagestring($im,3,$x-10,$bottom+6,$m,$black);
        }
        // barras
        foreach ($mesLabels as $i=>$_) {
            $x0 = (int)($left + $i*$groupW + $groupW/2);
            $a = $serieA[$i]; $r = $serieR[$i];
            $ha = (int)(($a/$max)*($bottom-$top));
            $hr = (int)(($r/$max)*($bottom-$top));
            imagefilledrectangle($im, $x0-$barW-2, $bottom-$ha, $x0-2, $bottom, $green);
            imagefilledrectangle($im, $x0+2,            $bottom-$hr, $x0+$barW+2, $bottom, $red);
        }
        ob_start(); imagepng($im); $png=ob_get_clean(); imagedestroy($im); return $png;
    }

    private function renderPdfOrHtml(string $html, string $filename)
    {
        $autoload = __DIR__ . '/../vendor/autoload.php';
        if (file_exists($autoload)) require_once $autoload;

        if (class_exists('Dompdf\\Dompdf')) {
            $dompdf = new \Dompdf\Dompdf();
            $dompdf->loadHtml($html,'UTF-8');
            $dompdf->setPaper('A4','portrait');
            $dompdf->render();

            // Descargar como archivo (como ya lo haces)
            $dompdf->stream($filename, ['Attachment' => true]);
            exit;
        }

        header('Content-Type: text/html; charset=utf-8');
        echo $html;
        exit;
    }

    // ===== Excel (SpreadsheetML) helper =====
    private function renderExcelXml(string $filename, array $sheets)
    {
        $xml  = "<?xml version=\"1.0\"?>\n";
        $xml .= "<?mso-application progid=\"Excel.Sheet\"?>\n";
        $xml .= "<Workbook xmlns=\"urn:schemas-microsoft-com:office:spreadsheet\" xmlns:o=\"urn:schemas-microsoft-com:office:office\" xmlns:x=\"urn:schemas-microsoft-com:office:excel\" xmlns:ss=\"urn:schemas-microsoft-com:office:spreadsheet\" xmlns:html=\"http://www.w3.org/TR/REC-html40\">";
        $xml .= "<Styles>"
              .   "<Style ss:ID=\"sHeader\"><Font ss:Bold=\"1\"/><Interior ss:Color=\"#F6F8FA\" ss:Pattern=\"Solid\"/></Style>"
              .   "<Style ss:ID=\"sRight\"><Alignment ss:Horizontal=\"Right\"/></Style>"
              .   "<Style ss:ID=\"sPercent\"><NumberFormat ss:Format=\"0.00%\"/></Style>"
              . "</Styles>";
        foreach ($sheets as $sheet) {
            $name = $this->excelSanitizeSheetName($sheet['name'] ?? 'Hoja');
            $rows = $sheet['rows'] ?? [];
            $xml .= "<Worksheet ss:Name=\"".htmlspecialchars($name)."\"><Table>";
            foreach ($rows as $row) {
                $xml .= "<Row>";
                foreach ($row as $cell) {
                    $isFormula = is_string($cell) && strlen($cell) > 0 && $cell[0] === '=';
                    $type = $isFormula || is_numeric($cell) ? 'Number' : 'String';
                    $attr = $isFormula ? " ss:Formula=\"".htmlspecialchars($cell)."\"" : '';
                    $style = '';
                    if ($isFormula && (stripos($cell,'/R')!==false || stripos($cell,'%')!==false)) { $style = " ss:StyleID=\"sPercent\""; }
                    $data = $isFormula ? 0 : htmlspecialchars((string)$cell);
                    $xml .= "<Cell$attr$style><Data ss:Type=\"$type\">$data</Data></Cell>";
                }
                $xml .= "</Row>";
            }
            $xml .= "</Table></Worksheet>";
        }
        $xml .= "</Workbook>";
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment; filename="'.$filename.'"');
        echo $xml;
        exit;
    }

    private function excelSanitizeSheetName(string $name): string
    {
        $name = preg_replace('/[\\\\:\\/\\?\\*\\[\\]]/', ' ', $name);
        $name = trim($name);
        if ($name === '') $name = 'Hoja';
        return substr($name, 0, 31);
    }

    // ===== Datos-only endpoints =====
    public function exportar_datos_funnel_pdf()
    {
        $anio = isset($_GET['anio']) ? (int)$_GET['anio'] : null;
        $mes  = isset($_GET['mes'])  ? (int)$_GET['mes']  : null;
        [$labels, $values] = $this->datosEmbudo($anio, $mes);
        $total = array_sum($values);
        $rows = '';
        if ($total > 0) {
            foreach ($labels as $i=>$l) {
                $v = (int)$values[$i]; $pct = round($v * 100 / $total, 2);
                $rows .= '<tr><td>'.htmlspecialchars($l).'</td><td style="text-align:right">'.number_format($v).'</td><td style="text-align:right">'.$pct.'%</td></tr>';
            }
            $rows .= '<tr><th>Total</th><th style="text-align:right">'.number_format($total).'</th><th></th></tr>';
        } else {
            $rows = '<tr><td colspan="3">Sin datos.</td></tr>';
        }
      $periodoTxt = '';
      if ($anio) { $periodoTxt = 'Año ' . $anio . ($mes ? (' · Mes ' . $mes) : ''); }
      $html = '<html><head><meta charset="utf-8"><style>body{font-family:Arial,Helvetica,sans-serif;font-size:12px;color:#222;margin:22px}h1{font-size:18px;margin:0 0 6px}.muted{color:#666;margin-bottom:12px}table{width:100%;border-collapse:collapse}th,td{border:1px solid #e5e5e5;padding:6px 8px}th{background:#f6f8fa}</style></head><body>'
          . '<h1>Reporte de datos — Embudo</h1><div class="muted">'.($periodoTxt ? ($periodoTxt.' · ') : '').'Generado: '.date('Y-m-d H:i').'</div>'
              . '<table><thead><tr><th>Estado</th><th style="text-align:right">Cantidad</th><th style="text-align:right">% del total</th></tr></thead><tbody>'
              . $rows . '</tbody></table></body></html>';
      $suf = $anio ? ('_'.$anio.($mes ? '_'.$mes : '')) : '';
      $this->renderPdfOrHtml($html, 'datos_embudo'.$suf.'.pdf');
    }

    public function exportar_datos_funnel_excel()
    {
        $anio = isset($_GET['anio']) ? (int)$_GET['anio'] : null;
        $mes  = isset($_GET['mes'])  ? (int)$_GET['mes']  : null;
        [$labels, $values] = $this->datosEmbudo($anio, $mes);
        $total = array_sum($values);
        $rows = [['Estado','Cantidad','% del total']];
        foreach ($labels as $i=>$l) {
            $v = (int)$values[$i];
            $rows[] = [$l, $v, $total>0 ? ($v/$total) : 0];
        }
        $rows[] = ['Total', $total, ''];
        $suf = $anio ? ('_'.$anio.($mes ? '_'.$mes : '')) : '';
        $this->renderExcelXml('datos_embudo'.$suf.'.xls', [ ['name'=>'Embudo','rows'=>$rows], ['name'=>'Filtros','rows'=>[['Año',$anio?:'Todos'],['Mes',$mes?:'Todos']]] ]);
    }

    public function exportar_datos_apr_rech_pdf()
    {
        $anio = isset($_GET['anio']) ? (int)$_GET['anio'] : (int)date('Y');
        [$mesLabels, $serieA, $serieR, $titulo] = $this->datosAprobRechMes($anio);
        $rows = '';
        for ($i=0; $i<count($mesLabels); $i++) {
            $a = (int)$serieA[$i]; $r = (int)$serieR[$i]; $t = $a + $r;
            $pa = $t ? round($a*100/$t,2) : 0; $pr = $t ? round($r*100/$t,2) : 0;
            $rows .= '<tr><td>'.$mesLabels[$i].'</td><td style="text-align:right">'.number_format($a).'</td><td style="text-align:right">'.number_format($r).'</td><td style="text-align:right">'.$pa.'%</td><td style="text-align:right">'.$pr.'%</td></tr>';
        }
        $html = '<html><head><meta charset="utf-8"><style>body{font-family:Arial,Helvetica,sans-serif;font-size:12px;color:#222;margin:22px}h1{font-size:18px;margin:0 0 6px}.muted{color:#666;margin-bottom:12px}table{width:100%;border-collapse:collapse}th,td{border:1px solid #e5e5e5;padding:6px 8px}th{background:#f6f8fa}</style></head><body>'
              . '<h1>Datos — Aprobadas vs Rechazadas por mes</h1><div class="muted">'.$titulo.' · Generado: '.date('Y-m-d H:i').'</div>'
              . '<table><thead><tr><th>Mes</th><th style="text-align:right">Aprobadas</th><th style="text-align:right">Rechazadas</th><th style="text-align:right">Tasa Aprob.</th><th style="text-align:right">Tasa Rech.</th></tr></thead><tbody>'
              . $rows . '</tbody></table></body></html>';
        $this->renderPdfOrHtml($html, 'datos_aprobadas_rechazadas_'.$anio.'.pdf');
    }

    public function exportar_datos_apr_rech_excel()
    {
        $anio = isset($_GET['anio']) ? (int)$_GET['anio'] : (int)date('Y');
        [$mesLabels, $serieA, $serieR, $titulo] = $this->datosAprobRechMes($anio);
        $rows = [['Mes','Aprobadas','Rechazadas','Total','Tasa Aprob.','Tasa Rech.']];
        for ($i=0; $i<count($mesLabels); $i++) {
            $a = (int)$serieA[$i]; $r = (int)$serieR[$i]; $t = $a + $r;
            $rows[] = [$mesLabels[$i], $a, $r, $t, $t?($a/$t):0, $t?($r/$t):0];
        }
        $resumen = [ ['Título'], [$titulo] ];
        $this->renderExcelXml('datos_aprobadas_rechazadas_'.$anio.'.xls', [
            ['name'=>'Mensual','rows'=>$rows],
            ['name'=>'Resumen','rows'=>$resumen],
        ]);
    }
}
?>