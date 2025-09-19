<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <h1 class="h3 mb-4 text-center">
                        <i class="fas fa-edit text-primary me-2"></i>
                        Editar Mascota
                    </h1>

                    <form action="<?= RUTA; ?>mascota/update/<?php echo $mascota->getIdmascota(); ?>" method="post">
                        <div class="mb-4">
                            <label for="id_tipo" class="form-label">Tipo de Mascota:</label>
                            <div class="input-group mb-2">
                                <span class="input-group-text">
                                    <i class="fas fa-search"></i>
                                </span>
                                <input type="text" class="form-control" id="buscarTipo"
                                    placeholder="Buscar tipo de mascota...">
                            </div>
                            <select id="id_tipo" name="id_tipo" class="form-select" required>
                                <?php foreach ($tipos as $t): ?>
                                    <option value="<?= $t->getIdTipo(); ?>"
                                        data-nombre="<?= htmlspecialchars(strtolower($t->getNombre())); ?>"
                                        data-descripcion="<?= htmlspecialchars(strtolower($t->getDescripcion() ?? '')); ?>"
                                        <?= ($t->getIdTipo() == $mascota->getIdTipo()) ? 'selected' : ''; ?>>
                                        <?= htmlspecialchars($t->getNombre()); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <div class="form-text">
                                Puedes buscar por nombre o descripción del tipo de mascota
                            </div>

                            <script>
                                document.addEventListener('DOMContentLoaded', function () {
                                    const buscarInput = document.getElementById('buscarTipo');
                                    const selectTipo = document.getElementById('id_tipo');
                                    const opciones = Array.from(selectTipo.options);

                                    buscarInput.addEventListener('input', function (e) {
                                        const busqueda = e.target.value.toLowerCase().trim();

                                        opciones.forEach(opcion => {
                                            const nombre = opcion.getAttribute('data-nombre') || '';
                                            const descripcion = opcion.getAttribute('data-descripcion') || '';
                                            const coincide = nombre.includes(busqueda) ||
                                                descripcion.includes(busqueda);

                                            opcion.style.display = coincide ? '' : 'none';
                                        });
                                    });
                                });
                            </script>
                        </div>

                        <div class="mb-4">
                            <label for="nombre" class="form-label">Nombre de la Mascota:</label>
                            <input type="text" class="form-control" id="nombre" name="nombre"
                                value="<?= htmlspecialchars($mascota->getNomMascota()); ?>" required>
                        </div>

                        <div class="mb-4">
                            <label for="foto" class="form-label">URL de la Foto:</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-camera"></i></span>
                                <input type="url" class="form-control" id="foto" name="foto"
                                    value="<?= htmlspecialchars($mascota->getFoto()); ?>">
                            </div>
                            <div class="form-text">Ingresa la URL de una imagen de la mascota</div>
                        </div>

                        <?php if ($mascota->getFoto()): ?>
                            <div class="mb-4">
                                <label class="form-label">Vista Previa:</label>
                                <img src="<?= htmlspecialchars($mascota->getFoto()); ?>" class="img-fluid rounded"
                                    style="max-height: 200px" alt="Vista previa">
                            </div>
                        <?php endif; ?>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Actualizar Mascota
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