<?php if (!$mascota): ?>
    <div class="container py-3">
        <div class="alert alert-danger">Mascota no encontrada.</div>
    </div>
    <?php return; endif; ?>

<div class="container py-3">
    <a href="<?= RUTA; ?>cliente" class="btn btn-link p-0 mb-2"><i class="fas fa-arrow-left me-1"></i>Volver</a>

    <div class="card border-0 shadow-sm overflow-hidden">
        <!-- Imagen destacada con badges -->
        <div class="position-relative bg-light">
            <?php $foto = $mascota->getFoto() ? htmlspecialchars($mascota->getFoto()) : (RUTA . 'img/404.png'); ?>
            <img src="<?= $foto ?>" alt="<?= htmlspecialchars($mascota->getNomMascota()) ?>" class="w-100"
                style="max-height:300px;object-fit:cover;display:block" />
            <div class="position-absolute top-0 start-0 p-2 d-flex gap-2">
                <span class="badge bg-info text-dark">
                    <i class="fas fa-tag me-1"></i>
                    <?= $mascota->getTipo() ? htmlspecialchars($mascota->getTipo()->getNombre()) : 'General' ?>
                </span>
                <span
                    class="badge bg-<?= $mascota->getEstadoAdopcion() === 'Disponible' ? 'success' : ($mascota->getEstadoAdopcion() === 'Adoptado' ? 'secondary' : 'warning') ?>">
                    <?= htmlspecialchars($mascota->getEstadoAdopcion()) ?>
                </span>
            </div>
        </div>

        <div class="card-body">
            <div class="text-center mb-3">
                <h2 class="h4 mb-1"><i class="fas fa-heart text-danger me-2"></i>¿Adoptar a
                    <?= htmlspecialchars($mascota->getNomMascota()) ?>?</h2>
                <p class="text-muted mb-0">Al confirmar, enviaremos tu solicitud para revisión del equipo del refugio.
                </p>
            </div>

            <?php if (isset($_GET['msg']) && $_GET['msg'] === 'ya_solicitada'): ?>
                <div class="alert alert-warning"><i class="fas fa-exclamation-triangle me-1"></i> Ya registraste una
                    solicitud para esta mascota.</div>
            <?php endif; ?>

            <div class="row g-2 align-items-stretch">
                <div class="col-12 col-md-6 d-grid">
                    <?php if (isset($_SESSION['usuario']) && $_SESSION['usuario']['rol'] === 'Cliente' && $mascota->getEstadoAdopcion() === 'Disponible'): ?>
                        <form method="post" action="<?= RUTA; ?>cliente/adoptar" class="h-100">
                            <input type="hidden" name="id_mascota" value="<?= $mascota->getIdmascota() ?>" />
                            <button class="btn btn-success btn-lg w-100 h-100" type="submit">
                                <i class="fas fa-check me-2"></i> Sí, quiero adoptar
                            </button>
                        </form>
                    <?php else: ?>
                        <a href="<?= RUTA; ?>login" class="btn btn-primary btn-lg w-100 h-100">
                            <i class="fas fa-sign-in-alt me-2"></i> Inicia sesión para adoptar
                        </a>
                    <?php endif; ?>
                </div>
                <div class="col-12 col-md-6 d-grid">
                    <a href="<?= RUTA; ?>cliente/mascota/<?= $mascota->getIdmascota() ?>"
                        class="btn btn-outline-secondary btn-lg w-100 h-100">
                        <i class="fas fa-info-circle me-2"></i> Ver detalles
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Proceso de adopción -->
    <div class="card border-0 shadow-sm mt-3">
        <div class="card-body">
            <h6 class="card-title mb-3"><i class="fas fa-route text-primary me-2"></i>Proceso de adopción</h6>
            <div class="row text-center small">
                <div class="col-4">
                    <i class="fas fa-paper-plane fa-2x text-primary mb-1"></i>
                    <div>1. Envías solicitud</div>
                </div>
                <div class="col-4">
                    <i class="fas fa-search fa-2x text-warning mb-1"></i>
                    <div>2. Revisión del equipo</div>
                </div>
                <div class="col-4">
                    <i class="fas fa-home fa-2x text-success mb-1"></i>
                    <div>3. ¡Nuevo hogar!</div>
                </div>
            </div>
        </div>
    </div>
</div>