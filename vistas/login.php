<?php
if (isset($error)) {
    echo "<div class='alert alert-danger'>$error</div>";
}
?>
<div class="row justify-content-center">
    <div class="col-md-4">
        <h2>Iniciar sesión</h2>
        <form method="POST" action="index.php?url=login/autenticar">
            <div class="mb-3">
                <label for="correo" class="form-label">Correo:</label>
                <input type="email" name="correo" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Contraseña:</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary">Ingresar</button>
            <a href="<?= RUTA; ?>login/registrar" class="btn btn-link">¿No tienes cuenta? Regístrate</a>
        </form>
    </div>
</div>

<!-- El navbar ya maneja los enlaces de sesión -->