<div class="container py-3">
    <h1 class="h4 mb-3"><i class="fas fa-clipboard-list me-2"></i>Mis solicitudes</h1>
    <?php if (isset($_GET['msg']) && $_GET['msg'] === 'sol_enviada'): ?>
        <div class="alert alert-success">Tu solicitud fue enviada y está pendiente de revisión.</div>
    <?php endif; ?>
    <?php if (empty($solicitudes)): ?>
        <div class="alert alert-info">Aún no tienes solicitudes.</div>
    <?php else: ?>
        <div class="list-group">
            <?php foreach ($solicitudes as $s): ?>
                <div class="list-group-item">
                    <div class="d-flex justify-content-between">
                        <div>
                            <div class="fw-bold"><i class="fas fa-dog me-1"></i><?= htmlspecialchars($s['mascota_nombre']) ?>
                            </div>
                            <small class="text-muted">Fecha: <?= htmlspecialchars($s['fecha_adopcion']) ?></small>
                        </div>
                        <div>
                            <?php
                            $badge = 'bg-secondary';
                            if ($s['estado'] === 'Pendiente')
                                $badge = 'bg-warning text-dark';
                            if ($s['estado'] === 'Aprobada')
                                $badge = 'bg-success';
                            if ($s['estado'] === 'Rechazada')
                                $badge = 'bg-danger';
                            ?>
                            <span class="badge <?= $badge ?>"><?= htmlspecialchars($s['estado']) ?></span>
                        </div>
                    </div>
                    <div class="mt-2 text-end">
                        <?php if (in_array($s['estado'], ['Aprobada', 'Rechazada'])): ?>
                            <a class="btn btn-outline-primary btn-sm" href="<?= RUTA; ?>adopcion/ticket/<?= $s['id_adopcion'] ?>">
                                <i class="fas fa-receipt me-1"></i> Ver/Imprimir ticket
                            </a>
                        <?php else: ?>
                            <span class="text-muted small">Ticket no disponible</span>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
    <div class="mt-3">
        <a href="<?= RUTA; ?>cliente" class="btn btn-link p-0"><i class="fas fa-arrow-left me-1"></i>Volver a
            mascotas</a>
    </div>
</div>