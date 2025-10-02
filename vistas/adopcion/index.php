<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h2 mb-0">
            <i class="fas fa-heart text-primary me-2"></i>
            Registro de Adopciones
        </h1>
        <a href="<?= RUTA; ?>adoptante/create" class="btn btn-primary">
            <i class="fas fa-plus-circle me-2"></i>Nueva Adopción
        </a>
    </div>

    <!-- Filtro de búsqueda -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form action="<?= RUTA; ?>adoptante" method="GET" class="row g-3">
                <div class="col-md-8">
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="fas fa-search"></i>
                        </span>
                        <input type="text" name="search" class="form-control"
                            placeholder="Buscar por nombre, ID, teléfono o mascota..."
                            value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary flex-grow-1">
                            <i class="fas fa-search me-2"></i>Buscar
                        </button>
                        <?php if (!empty($_GET['search'])): ?>
                            <a href="<?= RUTA; ?>adoptante" class="btn btn-outline-secondary" title="Limpiar búsqueda">
                                <i class="fas fa-times"></i>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-0">
                    <?php if (empty($adoptantes)): ?>
                        <div class="alert alert-info m-3">
                            <i class="fas fa-info-circle me-2"></i>
                            No se encontraron
                            adoptantes<?= !empty($_GET['search']) ? ' con los criterios de búsqueda especificados' : '' ?>.
                            <?php if (!empty($_GET['search'])): ?>
                                <a href="<?= RUTA; ?>adoptante" class="alert-link">Ver todos los adoptantes</a>
                            <?php endif; ?>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="py-3">Adoptante</th>
                                        <th class="py-3">Contacto</th>
                                        <th class="py-3">Mascota Adoptada</th>
                                        <th class="py-3">Tipo</th>
                                        <th class="py-3">Foto</th>
                                        <th class="py-3 text-end">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($adoptantes as $a): ?>
                                        <tr>
                                            <td class="align-middle">
                                                <div class="d-flex align-items-center">
                                                    <i class="fas fa-user-circle text-primary me-2 fa-2x"></i>
                                                    <div>
                                                        <div class="fw-bold"><?= htmlspecialchars($a->getNombre()); ?></div>
                                                        <div class="small text-muted">#<?= $a->getIdAdoptante(); ?></div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="align-middle">
                                                <?php if ($a->getTelefono()): ?>
                                                    <i class="fas fa-phone text-success me-2"></i>
                                                    <?= htmlspecialchars($a->getTelefono()); ?>
                                                <?php else: ?>
                                                    <span class="text-muted">
                                                        <i class="fas fa-phone-slash me-2"></i>Sin teléfono
                                                    </span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="align-middle">
                                                <?php if ($a->nom_mascota): ?>
                                                    <span class="fw-medium"><?= htmlspecialchars($a->nom_mascota); ?></span>
                                                <?php else: ?>
                                                    <span class="badge bg-warning text-dark">
                                                        <i class="fas fa-exclamation-circle me-1"></i>Pendiente
                                                    </span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="align-middle">
                                                <?php if ($a->tipo_mascota): ?>
                                                    <span class="badge bg-info text-dark">
                                                        <?= htmlspecialchars($a->tipo_mascota); ?>
                                                    </span>
                                                <?php else: ?>
                                                    —
                                                <?php endif; ?>
                                            </td>
                                            <td class="align-middle">
                                                <?php if (!empty($a->foto_mascota)): ?>
                                                    <img src="<?= htmlspecialchars($a->foto_mascota); ?>" class="rounded" width="50"
                                                        height="50" style="object-fit: cover;" alt="foto">
                                                <?php else: ?>
                                                    <div class="bg-light rounded d-flex align-items-center justify-content-center"
                                                        style="width: 50px; height: 50px;">
                                                        <i class="fas fa-camera text-muted"></i>
                                                    </div>
                                                <?php endif; ?>
                                            </td>
                                            <td class="align-middle text-end">
                                                <div class="btn-group">
                                                    <a href="<?= RUTA; ?>adoptante/edit/<?= $a->getIdAdoptante(); ?>"
                                                        class="btn btn-sm btn-outline-primary">
                                                        <i class="fas fa-edit me-1"></i>Editar
                                                    </a>
                                                    <a href="<?= RUTA; ?>adoptante/delete/<?= $a->getIdAdoptante(); ?>"
                                                        class="btn btn-sm btn-outline-danger"
                                                        onclick="return confirm('¿Estás seguro de que deseas eliminar este registro de adopción?');">
                                                        <i class="fas fa-trash me-1"></i>Eliminar
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>