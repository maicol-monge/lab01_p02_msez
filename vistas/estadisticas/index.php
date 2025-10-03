<div class="container py-4">
    <style>
        /* Mantener tamaño estable del gráfico */
        .chart-wrap {
            position: relative;
            height: 340px; /* altura fija del contenedor */
        }
        #graficoDistribucion {
            width: 100% !important;
            height: 100% !important;
        }
    </style>
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="h2 mb-4 text-center">
                <i class="fas fa-chart-pie text-primary me-2"></i>
                Estadísticas del Refugio
            </h1>
            <p class="lead text-center text-muted">
                Un vistazo a nuestro impacto en la vida de las mascotas y sus familias adoptivas.
            </p>
        </div>
    </div>

    <!-- Tarjetas de Estadísticas Principales -->
    <div class="row g-4 mb-5">
        <div class="col-md-6 col-lg-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="display-4 text-primary mb-2">
                        <i class="fas fa-house-chimney"></i>
                    </div>
                    <h5 class="card-title"><?= number_format($stats['mascotas_disponibles']); ?></h5>
                    <p class="card-text text-muted">Mascotas Disponibles</p>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-lg-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="display-4 text-success mb-2">
                        <i class="fas fa-heart"></i>
                    </div>
                    <h5 class="card-title"><?= number_format($stats['mascotas_adoptadas']); ?></h5>
                    <p class="card-text text-muted">Adopciones Exitosas</p>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-lg-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="display-4 text-info mb-2">
                        <i class="fas fa-users"></i>
                    </div>
                    <h5 class="card-title"><?= number_format($stats['total_adoptantes']); ?></h5>
                    <p class="card-text text-muted">Familias Adoptantes</p>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-lg-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="display-4 text-warning mb-2">
                        <i class="fas fa-handshake"></i>
                    </div>
                    <h5 class="card-title"><?= number_format($stats['adopciones_recientes']); ?></h5>
                    <p class="card-text text-muted">Adopciones Activas</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Reportes (Datos) -->
    <!-- Sección eliminada por solicitud -->

    <!-- Distribución de Mascotas -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h4 class="card-title mb-4">
                        <i class="fas fa-chart-pie text-primary me-2"></i>
                        Distribución de Mascotas
                    </h4>

                    <!-- Filtros -->
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <select id="filtroTipo" class="form-select">
                                <option value="todos">Todos los tipos</option>
                                <?php
                                $tipos_unicos = array_unique(array_column($stats['distribucion'], 'tipo_nombre'));
                                foreach ($tipos_unicos as $tipo): ?>
                                    <option value="<?= htmlspecialchars($tipo); ?>">
                                        <?= htmlspecialchars($tipo); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <select id="filtroEstado" class="form-select">
                                <option value="todos">Todos los estados</option>
                                <option value="Disponible">Disponibles</option>
                                <option value="Adoptado">Adoptados</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <select id="tipoVista" class="form-select">
                                <option value="combinado">Vista Combinada</option>
                                <option value="porTipo">Por Tipo</option>
                                <option value="porEstado">Por Estado</option>
                            </select>
                        </div>
                    </div>

                    <!-- Nota aclaratoria bajo los filtros de Distribución -->
                    <p class="text-muted small mb-2">Nota: este gráfico no usa rango de fechas; refleja únicamente los filtros seleccionados arriba.</p>

                    <div class="table-responsive">
                        <table class="table" id="tablaDistribucion">
                            <thead class="table-light">
                                <tr>
                                    <th>Tipo</th>
                                    <th>Estado</th>
                                    <th class="text-end">Cantidad</th>
                                    <th>Distribución</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                foreach ($stats['distribucion'] as $item):
                                    if (!$item['estado_adopcion'])
                                        continue; // Saltar si no hay estado de adopción
                                    ?>
                                    <tr data-tipo="<?= htmlspecialchars($item['tipo_nombre']); ?>"
                                        data-estado="<?= htmlspecialchars($item['estado_adopcion']); ?>">
                                        <td>
                                            <i class="fas fa-paw text-primary me-2"></i>
                                            <?= htmlspecialchars($item['tipo_nombre']); ?>
                                        </td>
                                        <td>
                                            <span
                                                class="badge <?= $item['estado_adopcion'] === 'Disponible' ? 'bg-success' : 'bg-warning text-dark'; ?>">
                                                <?= $item['estado_adopcion']; ?>
                                            </span>
                                        </td>
                                        <td class="text-end"><?= number_format($item['cantidad']); ?></td>
                                        <td style="width: 40%;">
                                            <div class="progress" style="height: 20px;">
                                                <div class="progress-bar <?= $item['estado_adopcion'] === 'Disponible' ? 'bg-success' : 'bg-warning'; ?>"
                                                    role="progressbar" style="width: 0%">
                                                    0%
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Gráfico -->
                    <div class="row mt-4">
                        <div class="col-md-8">
                            <div class="card">
                                <div class="card-body">
                                    <div class="chart-wrap">
                                        <canvas id="graficoDistribucion"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div id="chartAlert" class="alert alert-warning d-none" role="alert"></div>
                            <div class="d-grid gap-2">
                                <button id="btnExportPDF" class="btn btn-danger">Gráfico: PDF</button>
                                <!-- NUEVOS BOTONES -->
                                <button id="btnExportDatosPDF" class="btn btn-outline-danger">Reporte: PDF (datos)</button>
                                <button id="btnExportExcel" class="btn btn-success">Reporte: Excel (datos)</button>
                            </div>
                        </div>
                    </div>

                    <script>
                        document.addEventListener('DOMContentLoaded', function () {
                            const filtroTipo = document.getElementById('filtroTipo');
                            const filtroEstado = document.getElementById('filtroEstado');
                            const tipoVista = document.getElementById('tipoVista');
                            const tabla = document.getElementById('tablaDistribucion');
                            const filas = tabla.querySelectorAll('tbody tr');
                            const chartAlert = document.getElementById('chartAlert');

                            let totalVisibleActual = 0;
                            function showChartMsg(msg, type='warning', timeout=3500){
                                chartAlert.className = 'alert alert-'+type;
                                chartAlert.textContent = msg;
                                chartAlert.classList.remove('d-none');
                                if (timeout){ setTimeout(()=> chartAlert.classList.add('d-none'), timeout); }
                            }
                            function clearChartMsg(){ chartAlert.classList.add('d-none'); }

                            function actualizarTabla() {
                                const tipoSeleccionado = filtroTipo.value;
                                const estadoSeleccionado = filtroEstado.value;

                                let totalVisible = 0;
                                let filasVisibles = [];

                                filas.forEach(fila => {
                                    const tipo = fila.dataset.tipo;
                                    const estado = fila.dataset.estado;
                                    const mostrarPorTipo = tipoSeleccionado === 'todos' || tipo === tipoSeleccionado;
                                    const mostrarPorEstado = estadoSeleccionado === 'todos' || estado === estadoSeleccionado;

                                    if (mostrarPorTipo && mostrarPorEstado) {
                                        fila.style.display = '';
                                        const cantidad = parseInt(
                                            fila.querySelector('td:nth-child(3)').textContent.replace(/,/g, '')
                                        ) || 0;
                                        totalVisible += cantidad;
                                        filasVisibles.push(fila);
                                    } else {
                                        fila.style.display = 'none';
                                    }
                                });

                                totalVisibleActual = totalVisible;

                                filasVisibles.forEach(fila => {
                                    const cantidad = parseInt(
                                        fila.querySelector('td:nth-child(3)').textContent.replace(/,/g, '')
                                    ) || 0;
                                    const porcentaje = totalVisible > 0 ? (cantidad / totalVisible * 100).toFixed(1) : 0;
                                    const progressBar = fila.querySelector('.progress-bar');
                                    progressBar.style.width = porcentaje + '%';
                                    progressBar.textContent = porcentaje + '%';
                                });
                            }

                            // Chart.js
                            (function loadChartJs(){
                                if (typeof Chart === 'undefined'){
                                    var s = document.createElement('script');
                                    s.src = 'https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js';
                                    s.onload = initChart;
                                    document.head.appendChild(s);
                                } else {
                                    initChart();
                                }
                            })();

                            let chartInstance = null;

                            function buildChart(){
                                const filasVisibles = Array.from(tabla.querySelectorAll('tbody tr'))
                                    .filter(r=> r.style.display !== 'none');
                                const vista = tipoVista.value;
                                let labels = [];
                                let values = [];

                                if (vista === 'porTipo' || vista === 'combinado'){
                                    const mapa = {};
                                    filasVisibles.forEach(fila=>{
                                        const tipo = fila.dataset.tipo;
                                        const cantidad = parseInt(
                                            fila.querySelector('td:nth-child(3)').textContent.replace(/,/g, '')
                                        ) || 0;
                                        mapa[tipo] = (mapa[tipo]||0) + cantidad;
                                    });
                                    labels = Object.keys(mapa);
                                    values = labels.map(l=> mapa[l]);
                                }

                                if (vista === 'porEstado'){
                                    const mapa = {};
                                    filasVisibles.forEach(fila=>{
                                        const estado = fila.dataset.estado;
                                        const cantidad = parseInt(
                                            fila.querySelector('td:nth-child(3)').textContent.replace(/,/g, '')
                                        ) || 0;
                                        mapa[estado] = (mapa[estado]||0) + cantidad;
                                    });
                                    labels = Object.keys(mapa);
                                    values = labels.map(l=> mapa[l]);
                                }

                                const ctx = document.getElementById('graficoDistribucion').getContext('2d');
                                const colors = [
                                    'rgba(78,128,152,0.8)','rgba(244,162,97,0.8)','rgba(42,157,143,0.8)',
                                    'rgba(231,111,81,0.8)','rgba(94,96,206,0.8)','rgba(255,193,7,0.8)'
                                ];
                                const bgColors = labels.map((_,i)=> colors[i % colors.length]);
                                const borderColors = bgColors.map(c=> c.replace('0.8','1'));

                                const data = {
                                    labels,
                                    datasets: [{
                                        label: 'Cantidad',
                                        data: values,
                                        backgroundColor: bgColors,
                                        borderColor: borderColors,
                                        borderWidth: 1,
                                        hoverOffset: 0
                                    }]
                                };

                                const options = {
                                    responsive: true,
                                    maintainAspectRatio: false,
                                    animation: false,
                                    resizeDelay: 0,
                                    plugins: { legend: { position: 'bottom' }, tooltip: { enabled: true } },
                                    interaction: { mode: 'nearest', intersect: true }
                                };

                                if (chartInstance){
                                    chartInstance.data = data;
                                    chartInstance.options = options;
                                    chartInstance.update();
                                } else {
                                    chartInstance = new Chart(ctx, { type: 'pie', data, options });
                                }
                            }

                            function initChart(){
                                actualizarTabla();
                                buildChart();
                            }

                            const onChange = ()=>{ actualizarTabla(); buildChart(); };
                            filtroTipo.addEventListener('change', onChange);
                            filtroEstado.addEventListener('change', onChange);
                            tipoVista.addEventListener('change', onChange);

                            // Eliminado: botones y validaciones de reportes (fechas/usuario)
                            // Solo queda el botón de Gráfico: PDF, manejado por el form oculto de abajo.
                        });
                    </script>
                </div>
            </div>
        </div>
    </div>

    <!-- Form oculto: solo filtros del gráfico -->
    <form id="formGrafPdf" action="<?= RUTA; ?>estadisticas/exportar_pdf" method="post" target="_blank" style="display:none">
        <input type="hidden" name="filtroTipo" />
        <input type="hidden" name="filtroEstado" />
        <input type="hidden" name="tipoVista" />
    </form>

    <!-- NUEVOS: formularios ocultos para datos (PDF y Excel/CSV) -->
    <form id="formDatosPdf" action="<?= RUTA; ?>estadisticas/exportar_datos_pdf" method="post" target="_blank" style="display:none">
        <input type="hidden" name="filtroTipo" />
        <input type="hidden" name="filtroEstado" />
        <input type="hidden" name="tipoVista" />
    </form>
    <form id="formDatosExcel" action="<?= RUTA; ?>estadisticas/exportar_excel" method="post" target="_blank" style="display:none">
        <input type="hidden" name="filtroTipo" />
        <input type="hidden" name="filtroEstado" />
        <input type="hidden" name="tipoVista" />
    </form>

    <script>
    document.addEventListener('DOMContentLoaded', function(){
        const filtroTipo   = document.getElementById('filtroTipo');
        const filtroEstado = document.getElementById('filtroEstado');
        const tipoVista    = document.getElementById('tipoVista');

        const btnGrafPdf     = document.getElementById('btnExportPDF');
        const btnDatosPdf    = document.getElementById('btnExportDatosPDF');
        const btnDatosExcel  = document.getElementById('btnExportExcel');

        const formGrafPdf    = document.getElementById('formGrafPdf');
        const formDatosPdf   = document.getElementById('formDatosPdf');
        const formDatosExcel = document.getElementById('formDatosExcel');

        function setFilters(form){
            form.elements.filtroTipo.value   = filtroTipo?.value   || 'todos';
            form.elements.filtroEstado.value = filtroEstado?.value || 'todos';
            form.elements.tipoVista.value    = tipoVista?.value    || 'combinado';
        }

        btnGrafPdf.addEventListener('click', function(){
            setFilters(formGrafPdf); formGrafPdf.submit();
        });
        btnDatosPdf.addEventListener('click', function(){
            setFilters(formDatosPdf); formDatosPdf.submit();
        });
        btnDatosExcel.addEventListener('click', function(){
            setFilters(formDatosExcel); formDatosExcel.submit();
        });
    });
    </script>

    <!-- Nuevos gráficos con JPGraph -->
    <div class="row mt-5">
        <!-- Embudo del Proceso -->
        <div class="col-lg-6 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <h4 class="card-title mb-3">
                        <i class="fas fa-filter text-primary me-2"></i>
                        Embudo del Proceso de Adopción
                    </h4>
                    <div class="row g-2 align-items-center mb-2">
                        <div class="col-6 col-md-4">
                            <?php $anioActual = (int)date('Y'); ?>
                            <select id="selAnioFunnel" class="form-select form-select-sm">
                                <option value="">Todos los años</option>
                                <?php for ($y=$anioActual; $y>=$anioActual-4; $y--): ?>
                                    <option value="<?= $y; ?>"><?= $y; ?></option>
                                <?php endfor; ?>
                            </select>
                        </div>
                        <div class="col-6 col-md-4">
                            <select id="selMesFunnel" class="form-select form-select-sm">
                                <option value="">Todos los meses</option>
                                <?php
                                $meses = ['01'=>'Enero','02'=>'Febrero','03'=>'Marzo','04'=>'Abril','05'=>'Mayo','06'=>'Junio','07'=>'Julio','08'=>'Agosto','09'=>'Septiembre','10'=>'Octubre','11'=>'Noviembre','12'=>'Diciembre'];
                                foreach ($meses as $k=>$v): ?>
                                    <option value="<?= (int)$k; ?>"><?= $v; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="text-center">
                        <img id="imgFunnel" class="img-fluid" alt="Embudo de adopción"
                             src="<?= RUTA; ?>estadisticas/grafico_funnel">
                    </div>
                    <div class="d-grid gap-2 mt-3">
                        <button id="btnPdfFunnel" class="btn btn-danger" type="button">Embudo: PDF (gráfico)</button>
                        <button id="btnDatosPdfFunnel" class="btn btn-outline-danger" type="button">Embudo: PDF (datos)</button>
                        <button id="btnDatosExcelFunnel" class="btn btn-success" type="button">Embudo: Excel (datos)</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Aprobadas vs Rechazadas por Mes -->
        <div class="col-lg-6 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h4 class="card-title mb-0">
                            <i class="fas fa-chart-column text-primary me-2"></i>
                            Aprobadas vs Rechazadas por Mes
                        </h4>
                        <div class="ms-3" style="min-width:140px">
                            <?php $anioActual = (int)date('Y'); ?>
                            <select id="selAnio" class="form-select form-select-sm">
                                <?php for ($y=$anioActual; $y>=$anioActual-4; $y--): ?>
                                    <option value="<?= $y; ?>"><?= $y; ?></option>
                                <?php endfor; ?>
                            </select>
                        </div>
                    </div>
                    <div class="text-center">
                        <img id="imgAprRech" class="img-fluid" alt="Aprobadas vs Rechazadas"
                             src="<?= RUTA; ?>estadisticas/grafico_apr_rech_mensual?anio=<?= (int)date('Y'); ?>">
                    </div>
                    <div class="d-grid gap-2 mt-3">
                        <button id="btnPdfAprRech" class="btn btn-danger" type="button">Gráfico mensual: PDF</button>
                        <button id="btnDatosPdfAprRech" class="btn btn-outline-danger" type="button">Datos mensuales: PDF</button>
                        <button id="btnDatosExcelAprRech" class="btn btn-success" type="button">Datos mensuales: Excel</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Formularios ocultos para exportación de los nuevos gráficos -->
    <form id="formPdfFunnel" action="<?= RUTA; ?>estadisticas/exportar_pdf_funnel" method="get" target="_blank" style="display:none">
        <input type="hidden" name="anio" id="funnelAnio">
        <input type="hidden" name="mes" id="funnelMes">
    </form>
    <form id="formDatosPdfFunnel" action="<?= RUTA; ?>estadisticas/exportar_datos_funnel_pdf" method="get" target="_blank" style="display:none">
        <input type="hidden" name="anio" id="funnelAnioDatosPdf">
        <input type="hidden" name="mes" id="funnelMesDatosPdf">
    </form>
    <form id="formDatosExcelFunnel" action="<?= RUTA; ?>estadisticas/exportar_datos_funnel_excel" method="get" target="_blank" style="display:none">
        <input type="hidden" name="anio" id="funnelAnioDatosXls">
        <input type="hidden" name="mes" id="funnelMesDatosXls">
    </form>
    <form id="formPdfAprRech" action="<?= RUTA; ?>estadisticas/exportar_pdf_apr_rech" method="get" target="_blank" style="display:none">
        <input type="hidden" name="anio" id="pdfAnio" value="<?= (int)date('Y'); ?>">
    </form>
    <form id="formDatosPdfAprRech" action="<?= RUTA; ?>estadisticas/exportar_datos_apr_rech_pdf" method="get" target="_blank" style="display:none">
        <input type="hidden" name="anio" id="datosPdfAnio" value="<?= (int)date('Y'); ?>">
    </form>
    <form id="formDatosExcelAprRech" action="<?= RUTA; ?>estadisticas/exportar_datos_apr_rech_excel" method="get" target="_blank" style="display:none">
        <input type="hidden" name="anio" id="datosExcelAnio" value="<?= (int)date('Y'); ?>">
    </form>

    <script>
    document.addEventListener('DOMContentLoaded', function(){
        const selAnioF = document.getElementById('selAnioFunnel');
        const selMesF  = document.getElementById('selMesFunnel');
        const imgFunnel = document.getElementById('imgFunnel');

        function updateFunnelImgAndForms(){
            const anio = selAnioF?.value || '';
            const mes  = selMesF?.value  || '';
            const base = '<?= RUTA; ?>estadisticas/grafico_funnel';
            const qs = [];
            if (anio) qs.push('anio='+encodeURIComponent(anio));
            if (mes)  qs.push('mes='+encodeURIComponent(mes));
            imgFunnel.src = qs.length ? (base+'?'+qs.join('&')) : base;
            // sincronizar formularios ocultos
            const f1a = document.getElementById('funnelAnio');
            const f1m = document.getElementById('funnelMes');
            const f2a = document.getElementById('funnelAnioDatosPdf');
            const f2m = document.getElementById('funnelMesDatosPdf');
            const f3a = document.getElementById('funnelAnioDatosXls');
            const f3m = document.getElementById('funnelMesDatosXls');
            [f1a,f2a,f3a].forEach(el=>{ if(el) el.value = anio; });
            [f1m,f2m,f3m].forEach(el=>{ if(el) el.value = mes; });
        }

        selAnioF?.addEventListener('change', updateFunnelImgAndForms);
        selMesF?.addEventListener('change', updateFunnelImgAndForms);
        updateFunnelImgAndForms();

        document.getElementById('btnPdfFunnel')?.addEventListener('click', function(){
            updateFunnelImgAndForms();
            document.getElementById('formPdfFunnel').submit();
        });
        const selAnio = document.getElementById('selAnio');
        const imgAprRech = document.getElementById('imgAprRech');
        const pdfAnio = document.getElementById('pdfAnio');
        const datosPdfAnio = document.getElementById('datosPdfAnio');
        const datosExcelAnio = document.getElementById('datosExcelAnio');
        selAnio?.addEventListener('change', function(){
            const y = selAnio.value || new Date().getFullYear();
            imgAprRech.src = '<?= RUTA; ?>estadisticas/grafico_apr_rech_mensual?anio=' + encodeURIComponent(y);
            if (pdfAnio) pdfAnio.value = y;
            if (datosPdfAnio) datosPdfAnio.value = y;
            if (datosExcelAnio) datosExcelAnio.value = y;
        });
        document.getElementById('btnPdfAprRech')?.addEventListener('click', function(){
            document.getElementById('formPdfAprRech').submit();
        });
        document.getElementById('btnDatosPdfAprRech')?.addEventListener('click', function(){
            document.getElementById('formDatosPdfAprRech').submit();
        });
        document.getElementById('btnDatosExcelAprRech')?.addEventListener('click', function(){
            document.getElementById('formDatosExcelAprRech').submit();
        });
        document.getElementById('btnDatosPdfFunnel')?.addEventListener('click', function(){
            document.getElementById('formDatosPdfFunnel').submit();
        });
        document.getElementById('btnDatosExcelFunnel')?.addEventListener('click', function(){
            document.getElementById('formDatosExcelFunnel').submit();
        });
    });
    </script>

    <!-- Llamado a la Acción -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 bg-primary text-white shadow">
                <div class="card-body text-center p-5">
                    <h3 class="mb-4">¿Listo para cambiar una vida?</h3>
                    <p class="lead mb-4">
                        Adoptar una mascota no solo cambia su vida, también cambiará la tuya.
                        Descubre el amor incondicional que solo una mascota puede dar.
                    </p>
                    <a href="<?= RUTA; ?>mascota" class="btn btn-lg btn-light">
                        <i class="fas fa-search me-2"></i>
                        Encuentra tu compañero ideal
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>