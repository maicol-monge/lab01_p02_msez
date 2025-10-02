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
        </form>
    </div>
</div>

<?php if (isset($_SESSION['usuario'])): ?>
    <li class="nav-item">
        <span class="nav-link">Hola, <?= $_SESSION['usuario']['nombre']; ?> (<?= $_SESSION['usuario']['rol']; ?>)</span>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="<?= RUTA; ?>login/logout">Cerrar sesión</a>
    </li>
<?php else: ?>
    <li class="nav-item">
        <a class="nav-link" href="<?= RUTA; ?>login">Iniciar sesión</a>
    </li>
<?php endif; ?>