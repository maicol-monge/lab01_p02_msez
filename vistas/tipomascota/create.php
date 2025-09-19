<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <h1 class="h3 mb-4 text-center">
                        <i class="fas fa-plus-circle text-primary me-2"></i>
                        Nueva Categoría de Mascota
                    </h1>

                    <form action="<?= RUTA; ?>tipomascota/store" method="post" class="needs-validation" novalidate>
                        <div class="mb-4">
                            <label for="nombre" class="form-label">Nombre de la Categoría:</label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fas fa-tag"></i>
                                </span>
                                <input type="text" class="form-control" id="nombre" name="nombre" required
                                    placeholder="Ej: Perros, Gatos, Aves...">
                            </div>
                            <div class="form-text">
                                Ingresa un nombre descriptivo para esta categoría de mascotas
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="descripcion" class="form-label">Descripción:</label>
                            <div class="input-group has-validation">
                                <span class="input-group-text">
                                    <i class="fas fa-align-left"></i>
                                </span>
                                <textarea class="form-control" id="descripcion" name="descripcion" rows="4"
                                    placeholder="Describe las características generales de esta categoría..."
                                    required></textarea>
                                <div class="invalid-feedback">
                                    Por favor ingresa una descripción para la categoría.
                                </div>
                            </div>
                            <div class="form-text">
                                Agrega información útil sobre este tipo de mascota
                            </div>
                        </div>

                        <script>
                            // Example starter JavaScript for disabling form submissions if there are invalid fields
                            (function () {
                                'use strict'

                                // Fetch all the forms we want to apply custom Bootstrap validation styles to
                                var forms = document.querySelectorAll('.needs-validation')

                                // Loop over them and prevent submission
                                Array.prototype.slice.call(forms)
                                    .forEach(function (form) {
                                        form.addEventListener('submit', function (event) {
                                            if (!form.checkValidity()) {
                                                event.preventDefault()
                                                event.stopPropagation()
                                            }

                                            form.classList.add('was-validated')
                                        }, false)
                                    })
                            })()
                        </script>

                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            Las categorías ayudan a organizar mejor las mascotas y facilitan la búsqueda para los
                            adoptantes.
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-save me-2"></i>Guardar Categoría
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