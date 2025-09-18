<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <h1 class="h3 mb-4 text-center">
                        <i class="fas fa-edit text-primary me-2"></i>
                        Editar Categoría de Mascota
                    </h1>

                    <form action="<?= RUTA; ?>tipomascota/update/<?= $tipo->getIdTipo(); ?>" method="post"
                        class="needs-validation" novalidate>

                        <div class="mb-4">
                            <label for="nombre" class="form-label">Nombre de la Categoría:</label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fas fa-tag"></i>
                                </span>
                                <input type="text" class="form-control" id="nombre" name="nombre"
                                    value="<?= htmlspecialchars($tipo->getNombre()); ?>" required>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="descripcion" class="form-label">Descripción:</label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fas fa-align-left"></i>
                                </span>
                                <textarea class="form-control" id="descripcion" name="descripcion"
                                    rows="4"><?= htmlspecialchars($tipo->getDescripcion()); ?></textarea>
                            </div>
                            <div class="form-text">
                                Proporciona detalles sobre las características de esta categoría
                            </div>
                        </div>

                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            Los cambios en esta categoría afectarán a todas las mascotas asociadas.
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-save me-2"></i>Guardar Cambios
                            </button>
                            <a href="<?= RUTA; ?>tipomascota" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Volver al Listado
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>