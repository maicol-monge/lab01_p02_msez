<!-- CREATE TABLE mascota (
idmascota INT(11) NOT NULL AUTO_INCREMENT,
idraza INT(11),
nom_mascota VARCHAR(200),
nombre_propietario VARCHAR(250),
PRIMARY KEY (idmascota),
FOREIGN KEY (idraza) REFERENCES raza(idraza)
); -->

<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h2 mb-0">
            <i class="fas fa-paw me-2 text-primary"></i>
            Mascotas en Adopción
        </h1>
        <a href="<?= RUTA; ?>mascota/create" class="btn btn-primary">
            <i class="fas fa-plus-circle me-2"></i>Agregar Nueva Mascota
        </a>
    </div>

    <!-- Filtros de búsqueda -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form action="<?= RUTA; ?>mascota" method="GET" class="row g-3">
                <div class="col-md-4">
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="fas fa-search"></i>
                        </span>
                        <input type="text" name="nombre" class="form-control" 
                               placeholder="Buscar por nombre..."
                               value="<?= isset($_GET['nombre']) ? htmlspecialchars($_GET['nombre']) : '' ?>">
                    </div>
                </div>
                
                <div class="col-md-3">
                    <select name="tipo" class="form-select">
                        <option value="">Todos los tipos</option>
                        <?php foreach ($tipos as $tipo): ?>
                            <option value="<?= $tipo->getIdTipo() ?>" 
                                    <?= (isset($_GET['tipo']) && $_GET['tipo'] == $tipo->getIdTipo()) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($tipo->getNombre()) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-3">
                    <select name="estado" class="form-select">
                        <option value="">Todos los estados</option>
                        <option value="disponible" <?= (isset($_GET['estado']) && $_GET['estado'] == 'disponible') ? 'selected' : '' ?>>
                            Disponible para adopción
                        </option>
                        <option value="adoptado" <?= (isset($_GET['estado']) && $_GET['estado'] == 'adoptado') ? 'selected' : '' ?>>
                            Ya adoptado
                        </option>
                    </select>
                </div>

                <div class="col-md-2">
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary flex-grow-1">
                            <i class="fas fa-search me-2"></i>Buscar
                        </button>
                        <?php if (!empty($_GET['nombre']) || !empty($_GET['tipo']) || !empty($_GET['estado'])): ?>
                            <a href="<?= RUTA; ?>mascota" class="btn btn-outline-secondary" title="Limpiar filtros">
                                <i class="fas fa-times"></i>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <?php if (empty($mascotas)): ?>
        <div class="alert alert-info">
            <i class="fas fa-info-circle me-2"></i>
            No se encontraron mascotas con los criterios de búsqueda especificados.
            <?php if (!empty($_GET)): ?>
                <a href="<?= RUTA; ?>mascota" class="alert-link">Ver todas las mascotas</a>
            <?php endif; ?>
        </div>
    <?php else: ?>
        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
        <?php foreach ($mascotas as $mascota): ?>
            <div class="col">
                <div class="card h-100 border-0 shadow-sm">
                    <?php if ($mascota->getFoto()): ?>
                        <img src="<?= htmlspecialchars($mascota->getFoto()); ?>" class="card-img-top"
                            style="height: 250px; object-fit: cover;" alt="<?= htmlspecialchars($mascota->getNomMascota()); ?>">
                    <?php else: ?>
                        <div class="card-img-top bg-light d-flex align-items-center justify-content-center"
                            style="height: 250px;">
                            <i class="fas fa-paw fa-4x text-muted"></i>
                        </div>
                    <?php endif; ?>

                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <h5 class="card-title mb-0">
                                <?= htmlspecialchars($mascota->getNomMascota()); ?>
                            </h5>
                            <span class="badge <?= $mascota->getEstadoAdopcion() === 'Disponible' ? 'bg-success' : 'bg-warning text-dark' ?>">
                                <i class="fas <?= $mascota->getEstadoAdopcion() === 'Disponible' ? 'fa-heart' : 'fa-home' ?> me-1"></i>
                                <?= $mascota->getEstadoAdopcion() ?>
                            </span>
                        </div>
                        
                        <div class="mb-3">
                            <span class="badge bg-info text-dark">
                                <i class="fas fa-tag me-1"></i>
                                <?= $mascota->getTipo() ? htmlspecialchars($mascota->getTipo()->getNombre()) : 'Sin categoría'; ?>
                            </span>
                            <?php if ($mascota->getTipo() && $mascota->getTipo()->getDescripcion()): ?>
                                <div class="mt-2 small text-muted">
                                    <?= htmlspecialchars($mascota->getTipo()->getDescripcion()); ?>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="d-flex align-items-center gap-3 mb-3">
                            <span class="small text-muted">
                                <i class="fas fa-hashtag me-1"></i>ID: <?= $mascota->getIdmascota(); ?>
                            </span>
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <?php if ($mascota->getEstadoAdopcion() === 'Disponible'): ?>
                                <a href="<?= RUTA; ?>adoptante/create?id_mascota=<?= $mascota->getIdmascota(); ?>" 
                                   class="btn btn-success btn-sm">
                                    <i class="fas fa-heart me-1"></i>Adoptar
                                </a>
                            <?php endif; ?>
                            <a href="<?= RUTA; ?>mascota/edit/<?= $mascota->getIdmascota(); ?>"
                                class="btn btn-outline-primary btn-sm">
                                <i class="fas fa-edit me-1"></i>Editar
                            </a>
                            <a href="<?= RUTA; ?>mascota/delete/<?= $mascota->getIdmascota(); ?>"
                                class="btn btn-outline-danger btn-sm"
                                onclick="return confirm('¿Estás seguro de que deseas eliminar a <?= htmlspecialchars($mascota->getNomMascota()); ?>?')">
                                <i class="fas fa-trash me-1"></i>Eliminar
                            </a>
                        </div>

                        <?php
    // obtener ruta del QR desde diferentes posibles estructuras ($mascota puede ser objeto o array)
    $qrPath = '';
    if (is_object($mascota) && method_exists($mascota, 'getQrCode')) {
        $qrPath = $mascota->getQrCode();
    } elseif (is_object($mascota) && property_exists($mascota, 'qr_code')) {
        $qrPath = $mascota->qr_code;
    } elseif (is_array($mascota) && isset($mascota['qr_code'])) {
        $qrPath = $mascota['qr_code'];
    }
?>
<?php if (!empty($qrPath)): ?>
    <div class="mt-2 text-center">
        <a href="<?= RUTA ?>mascota/ver/<?= is_object($mascota) && method_exists($mascota,'getIdmascota') ? $mascota->getIdmascota() : (is_array($mascota) ? $mascota['id_mascota'] ?? '' : '') ?>">
            <img src="<?= htmlspecialchars(RUTA . $qrPath) ?>" alt="QR <?= htmlspecialchars($mascota->getNomMascota() ?? ($mascota['nombre'] ?? '')) ?>" style="width:80px;height:auto;border-radius:6px;">
        </a>
    </div>
<?php else: ?>
    <div class="mt-2 text-center text-muted small">Sin QR</div>
<?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>