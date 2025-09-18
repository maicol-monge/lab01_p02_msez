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

    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-0">
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
                </div>
            </div>
        </div>
    </div>
</div>