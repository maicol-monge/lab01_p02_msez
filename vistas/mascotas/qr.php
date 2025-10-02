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
            // Reescribir a front controller para asegurar layout completo
            $scanUrl = $url;
            // Si es detalle por id
            if (preg_match('#/cliente/mascota/\d+$#', $url)) {
                $id = preg_replace('#.*?/cliente/mascota/(\d+)$#', '$1', $url);
                $scanUrl = RUTA . 'index.php?url=cliente/mascota/' . $id;
            }
            // Si es QR por token
            if (strpos($url, 'cliente/qr') !== false && strpos($url, 'code=') !== false) {
                $scanUrl = RUTA . 'index.php?url=cliente/qr&' . parse_url($url, PHP_URL_QUERY);
            }
            // Generamos imagen de QR vía API de QuickChart (sin dependencia local)
            $qrApi = 'https://quickchart.io/qr?text=' . urlencode($scanUrl) . '&margin=1&size=320&dark=000000&light=ffffff';
            ?>
            <img src="<?= $qrApi ?>" alt="QR" class="img-fluid" style="max-width: 320px;" />
            <div class="mt-3">
                <div class="small text-muted">URL destino:</div>
                <div class="small"><a href="<?= htmlspecialchars($scanUrl) ?>"
                        target="_blank"><?= htmlspecialchars($scanUrl) ?></a></div>
            </div>
            <div class="mt-4">
                <button class="btn btn-primary" onclick="window.print()"><i
                        class="fas fa-print me-1"></i>Imprimir</button>
            </div>
        </div>
    </div>
</div>