<div class="container py-3">
    <h1 class="h4 mb-3"><i class="fas fa-qrcode me-2"></i>Escanear QR</h1>
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <div class="ratio ratio-1x1 mb-2" style="max-width:420px;margin:0 auto;">
                <video id="preview" playsinline
                    style="width:100%;height:100%;object-fit:cover;border-radius:.5rem;background:#000"></video>
            </div>
            <p class="small text-muted text-center">Apunta la cámara hacia el código QR de la mascota.</p>
            <div class="text-center">
                <button id="btnStop" class="btn btn-outline-secondary btn-sm" disabled>Detener</button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/jsqr@1.4.0/dist/jsQR.js"></script>
<div class="container py-3">
    <h1 class="h4 mb-3"><i class="fas fa-qrcode me-2"></i>Escanear QR</h1>
    <div id="info" class="alert alert-info d-none"></div>
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <div class="ratio ratio-1x1 mb-2" style="max-width:420px;margin:0 auto;">
                <video id="preview" playsinline
                    style="width:100%;height:100%;object-fit:cover;border-radius:.5rem;background:#000"></video>
            </div>
            <div class="text-center d-grid gap-2">
                <button id="btnStop" class="btn btn-outline-secondary btn-sm" disabled>Detener cámara</button>
                <button id="btnPhoto" class="btn btn-outline-primary btn-sm">Usar foto (capturar o elegir)</button>
                <input id="fileInput" type="file" accept="image/*" capture="environment" class="d-none" />
                <?php
                // ZXing intent: ret admite {CODE} que será reemplazado por el valor leído
                $ret = urlencode(RUTA . 'cliente/qr?code={CODE}');
                $play = urlencode('https://play.google.com/store/apps/details?id=com.google.zxing.client.android');
                $intent = "intent://scan/?ret={$ret}#Intent;scheme=zxing;package=com.google.zxing.client.android;S.browser_fallback_url={$play};end";
                ?>
                <a id="openNative" class="btn btn-outline-dark btn-sm d-none" href="<?= $intent ?>">Escanear con app
                    (Android)</a>
            </div>
            <canvas id="hiddenCanvas" class="d-none"></canvas>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/jsqr@1.4.0/dist/jsQR.js"></script>
<script>
    const video = document.getElementById('preview');
    <div class="container py-3">
        <h1 class="h4 mb-3"><i class="fas fa-qrcode me-2"></i>Escanear QR (foto)</h1>
        <div id="info" class="alert alert-info d-none"></div>
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center">
                <p class="text-muted">Toma una foto del código QR o elige una desde tu galería.</p>
                <button id="btnPhoto" class="btn btn-primary btn-lg"><i class="fas fa-camera me-2"></i>Tomar/Elegir foto</button>
                <input id="fileInput" type="file" accept="image/*" capture="environment" class="d-none" />
                <canvas id="hiddenCanvas" class="d-none"></canvas>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/jsqr@1.4.0/dist/jsQR.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/exif-js"></script>
<script>
        const btnPhoto = document.getElementById('btnPhoto');
        const fileInput = document.getElementById('fileInput');
        const info = document.getElementById('info');
        const canvas = document.getElementById('hiddenCanvas');
        const ctx = canvas.getContext('2d');

        function showInfo(msg, type='info') {
            info.textContent = msg;
            info.className = 'alert alert-' + (type === 'error' ? 'danger' : type === 'success' ? 'success' : 'info');
        }

        function degToRad(d) { return d * Math.PI / 180; }

        function drawImageOriented(img, angleDeg, maxW) {
            // Ajusta tamaño con escala
            const iw = img.naturalWidth || img.width;
            const ih = img.naturalHeight || img.height;
            const scale = Math.min(1, (maxW / Math.max(iw, ih)) || 1);
            const w = Math.max(1, Math.floor(iw * scale));
            const h = Math.max(1, Math.floor(ih * scale));

            const angle = ((angleDeg % 360) + 360) % 360; // normaliza
            const rot90 = angle === 90 || angle === 270;
            canvas.width = rot90 ? h : w;
            canvas.height = rot90 ? w : h;

            ctx.save();
            // mover al centro y rotar
            ctx.translate(canvas.width / 2, canvas.height / 2);
            ctx.rotate(degToRad(angle));
            // dibujar centrado
            const dw = w;
            const dh = h;
            ctx.drawImage(img, -dw / 2, -dh / 2, dw, dh);
            ctx.restore();
        }

        function tryScanOnce() {
            const imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);
            return jsQR(imageData.data, imageData.width, imageData.height);
        }

        async function scanWithAttempts(img, exifAngle) {
            const sizes = [1600, 1280, 1024, 800];
            const angles = [exifAngle, 0, 90, 180, 270].filter((v, i, a) => a.indexOf(v) === i); // únicos
            for (const s of sizes) {
                for (const a of angles) {
                    drawImageOriented(img, a, s);
                    const code = tryScanOnce();
                    if (code && code.data) return code.data.trim();
                }
            }
            return null;
        }

        // Modo foto (único método)
        btnPhoto.addEventListener('click', () => fileInput.click());
        fileInput.addEventListener('change', (e) => {
            const file = e.target.files && e.target.files[0];
            if (!file) return;
            const img = new Image();
            img.onload = async () => {
                // Obtener orientación EXIF si existe
                let exifAngle = 0;
                try {
                    EXIF.getData(img, function() {
                        const ori = EXIF.getTag(this, 'Orientation');
                        if (ori === 3) exifAngle = 180;
                        else if (ori === 6) exifAngle = 90;
                        else if (ori === 8) exifAngle = 270;
                    });
                } catch (_) {}

                const data = await scanWithAttempts(img, exifAngle);
                if (data) {
                    let url = data;
                    if (/^\d+$/.test(url)) url = '<?= RUTA; ?>cliente/confirmar/' + url;
                    window.location.href = url;
                } else {
                    showInfo('No se detectó un QR. Tips: llene la pantalla con el código, buena luz y enfoque. Prueba otra toma más cercana.', 'error');
                }
            };
            img.onerror = () => showInfo('No se pudo leer la imagen seleccionada.', 'error');
            img.src = URL.createObjectURL(file);
        });
</script>