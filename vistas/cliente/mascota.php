<?php if (!$mascota): ?>
    <div class="container py-3">
        <div class="alert alert-danger">Mascota no encontrada.</div>
    </div>
    <?php return; endif; ?>

<div class="container py-3">
    <a href="<?= RUTA; ?>cliente" class="btn btn-link p-0 mb-2"><i class="fas fa-arrow-left me-1"></i>Volver</a>

    <div class="card border-0 shadow-sm overflow-hidden">
        <!-- Imagen destacada con overlay -->
        <div class="position-relative bg-light">
            <?php
            $foto = $mascota->getFoto() ? htmlspecialchars($mascota->getFoto()) : (RUTA . 'img/404.png');
            ?>
            <img src="<?= $foto ?>" alt="<?= htmlspecialchars($mascota->getNomMascota()) ?>" class="w-100"
                style="max-height:340px;object-fit:cover;display:block" />
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
            <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-2">
                <h1 class="h4 mb-0 d-flex align-items-center">
                    <i class="fas fa-paw text-primary me-2"></i>
                    <?= htmlspecialchars($mascota->getNomMascota()) ?>
                </h1>
                <?php if ($mascota->getEstadoAdopcion() !== 'Disponible'): ?>
                    <span class="badge rounded-pill bg-secondary">No disponible</span>
                <?php endif; ?>
            </div>

            <?php if (isset($_GET['msg']) && $_GET['msg'] === 'ya_solicitada'): ?>
                <div class="alert alert-warning"><i class="fas fa-exclamation-triangle me-1"></i> Ya registraste una
                    solicitud para esta mascota.</div>
            <?php elseif (isset($_GET['msg']) && $_GET['msg'] === 'error'): ?>
                <div class="alert alert-danger"><i class="fas fa-times-circle me-1"></i> No pudimos registrar tu solicitud.
                    Inténtalo nuevamente.</div>
            <?php endif; ?>

            <!-- Descripción / información -->
            <div class="mb-3 text-muted small">
                <i class="far fa-circle-question me-1"></i>
                Esta mascota está esperando un hogar amoroso. Si te interesa adoptarla, envía tu solicitud y nuestro
                equipo la revisará.
            </div>

            <!-- Acciones -->
            <div class="row g-2">
                <div class="col-12 col-md-6 d-grid">
                    <?php if (isset($_SESSION['usuario']) && $_SESSION['usuario']['rol'] === 'Cliente' && $mascota->getEstadoAdopcion() === 'Disponible'): ?>
                        <form method="post" action="<?= RUTA; ?>cliente/adoptar">
                            <input type="hidden" name="id_mascota" value="<?= $mascota->getIdmascota() ?>" />
                            <button class="btn btn-success btn-lg" type="submit">
                                <i class="fas fa-heart me-2"></i> Solicitar adopción
                            </button>
                        </form>
                    <?php else: ?>
                        <button class="btn btn-secondary btn-lg" disabled>
                            <i class="fas fa-ban me-2"></i> No disponible para adopción
                        </button>
                    <?php endif; ?>
                </div>
                <div class="col-12 col-md-6 d-grid">
                    <a class="btn btn-outline-secondary btn-lg" href="<?= RUTA; ?>cliente/scan">
                        <i class="fas fa-qrcode me-2"></i> Escanear otro QR
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>