<?php if (!$mascota): ?>
    <div class="container py-3">
        <div class="alert alert-danger">Mascota no encontrada.</div>
    </div>
    <?php return; endif; ?>

<div class="container py-3">
    <a href="<?= RUTA; ?>cliente" class="btn btn-link p-0 mb-2"><i class="fas fa-arrow-left me-1"></i>Volver</a>
    <div class="card border-0 shadow-sm">
        <?php if ($mascota->getFoto()): ?>
            <img src="<?= htmlspecialchars($mascota->getFoto()) ?>" class="card-img-top"
                style="max-height:280px;object-fit:cover" />
        <?php endif; ?>
        <div class="card-body">
            <h3 class="h4 mb-1"><?= htmlspecialchars($mascota->getNomMascota()) ?></h3>
            <div class="mb-2 small text-muted">
                <i
                    class="fas fa-tag me-1"></i><?= $mascota->getTipo() ? htmlspecialchars($mascota->getTipo()->getNombre()) : 'General' ?>
            </div>
            <div class="mb-3">
                <span class="badge bg-success"><?= htmlspecialchars($mascota->getEstadoAdopcion()) ?></span>
            </div>
            <?php if (isset($_GET['msg']) && $_GET['msg'] === 'ya_solicitada'): ?>
                <div class="alert alert-warning">Ya registraste una solicitud para esta mascota.</div>
            <?php elseif (isset($_GET['msg']) && $_GET['msg'] === 'error'): ?>
                <div class="alert alert-danger">No pudimos registrar tu solicitud. Inténtalo nuevamente.</div>
            <?php endif; ?>
            <div class="d-grid gap-2">
                <?php if (isset($_SESSION['usuario']) && $_SESSION['usuario']['rol'] === 'Cliente' && $mascota->getEstadoAdopcion() === 'Disponible'): ?>
                    <form method="post" action="<?= RUTA; ?>cliente/adoptar">
                        <input type="hidden" name="id_mascota" value="<?= $mascota->getIdmascota() ?>" />
                        <button class="btn btn-success btn-lg" type="submit"><i class="fas fa-heart me-1"></i>Solicitar
                            adopción</button>
                    </form>
                <?php else: ?>
                    <button class="btn btn-secondary" disabled>No disponible para adopción</button>
                <?php endif; ?>
                <a class="btn btn-outline-secondary" href="<?= RUTA; ?>cliente/scan"><i
                        class="fas fa-qrcode me-1"></i>Escanear otro QR</a>
            </div>
        </div>
    </div>
</div>