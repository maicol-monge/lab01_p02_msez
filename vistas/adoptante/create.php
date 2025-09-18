<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <h1 class="h3 mb-4 text-center">
                        <i class="fas fa-heart text-primary me-2"></i>
                        Iniciar Proceso de Adopción
                    </h1>

                    <form action="<?= RUTA; ?>adoptante/store" method="post" class="needs-validation" novalidate>
                        <div class="mb-4">
                            <label for="nombre" class="form-label">Nombre Completo:</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-user"></i></span>
                                <input type="text" class="form-control" id="nombre" name="nombre" required
                                    placeholder="Ingresa tu nombre completo">
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="telefono" class="form-label">Teléfono de Contacto:</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-phone"></i></span>
                                <input type="tel" class="form-control" id="telefono" name="telefono"
                                    placeholder="Ingresa tu número de teléfono">
                            </div>
                            <div class="form-text">Nos comunicaremos contigo para coordinar la adopción</div>
                        </div>

                        <div class="mb-4">
                            <label for="id_mascota" class="form-label">Mascota que Deseas Adoptar:</label>
                            <select id="id_mascota" name="id_mascota" class="form-select">
                                <option value="">Selecciona una mascota</option>
                                <?php foreach ($mascotas as $m): ?>
                                    <?php $label = ($m['nombre'] ?? ('Mascota #' . $m['id_mascota'])) .
                                        (isset($m['tipo_nombre']) ? ' (' . $m['tipo_nombre'] . ')' : ''); ?>
                                    <option value="<?= $m['id_mascota']; ?>">
                                        <?= htmlspecialchars($label); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <div class="form-text">Puedes seleccionar la mascota ahora o más tarde</div>
                        </div>

                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            Al enviar este formulario, inicias el proceso de adopción. Nos pondremos en contacto contigo
                            para los siguientes pasos.
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-heart me-2"></i>Iniciar Adopción
                            </button>
                            <a href="<?= RUTA; ?>adoptante" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Volver
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>