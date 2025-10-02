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
        // Leer filtros enviados (JSON)
        $raw = file_get_contents('php://input');
        $data = json_decode($raw, true);
        $filtros = $data['filtros'] ?? [];

        // Generar la imagen del gráfico en memoria (llama a método interno)
        $imageData = $this->generarImagenGrafico($filtros);
        if ($imageData === false) {
            http_response_code(500);
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['error' => 'No se pudo generar la imagen del gráfico (falta JPGraph o GD).']);
            exit;
        }

        $stats = $this->model->obtenerEstadisticas();

        $html = '<h2>Reporte de Estadísticas - Refugio</h2>';
        $html .= '<p>Generado: ' . date('Y-m-d H:i:s') . '</p>';
        $html .= '<div><img src="data:image/png;base64,' . base64_encode($imageData) . '" style="max-width:600px; width:100%; height:auto;"/></div>';

        // Agregar tabla resumen
        $html .= '<h3>Resumen</h3>';
        $html .= '<table border="1" cellpadding="6" cellspacing="0" style="border-collapse:collapse; width:100%;">'
              . '<tr><th>Etiqueta</th><th>Valor</th></tr>';
        $html .= '<tr><td>Total mascotas</td><td>' . ($stats['total_mascotas'] ?? 0) . '</td></tr>';
        $html .= '<tr><td>Mascotas disponibles</td><td>' . ($stats['mascotas_disponibles'] ?? 0) . '</td></tr>';
        $html .= '<tr><td>Mascotas adoptadas</td><td>' . ($stats['mascotas_adoptadas'] ?? 0) . '</td></tr>';
        $html .= '<tr><td>Total adoptantes</td><td>' . ($stats['total_adoptantes'] ?? 0) . '</td></tr>';
        $html .= '<tr><td>Adopciones recientes</td><td>' . ($stats['adopciones_recientes'] ?? 0) . '</td></tr>';
        $html .= '</table>';

        // Intentar usar Dompdf si existe
        if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
            require_once __DIR__ . '/../vendor/autoload.php';
        }

        if (class_exists('Dompdf\\Dompdf')) {
            $dompdf = new \Dompdf\Dompdf();
            $dompdf->loadHtml($html);
            $dompdf->setPaper('A4', 'portrait');
            $dompdf->render();
            $pdf = $dompdf->output();
            header('Content-Type: application/pdf');
            header('Content-Disposition: attachment; filename="estadisticas.pdf"');
            echo $pdf;
            exit;
        }

        // Si Dompdf no está disponible, devolver error claro para que el front muestre mensaje
        http_response_code(501);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['error' => 'Dompdf no está instalado en el servidor. Instala dompdf/dompdf vía composer.']);
        exit;
    }

    // Nuevo: Endpoint para exportar solo datos en PDF (sin gráfico)
    public function exportar_datos_pdf()
    {
        $stats = $this->model->obtenerEstadisticas();

        // Construir HTML de datos
        $html = '<style>
            @page { margin: 60px 40px; }
            header { position: fixed; top: -40px; left: 0; right: 0; height: 30px; }
            footer { position: fixed; bottom: -30px; left: 0; right: 0; height: 20px; font-size: 11px; color: #777; text-align:center; }
            table { width:100%; border-collapse: collapse; }
            th, td { border:1px solid #e5e5e5; padding:6px 8px; font-size:12px; }
            th { background:#f6f8fa; text-align:left; }
            h2 { color:#2a9d8f; }
        </style>';
        $html .= '<header><table style="border:none; width:100%"><tr><td style="border:none"><strong>Refugio Amigos Fieles</strong></td><td style="border:none; text-align:right">Generado: '.date('Y-m-d H:i').'</td></tr></table></header>';
        $html .= '<footer>Página <span class="pagenum"></span></footer>';
        $html .= '<h2>Reporte de Datos</h2>';
        $html .= '<table>';
        $html .= '<tr><th>Clave</th><th>Valor</th></tr>';
        $html .= '<tr><td>Total mascotas</td><td>' . ($stats['total_mascotas'] ?? 0) . '</td></tr>';
        $html .= '<tr><td>Mascotas disponibles</td><td>' . ($stats['mascotas_disponibles'] ?? 0) . '</td></tr>';
        $html .= '<tr><td>Mascotas adoptadas</td><td>' . ($stats['mascotas_adoptadas'] ?? 0) . '</td></tr>';
        $html .= '<tr><td>Total adoptantes</td><td>' . ($stats['total_adoptantes'] ?? 0) . '</td></tr>';
        $html .= '<tr><td>Adopciones recientes</td><td>' . ($stats['adopciones_recientes'] ?? 0) . '</td></tr>';
        $html .= '</table>';

        // Intentar usar Dompdf
        if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
            require_once __DIR__ . '/../vendor/autoload.php';
        }
        if (class_exists('Dompdf\\Dompdf')) {
            $dompdf = new \Dompdf\Dompdf();
            $dompdf->loadHtml($html);
            $dompdf->setPaper('A4', 'portrait');
            $dompdf->render();
            $pdf = $dompdf->output();
            header('Content-Type: application/pdf');
            header('Content-Disposition: attachment; filename="reporte_datos.pdf"');
            echo $pdf;
            exit;
        }

        http_response_code(501);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['error' => 'Dompdf no está instalado en el servidor. Instala dompdf/dompdf vía composer.']);
        exit;
    }

    // Endpoint para exportar Excel (simplemente HTML con headers para Excel)
    public function exportar_excel()
    {
        // leer filtros de query string (por ahora no aplican a CSV simple)
        $tipo = $_GET['tipo'] ?? 'todos';
        $estado = $_GET['estado'] ?? 'todos';

        $stats = $this->model->obtenerEstadisticas();

        // Exportar como CSV real para evitar la advertencia de Excel
        $filename = 'estadisticas_' . date('Ymd_His') . '.csv';
        header('Content-Type: text/csv; charset=UTF-8');
        header("Content-Disposition: attachment; filename={$filename}");
        // BOM para que Excel muestre bien tildes en Windows
        echo "\xEF\xBB\xBF";
        // Forzar separador reconocido por Excel
        echo "sep=,\n";

        // Encabezados
        $rows = [];
        $rows[] = ['Clave','Valor'];
        $rows[] = ['Total mascotas', (string)($stats['total_mascotas'] ?? 0)];
        $rows[] = ['Mascotas disponibles', (string)($stats['mascotas_disponibles'] ?? 0)];
        $rows[] = ['Mascotas adoptadas', (string)($stats['mascotas_adoptadas'] ?? 0)];
        $rows[] = ['Total adoptantes', (string)($stats['total_adoptantes'] ?? 0)];
        $rows[] = ['Adopciones recientes', (string)($stats['adopciones_recientes'] ?? 0)];

        // Salida CSV (comas, campos con comillas)
        foreach ($rows as $r) {
            $escaped = array_map(function($v){
                $v = (string)$v;
                $v = str_replace('"', '""', $v);
                return '"'.$v.'"';
            }, $r);
            echo implode(',', $escaped) . "\n";
        }
        exit;
    }

    // Reporte: Mascotas por Usuario (PDF)
    public function exportar_mascotas_usuario_pdf()
    {
        $idUsuario = isset($_GET['id_usuario']) ? (int)$_GET['id_usuario'] : 0;
        if ($idUsuario <= 0) { http_response_code(400); echo 'Falta id_usuario'; return; }
        $rows = $this->model->obtenerMascotasPorUsuario($idUsuario);

        $html = '<style>@page{margin:60px 40px} table{width:100%;border-collapse:collapse} th,td{border:1px solid #e5e5e5;padding:6px 8px;font-size:12px} th{background:#f6f8fa;text-align:left} h2{color:#2a9d8f}</style>';
        $html .= '<h2>Mascotas adoptadas por usuario</h2>';
        $html .= '<table><tr><th>Mascota</th><th>Tipo</th><th>Estado adopción</th></tr>';
        foreach ($rows as $r){
            $html .= '<tr><td>'.htmlspecialchars($r['mascota']).'</td><td>'.htmlspecialchars($r['tipo']).'</td><td>'.htmlspecialchars($r['estado_adopcion']).'</td></tr>';
        }
        $html .= '</table>';

        if (file_exists(__DIR__ . '/../vendor/autoload.php')) { require_once __DIR__ . '/../vendor/autoload.php'; }
        if (class_exists('Dompdf\\Dompdf')) {
            $dompdf = new \Dompdf\Dompdf();
            $dompdf->loadHtml($html); $dompdf->setPaper('A4', 'portrait'); $dompdf->render();
            header('Content-Type: application/pdf');
            header('Content-Disposition: attachment; filename="mascotas_usuario.pdf"');
            echo $dompdf->output(); return;
        }
        http_response_code(501); echo 'Falta Dompdf';
    }

    // Reporte: Mascotas por Usuario (CSV)
    public function exportar_mascotas_usuario_csv()
    {
        $idUsuario = isset($_GET['id_usuario']) ? (int)$_GET['id_usuario'] : 0;
        if ($idUsuario <= 0) { http_response_code(400); echo 'Falta id_usuario'; return; }
        $rows = $this->model->obtenerMascotasPorUsuario($idUsuario);
        header('Content-Type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment; filename=mascotas_usuario_'.date('Ymd_His').'.csv');
        echo "\xEF\xBB\xBF"; echo "sep=,\n";
        echo '"Mascota","Tipo","Estado adopción"' . "\n";
        foreach ($rows as $r){
            $line = [ $r['mascota'], $r['tipo'], $r['estado_adopcion'] ];
            $esc = array_map(function($v){ $v=str_replace('"','""',$v); return '"'.$v.'"';}, $line);
            echo implode(',', $esc) . "\n";
        }
    }

    // Reporte: Adopciones por rango (PDF)
    public function exportar_adopciones_pdf()
    {
        $desde = $_GET['fecha_desde'] ?? null; $hasta = $_GET['fecha_hasta'] ?? null;
        $rows = $this->model->obtenerAdopcionesDetalle($desde, $hasta);
        $html = '<style>@page{margin:60px 40px} table{width:100%;border-collapse:collapse} th,td{border:1px solid #e5e5e5;padding:6px 8px;font-size:12px} th{background:#f6f8fa;text-align:left} h2{color:#2a9d8f}</style>';
        $html .= '<h2>Adopciones</h2><p>Rango: '.htmlspecialchars($desde ?: '—').' a '.htmlspecialchars($hasta ?: '—').'</p>';
        $html .= '<table><tr><th>Fecha</th><th>Estado</th><th>Usuario</th><th>Email</th><th>Mascota</th><th>Tipo</th></tr>';
        foreach ($rows as $r){
            $html .= '<tr><td>'.htmlspecialchars($r['fecha_adopcion']).'</td><td>'.htmlspecialchars($r['estado']).'</td><td>'.htmlspecialchars($r['usuario']).'</td><td>'.htmlspecialchars($r['correo']).'</td><td>'.htmlspecialchars($r['mascota']).'</td><td>'.htmlspecialchars($r['tipo']).'</td></tr>';
        }
        $html .= '</table>';
        if (file_exists(__DIR__ . '/../vendor/autoload.php')) { require_once __DIR__ . '/../vendor/autoload.php'; }
        if (class_exists('Dompdf\\Dompdf')) {
            $dompdf = new \Dompdf\Dompdf(); $dompdf->loadHtml($html); $dompdf->setPaper('A4', 'landscape'); $dompdf->render();
            header('Content-Type: application/pdf'); header('Content-Disposition: attachment; filename="adopciones.pdf"'); echo $dompdf->output(); return;
        }
        http_response_code(501); echo 'Falta Dompdf';
    }

    // Reporte: Adopciones por rango (CSV)
    public function exportar_adopciones_csv()
    {
        $desde = $_GET['fecha_desde'] ?? null; $hasta = $_GET['fecha_hasta'] ?? null;
        $rows = $this->model->obtenerAdopcionesDetalle($desde, $hasta);
        header('Content-Type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment; filename=adopciones_'.date('Ymd_His').'.csv');
        echo "\xEF\xBB\xBF"; echo "sep=,\n";
        echo '"Fecha","Estado","Usuario","Email","Mascota","Tipo"' . "\n";
        foreach ($rows as $r){
            $line = [ $r['fecha_adopcion'], $r['estado'], $r['usuario'], $r['correo'], $r['mascota'], $r['tipo'] ];
            $esc = array_map(function($v){ $v=str_replace('"','""',$v); return '"'.$v.'"';}, $line);
            echo implode(',', $esc) . "\n";
        }
    }

    // Genera y retorna PNG binario del gráfico según filtros
    private function generarImagenGrafico($filtros = [])
    {
        // Obtener datos agregados por tipo desde el modelo
        $stats = $this->model->obtenerEstadisticas();

        // Construir un arreglo simple de ejemplo: usar distribución por tipo desde el modelo
        $distribucion = $stats['distribucion'] ?? [];
        $map = [];
        foreach ($distribucion as $row) {
            $tipo = $row['tipo_nombre'] ?? 'Otro';
            $cant = (int)($row['cantidad'] ?? 0);
            if (!isset($map[$tipo])) $map[$tipo] = 0;
            $map[$tipo] += $cant;
        }

        // Si no hay datos, retornar false
        if (empty($map)) return false;

        // Intentar JPGraph si está instalado
        $jpgraphAutoload = __DIR__ . '/../vendor/jpgraph/src/jpgraph.php';
        $jpgraphDir = __DIR__ . '/../vendor/jpgraph/src';
        if (file_exists($jpgraphAutoload) || class_exists('Graph')) {
            try {
                // intentar cargar JPGraph desde vendor o desde include_path
                if (file_exists($jpgraphAutoload)) require_once $jpgraphAutoload;
                else {
                    // intentar cargar por ruta relativa si se encuentra instalado fuera
                    @require_once 'jpgraph/jpgraph.php';
                }

                // incluir librerías necesarias para pie
                if (!class_exists('PieChart')) @require_once $jpgraphDir . '/jpgraph_pie.php';
                if (!class_exists('PiePlot')) @require_once $jpgraphDir . '/jpgraph_pie.php';

                // Preparar datos
                $labels = array_keys($map);
                $values = array_values($map);

                $graph = new Graph(700,400,"auto");
                $graph->SetScale('lin');
                $graph->img->SetMargin(40,30,20,80);
                $graph->title->Set('Distribución por Tipo');

                $p1 = new PiePlot($values);
                $p1->SetLegends($labels);
                $p1->ExplodeSlice(0);
                $graph->Add($p1);

                // Capturar imagen en buffer
                ob_start();
                $graph->Stroke(_IMG_HANDLER);
                $gdImg = $graph->img->img;
                imagepng($gdImg);
                $png = ob_get_clean();
                return $png;
            } catch (Exception $e) {
                // si falla jpgraph, caemos a GD
            }
        }

        // Fallback simple con GD: generar un pie chart básico
        if (!function_exists('imagecreatetruecolor')) return false;

        $width = 700; $height = 400;
        $im = imagecreatetruecolor($width,$height);
        $white = imagecolorallocate($im,255,255,255);
        imagefill($im,0,0,$white);

        // Colores predefinidos
        $palette = [
            imagecolorallocate($im,54,162,235),
            imagecolorallocate($im,255,99,132),
            imagecolorallocate($im,255,205,86),
            imagecolorallocate($im,75,192,192),
            imagecolorallocate($im,153,102,255),
            imagecolorallocate($im,255,159,64),
        ];

        $total = array_sum($map);
        $cx = 250; $cy = 200; $r = 150;
        $start = 0;
        $i = 0;
        foreach ($map as $label => $val) {
            $angle = ($val / $total) * 360;
            $color = $palette[$i % count($palette)];
            imagefilledarc($im, $cx, $cy, $r*2, $r*2, $start, $start + $angle, $color, IMG_ARC_PIE);
            $start += $angle;
            $i++;
        }

        // Leyenda
        $x = 480; $y = 40; $i = 0;
        $fontColor = imagecolorallocate($im,0,0,0);
        foreach ($map as $label => $val) {
            $color = $palette[$i % count($palette)];
            imagefilledrectangle($im, $x, $y+$i*28, $x+20, $y+16+$i*28, $color);
            imagestring($im, 3, $x+28, $y-2+$i*28, $label . ' (' . $val . ')', $fontColor);
            $i++;
        }

        ob_start();
        imagepng($im);
        $png = ob_get_clean();
        imagedestroy($im);
        return $png;
    }
}
?>