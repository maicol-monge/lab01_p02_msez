<div class="container py-4">
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

                    <script>
                        document.addEventListener('DOMContentLoaded', function () {
                            const filtroTipo = document.getElementById('filtroTipo');
                            const filtroEstado = document.getElementById('filtroEstado');
                            const tipoVista = document.getElementById('tipoVista');
                            const tabla = document.getElementById('tablaDistribucion');
                            const filas = tabla.querySelectorAll('tbody tr');

                            function actualizarTabla() {
                                const tipoSeleccionado = filtroTipo.value;
                                const estadoSeleccionado = filtroEstado.value;
                                const vistaSeleccionada = tipoVista.value;

                                let totalVisible = 0;
                                let filasVisibles = [];

                                filas.forEach(fila => {
                                    const tipo = fila.dataset.tipo;
                                    const estado = fila.dataset.estado;
                                    const mostrarPorTipo = tipoSeleccionado === 'todos' || tipo === tipoSeleccionado;
                                    const mostrarPorEstado = estadoSeleccionado === 'todos' || estado === estadoSeleccionado;

                                    if (mostrarPorTipo && mostrarPorEstado) {
                                        fila.style.display = '';
                                        const cantidad = parseInt(fila.querySelector('td:nth-child(3)').textContent.replace(',', ''));
                                        totalVisible += cantidad;
                                        filasVisibles.push(fila);
                                    } else {
                                        fila.style.display = 'none';
                                    }
                                });

                                // Actualizar porcentajes
                                filasVisibles.forEach(fila => {
                                    const cantidad = parseInt(fila.querySelector('td:nth-child(3)').textContent.replace(',', ''));
                                    const porcentaje = (cantidad / totalVisible * 100).toFixed(1);
                                    const progressBar = fila.querySelector('.progress-bar');
                                    progressBar.style.width = porcentaje + '%';
                                    progressBar.textContent = porcentaje + '%';
                                });
                            }

                            filtroTipo.addEventListener('change', actualizarTabla);
                            filtroEstado.addEventListener('change', actualizarTabla);
                            tipoVista.addEventListener('change', actualizarTabla);

                            // Inicializar la tabla
                            actualizarTabla();
                        });
                    </script>
                </div>
            </div>
        </div>
    </div>

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