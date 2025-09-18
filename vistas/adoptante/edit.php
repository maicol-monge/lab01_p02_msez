<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <h1 class="h3 mb-4 text-center">
                        <i class="fas fa-edit text-primary me-2"></i>
                        Actualizar Información de Adopción
                    </h1>

                    <form action="<?= RUTA; ?>adoptante/update/<?= $adoptante->getIdAdoptante(); ?>" method="post"
                        class="needs-validation" novalidate>

                        <div class="mb-4">
                            <label for="nombre" class="form-label">Nombre del Adoptante:</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-user"></i></span>
                                <input type="text" class="form-control" id="nombre" name="nombre"
                                    value="<?= htmlspecialchars($adoptante->getNombre()); ?>" required>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="telefono" class="form-label">Teléfono de Contacto:</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-phone"></i></span>
                                <input type="tel" class="form-control" id="telefono" name="telefono"
                                    value="<?= htmlspecialchars($adoptante->getTelefono()); ?>">
                            </div>
                            <div class="form-text">Número de teléfono para seguimiento de la adopción</div>
                        </div>

                        <div class="mb-4">
                            <label for="id_mascota" class="form-label">Mascota a Adoptar:</label>
                            <select id="id_mascota" name="id_mascota" class="form-select">
                                <option value="">Sin asignar mascota</option>
                                <?php foreach ($mascotas as $m): ?>
                                    <?php $label = ($m['nombre'] ?? ('Mascota #' . $m['id_mascota'])) .
                                        (isset($m['tipo_nombre']) ? ' (' . $m['tipo_nombre'] . ')' : ''); ?>
                                    <option value="<?= $m['id_mascota']; ?>" <?= $adoptante->getIdMascota() == $m['id_mascota'] ? 'selected' : ''; ?>>
                                        <?= htmlspecialchars($label); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <div class="form-text">
                                Selecciona la mascota que será adoptada por esta persona
                            </div>
                        </div>

                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            Actualiza la información según sea necesario. Los cambios se registrarán en el sistema.
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-save me-2"></i>Guardar Cambios
                            </button>
                            <a href="<?= RUTA; ?>adoptante" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Volver al Listado
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>