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

    <!-- Distribución por Tipo de Mascota -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h4 class="card-title mb-4">
                        <i class="fas fa-chart-pie text-primary me-2"></i>
                        Distribución por Tipo de Mascota
                    </h4>
                    <div class="table-responsive">
                        <table class="table">
                            <thead class="table-light">
                                <tr>
                                    <th>Tipo</th>
                                    <th class="text-end">Cantidad</th>
                                    <th>Distribución</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $total = array_sum(array_column($stats['por_tipo'], 'cantidad'));
                                foreach ($stats['por_tipo'] as $tipo):
                                    $porcentaje = $total > 0 ? ($tipo['cantidad'] / $total) * 100 : 0;
                                    ?>
                                    <tr>
                                        <td>
                                            <i class="fas fa-paw text-primary me-2"></i>
                                            <?= htmlspecialchars($tipo['nombre']); ?>
                                        </td>
                                        <td class="text-end"><?= number_format($tipo['cantidad']); ?></td>
                                        <td style="width: 50%;">
                                            <div class="progress" style="height: 20px;">
                                                <div class="progress-bar bg-primary" role="progressbar"
                                                    style="width: <?= $porcentaje; ?>%" aria-valuenow="<?= $porcentaje; ?>"
                                                    aria-valuemin="0" aria-valuemax="100">
                                                    <?= number_format($porcentaje, 1); ?>%
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
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