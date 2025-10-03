<?php
// Fallback: si el controlador no inyecta $solicitudes, obtenerlas aquí
if (!isset($solicitudes)) {
    require_once 'modelos/adopcionmodel.php';
    $adopcionModel = new AdopcionModel();
    $solicitudes = $adopcionModel->search();
}

$esAdmin = isset($_SESSION['usuario']) && $_SESSION['usuario']['rol'] === 'Administrador';
$usuarioId = $_SESSION['usuario']['id_usuario'] ?? null;

// Funciones de ayuda
function estadoBadgeClass($estado)
{
    return $estado === 'Aprobada' ? 'success' : ($estado === 'Rechazada' ? 'danger' : 'warning');
}
?>

<div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
    <h2 class="h4 mb-0">
        <i class="fas fa-paw me-2 text-primary"></i>
        <?= $esAdmin ? 'Solicitudes de Adopción' : 'Mis solicitudes de adopción' ?>
    </h2>
    <div class="d-flex gap-2">
        <?php if ($esAdmin): ?>
            <a class="btn btn-outline-secondary btn-sm" href="<?= RUTA; ?>adopcion/scan">
                <i class="fas fa-qrcode me-1"></i> Escanear/Buscar ticket
            </a>
        <?php endif; ?>
    </div>
    <div class="w-100"></div>
    <p class="text-muted small mb-0">Total: <?= count($solicitudes) ?> registro(s)</p>
    <hr class="mt-2 mb-0 w-100" />
    <div class="w-100"></div>
</div>

<?php if (empty($solicitudes)): ?>
    <div class="alert alert-info"><i class="fas fa-info-circle me-1"></i>No hay solicitudes registradas.</div>
<?php else: ?>
    <!-- Vista tarjetas (móvil) -->
    <div class="row g-3 d-md-none">
        <?php foreach ($solicitudes as $sol): ?>
            <?php if (!$esAdmin && $usuarioId && $sol['id_usuario'] != $usuarioId)
                continue; ?>
            <div class="col-12">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body d-flex gap-3">
                        <?php
                        $foto = !empty($sol['foto']) ? $sol['foto'] : (RUTA . 'img/404.png');
                        ?>
                        <img src="<?= htmlspecialchars($foto) ?>" alt="Mascota" class="rounded"
                            style="width:84px;height:84px;object-fit:cover;flex-shrink:0" />
                        <div class="flex-grow-1">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <div class="fw-semibold"><?= htmlspecialchars($sol['mascota_nombre']) ?></div>
                                    <div class="text-muted small">Cliente: <?= htmlspecialchars($sol['usuario_nombre']) ?></div>
                                </div>
                                <span
                                    class="badge bg-<?= estadoBadgeClass($sol['estado']) ?>"><?= htmlspecialchars($sol['estado']) ?></span>
                            </div>
                            <div class="text-muted small mt-1"><i
                                    class="far fa-calendar me-1"></i><?= htmlspecialchars($sol['fecha_adopcion']) ?></div>
                            <div class="mt-2 d-flex gap-2 flex-wrap">
                                <?php if ($esAdmin && $sol['estado'] === 'Pendiente'): ?>
                                    <form method="POST" action="<?= RUTA; ?>index.php?url=adopcion/aprobar">
                                        <input type="hidden" name="id_adopcion" value="<?= $sol['id_adopcion'] ?>">
                                        <button type="submit" class="btn btn-success btn-sm"><i
                                                class="fas fa-check me-1"></i>Aprobar</button>
                                    </form>
                                    <form method="POST" action="<?= RUTA; ?>index.php?url=adopcion/rechazar">
                                        <input type="hidden" name="id_adopcion" value="<?= $sol['id_adopcion'] ?>">
                                        <button type="submit" class="btn btn-danger btn-sm"><i
                                                class="fas fa-times me-1"></i>Rechazar</button>
                                    </form>
                                <?php endif; ?>
                                <a class="btn btn-outline-primary btn-sm"
                                    href="<?= RUTA; ?>index.php?url=adopcion/ticket/<?= $sol['id_adopcion'] ?>">
                                    <i class="fas fa-receipt me-1"></i> Ticket
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Vista tabla (md y superior) -->
    <div class="table-responsive d-none d-md-block">
        <table class="table align-middle">
            <thead class="table-light">
                <tr>
                    <th style="width:64px">Foto</th>
                    <?php if ($esAdmin): ?>
                        <th>Cliente</th><?php endif; ?>
                    <th>Mascota</th>
                    <th>Fecha</th>
                    <th>Estado</th>
                    <th class="text-end">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($solicitudes as $sol): ?>
                    <?php if (!$esAdmin && $usuarioId && $sol['id_usuario'] != $usuarioId)
                        continue; ?>
                    <?php $foto = !empty($sol['foto']) ? $sol['foto'] : (RUTA . 'img/404.png'); ?>
                    <tr>
                        <td>
                            <img src="<?= htmlspecialchars($foto) ?>" alt="Mascota" class="rounded"
                                style="width:48px;height:48px;object-fit:cover" />
                        </td>
                        <?php if ($esAdmin): ?>
                            <td><?= htmlspecialchars($sol['usuario_nombre']) ?></td><?php endif; ?>
                        <td><?= htmlspecialchars($sol['mascota_nombre']) ?></td>
                        <td><?= htmlspecialchars($sol['fecha_adopcion']) ?></td>
                        <td>
                            <span
                                class="badge bg-<?= estadoBadgeClass($sol['estado']) ?>"><?= htmlspecialchars($sol['estado']) ?></span>
                        </td>
                        <td class="text-end">
                            <div class="d-inline-flex gap-2">
                                <?php if ($esAdmin && $sol['estado'] === 'Pendiente'): ?>
                                    <form method="POST" action="<?= RUTA; ?>index.php?url=adopcion/aprobar">
                                        <input type="hidden" name="id_adopcion" value="<?= $sol['id_adopcion'] ?>">
                                        <button type="submit" class="btn btn-success btn-sm"><i
                                                class="fas fa-check me-1"></i>Aprobar</button>
                                    </form>
                                    <form method="POST" action="<?= RUTA; ?>index.php?url=adopcion/rechazar">
                                        <input type="hidden" name="id_adopcion" value="<?= $sol['id_adopcion'] ?>">
                                        <button type="submit" class="btn btn-danger btn-sm"><i
                                                class="fas fa-times me-1"></i>Rechazar</button>
                                    </form>
                                <?php endif; ?>
                                <a class="btn btn-outline-primary btn-sm"
                                    href="<?= RUTA; ?>index.php?url=adopcion/ticket/<?= $sol['id_adopcion'] ?>">
                                    <i class="fas fa-receipt me-1"></i> Ticket
                                </a>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>