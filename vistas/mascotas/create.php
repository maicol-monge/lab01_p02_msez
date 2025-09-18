<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <h1 class="h3 mb-4 text-center">
                        <i class="fas fa-plus-circle text-primary me-2"></i>
                        Agregar Nueva Mascota
                    </h1>

                    <form action="<?= RUTA; ?>mascota/store" method="post">
                        <div class="mb-4">
                            <label for="id_tipo" class="form-label">Tipo de Mascota:</label>
                            <select id="id_tipo" name="id_tipo" class="form-select" required>
                                <option value="" disabled selected>Selecciona un tipo</option>
                                <?php foreach ($tipos as $t): ?>
                                    <option value="<?= $t->getIdTipo(); ?>">
                                        <?= htmlspecialchars($t->getNombre()); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="mb-4">
                            <label for="nombre" class="form-label">Nombre de la Mascota:</label>
                            <input type="text" class="form-control" id="nombre" name="nombre" required
                                placeholder="Ingresa el nombre">
                        </div>

                        <div class="mb-4">
                            <label for="foto" class="form-label">URL de la Foto:</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-camera"></i></span>
                                <input type="url" class="form-control" id="foto" name="foto"
                                    placeholder="https://ejemplo.com/foto.jpg">
                            </div>
                            <div class="form-text">Ingresa la URL de una imagen de la mascota</div>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Guardar Mascota
                            </button>
                            <a href="<?= RUTA; ?>mascota" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Volver al Listado
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>