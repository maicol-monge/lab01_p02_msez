<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h2 mb-0">
            <i class="fas fa-tags text-primary me-2"></i>
            Categorías de Mascotas
        </h1>
        <a href="<?= RUTA; ?>tipomascota/create" class="btn btn-primary">
            <i class="fas fa-plus-circle me-2"></i>Nueva Categoría
        </a>
    </div>

    <!-- Filtro de búsqueda -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form action="<?= RUTA; ?>tipomascota" method="GET" class="row g-3">
                <div class="col-md-8">
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="fas fa-search"></i>
                        </span>
                        <input type="text" name="search" class="form-control" placeholder="Buscar por nombre o ID..."
                            value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary flex-grow-1">
                            <i class="fas fa-search me-2"></i>Buscar
                        </button>
                        <?php if (!empty($_GET['search'])): ?>
                            <a href="<?= RUTA; ?>tipomascota" class="btn btn-outline-secondary" title="Limpiar búsqueda">
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
                <div class="card-body">
                    <?php if (empty($tipos)): ?>
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            No se encontraron
                            categorías<?= !empty($_GET['search']) ? ' con los criterios de búsqueda especificados' : '' ?>.
                            <?php if (!empty($_GET['search'])): ?>
                                <a href="<?= RUTA; ?>tipomascota" class="alert-link">Ver todas las categorías</a>
                            <?php endif; ?>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="py-3">Categoría</th>
                                        <th class="py-3">Descripción</th>
                                        <th class="py-3 text-end">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($tipos as $t): ?>
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <span class="bg-primary bg-opacity-10 p-2 rounded me-3">
                                                        <i class="fas fa-paw text-primary"></i>
                                                    </span>
                                                    <div>
                                                        <div class="fw-medium"><?= htmlspecialchars($t->getNombre()); ?></div>
                                                        <div class="small text-muted">ID: <?= $t->getIdTipo(); ?></div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <?php if ($t->getDescripcion()): ?>
                                                    <?= htmlspecialchars($t->getDescripcion()); ?>
                                                <?php else: ?>
                                                    <span class="text-muted fst-italic">Sin descripción</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-end">
                                                <div class="btn-group">
                                                    <a href="<?= RUTA; ?>tipomascota/edit/<?= $t->getIdTipo(); ?>"
                                                        class="btn btn-sm btn-outline-primary">
                                                        <i class="fas fa-edit me-1"></i>Editar
                                                    </a>
                                                    <a href="<?= RUTA; ?>tipomascota/delete/<?= $t->getIdTipo(); ?>"
                                                        class="btn btn-sm btn-outline-danger"
                                                        onclick="return confirm('¿Estás seguro de que deseas eliminar esta categoría? Esto podría afectar a las mascotas asociadas.');">
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