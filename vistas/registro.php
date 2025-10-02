<?php
// Variables opcionales: $error (string) y $old (array con 'nombre' y 'correo')
?>
<div class="row justify-content-center">
    <div class="col-md-5">
        <h2>Crear cuenta</h2>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><?= $error; ?></div>
        <?php endif; ?>

        <form method="POST" action="?url=login/guardarRegistro">
            <div class="mb-3">
                <label for="nombre" class="form-label">Nombre completo</label>
                <input type="text" class="form-control" id="nombre" name="nombre" required
                    value="<?= htmlspecialchars($old['nombre'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
            </div>
            <div class="mb-3">
                <label for="correo" class="form-label">Correo</label>
                <input type="email" class="form-control" id="correo" name="correo" required
                    value="<?= htmlspecialchars($old['correo'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Contraseña</label>
                <input type="password" class="form-control" id="password" name="password" required minlength="6">
            </div>
            <div class="mb-3">
                <label for="password2" class="form-label">Repetir contraseña</label>
                <input type="password" class="form-control" id="password2" name="password2" required minlength="6">
            </div>
            <button type="submit" class="btn btn-primary">Registrarme</button>
            <a href="<?= RUTA; ?>login" class="btn btn-link">Ya tengo cuenta</a>
        </form>
    </div>
    <div class="col-md-5 d-none d-md-block">
        <div class="p-4 bg-light rounded h-100">
            <h5>Beneficios de registrarte</h5>
            <ul>
                <li>Postula para adoptar una mascota.</li>
                <li>Haz seguimiento a tus solicitudes.</li>
                <li>Recibe noticias del refugio.</li>
            </ul>
        </div>
    </div>

</div>