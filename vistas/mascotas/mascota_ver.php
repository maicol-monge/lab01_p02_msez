<?php
<?php if ($mascota): ?>
    <div class="card mb-4">
        <div class="row g-0">
            <div class="col-md-4">
                <img src="<?= htmlspecialchars($mascota['foto']) ?>" class="img-fluid rounded-start" alt="Foto de la mascota">
            </div>
            <div class="col-md-8">
                <div class="card-body">
                    <h3 class="card-title"><?= htmlspecialchars($mascota['nombre']) ?></h3>
                    <p class="card-text"><strong>Tipo:</strong> <?= htmlspecialchars($mascota['tipo_nombre']) ?></p>
                    <p class="card-text"><strong>Estado de adopción:</strong> <?= htmlspecialchars($mascota['estado_adopcion']) ?></p>
                    <p class="card-text"><strong>Descripción:</strong> <?= htmlspecialchars($mascota['descripcion'] ?? '') ?></p>
                    <?php if (
                        isset($_SESSION['usuario']) &&
                        $_SESSION['usuario']['rol'] === 'Cliente' &&
                        $mascota['estado_adopcion'] === 'Disponible'
                    ): ?>
                        <form method="POST" action="index.php?url=adopcion/adoptar">
                            <input type="hidden" name="id_mascota" value="<?= $mascota['id_mascota'] ?>">
                            <button type="submit" class="btn btn-success">Adoptar Mascota</button>
                        </form>
                    <?php elseif ($mascota['estado_adopcion'] !== 'Disponible'): ?>
                        <div class="alert alert-warning mt-3">Esta mascota ya fue adoptada.</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
<?php else: ?>
    <div class="alert alert-danger">Mascota no encontrada.</div>
<?php endif; ?>