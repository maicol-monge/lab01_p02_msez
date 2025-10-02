<?php if (!$mascota): ?>
    <div class="container py-3">
        <div class="alert alert-danger">Mascota no encontrada.</div>
    </div>
    <?php return; endif; ?>

<div class="container py-4">
    <a href="<?= RUTA; ?>mascota" class="btn btn-link p-0 mb-3"><i class="fas fa-arrow-left me-1"></i>Volver</a>
    <div class="card border-0 shadow-sm">
        <div class="card-body text-center">
            <h1 class="h4 mb-3">QR para <?= htmlspecialchars($mascota->getNomMascota()) ?></h1>
            <p class="text-muted mb-3">Escanea con la cámara del teléfono para ver la información de la mascota.</p>
            <?php
            // Generamos imagen de QR vía API de QuickChart (sin dependencia local)
            $qrApi = 'https://quickchart.io/qr?text=' . urlencode($url) . '&margin=1&size=320&dark=000000&light=ffffff';
            ?>
            <img src="<?= $qrApi ?>" alt="QR" class="img-fluid" style="max-width: 320px;" />
            <div class="mt-3">
                <div class="small text-muted">URL destino:</div>
                <div class="small"><a href="<?= htmlspecialchars($url) ?>"
                        target="_blank"><?= htmlspecialchars($url) ?></a></div>
            </div>
            <div class="mt-4">
                <button class="btn btn-primary" onclick="window.print()"><i
                        class="fas fa-print me-1"></i>Imprimir</button>
            </div>
        </div>
    </div>
</div>