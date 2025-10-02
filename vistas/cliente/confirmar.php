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
                style="max-height:260px;object-fit:cover" />
        <?php endif; ?>
        <div class="card-body text-center">
            <h3 class="h5">¿Deseas adoptar a <?= htmlspecialchars($mascota->getNomMascota()) ?>?</h3>
            <p class="text-muted mb-3">Confirma para enviar tu solicitud de adopción.</p>
            <?php if (isset($_GET['msg']) && $_GET['msg'] === 'ya_solicitada'): ?>
                <div class="alert alert-warning">Ya registraste una solicitud para esta mascota.</div>
            <?php endif; ?>
            <div class="d-grid gap-2">
                <?php if (isset($_SESSION['usuario']) && $_SESSION['usuario']['rol'] === 'Cliente' && $mascota->getEstadoAdopcion() === 'Disponible'): ?>
                    <form method="post" action="<?= RUTA; ?>cliente/adoptar">
                        <input type="hidden" name="id_mascota" value="<?= $mascota->getIdmascota() ?>" />
                        <button class="btn btn-success btn-lg" type="submit"><i class="fas fa-heart me-1"></i> Sí, quiero
                            adoptar</button>
                    </form>
                <?php else: ?>
                    <a href="<?= RUTA; ?>login" class="btn btn-primary btn-lg"><i class="fas fa-sign-in-alt me-1"></i>
                        Inicia sesión para adoptar</a>
                <?php endif; ?>
                <a href="<?= RUTA; ?>cliente/mascota/<?= $mascota->getIdmascota() ?>"
                    class="btn btn-outline-secondary">Ver detalles</a>
            </div>
        </div>
    </div>
</div>