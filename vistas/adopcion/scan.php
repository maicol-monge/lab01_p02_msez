<div class="container py-3">
    <h1 class="h5 mb-3"><i class="fas fa-qrcode me-2"></i>Buscar adopción por QR/ID</h1>

    <!-- Búsqueda manual por ID (alternativa) -->
    <form class="row g-2 mb-3" method="get" action="<?= RUTA; ?>adopcion/scan">
        <div class="col-8"><input type="text" class="form-control" name="id" placeholder="ID de adopción" /></div>
        <div class="col-4 d-grid"><button class="btn btn-outline-primary" type="submit">Abrir ticket</button></div>
    </form>

    <!-- Escaneo por foto -->
    <div id="info" class="alert alert-info d-none"></div>
    <div class="card border-0 shadow-sm">
        <div class="card-body text-center">
            <p class="text-muted">Toma una foto del código QR del ticket o elige una desde tu galería.</p>
            <button id="btnPhoto" class="btn btn-primary btn-lg">
                <i class="fas fa-camera me-2"></i>Tomar/Elegir foto
            </button>
            <input id="fileInput" type="file" accept="image/*" capture="environment" class="d-none" />
            <canvas id="hiddenCanvas" class="d-none"></canvas>
        </div>
    </div>

    <div class="mt-3">
        <a class="btn btn-outline-secondary" href="<?= RUTA; ?>adopcion">Volver</a>
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

    function showInfo(msg, type = 'info') {
        info.textContent = msg;
        info.className = 'alert alert-' + (type === 'error' ? 'danger' : type === 'success' ? 'success' : 'info');
    }

    function degToRad(d) { return d * Math.PI / 180; }

    function drawImageOriented(img, angleDeg, maxW) {
        const iw = img.naturalWidth || img.width;
        const ih = img.naturalHeight || img.height;
        const scale = Math.min(1, (maxW / Math.max(iw, ih)) || 1);
        const w = Math.max(1, Math.floor(iw * scale));
        const h = Math.max(1, Math.floor(ih * scale));
        const angle = ((angleDeg % 360) + 360) % 360;
        const rot90 = angle === 90 || angle === 270;
        canvas.width = rot90 ? h : w;
        canvas.height = rot90 ? w : h;
        ctx.save();
        ctx.translate(canvas.width / 2, canvas.height / 2);
        ctx.rotate(degToRad(angle));
        ctx.drawImage(img, -w / 2, -h / 2, w, h);
        ctx.restore();
    }

    function tryScanOnce() {
        const imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);
        return jsQR(imageData.data, imageData.width, imageData.height);
    }

    async function scanWithAttempts(img, exifAngle) {
        const sizes = [1600, 1280, 1024, 800];
        const angles = [exifAngle, 0, 90, 180, 270].filter((v, i, a) => a.indexOf(v) === i);
        for (const s of sizes) {
            for (const a of angles) {
                drawImageOriented(img, a, s);
                const code = tryScanOnce();
                if (code && code.data) return code.data.trim();
            }
        }
        return null;
    }

    btnPhoto.addEventListener('click', () => fileInput.click());
    fileInput.addEventListener('change', (e) => {
        const file = e.target.files && e.target.files[0];
        if (!file) return;
        const img = new Image();
        img.onload = async () => {
            let exifAngle = 0;
            try {
                EXIF.getData(img, function () {
                    const ori = EXIF.getTag(this, 'Orientation');
                    if (ori === 3) exifAngle = 180;
                    else if (ori === 6) exifAngle = 90;
                    else if (ori === 8) exifAngle = 270;
                });
            } catch (_) { }

            const data = await scanWithAttempts(img, exifAngle);
            if (data) {
                let url = data;
                // Si solo trae un ID, construir URL de ticket por front controller
                if (/^\d+$/.test(url)) url = '<?= RUTA; ?>index.php?url=adopcion/ticket/' + url;
                // Si es URL a ticket sin pasar por front controller, reescribir
                if (/adopcion\/ticket\/\d+$/.test(url) && !/index\.php\?url=/.test(url)) {
                    const id = url.match(/adopcion\/ticket\/(\d+)$/)[1];
                    url = '<?= RUTA; ?>index.php?url=adopcion/ticket/' + id;
                }
                window.location.href = url;
            } else {
                showInfo('No se detectó un QR. Tips: llene la pantalla con el código, buena luz y enfoque. Prueba otra toma más cercana.', 'error');
            }
        };
        img.onerror = () => showInfo('No se pudo leer la imagen seleccionada.', 'error');
        img.src = URL.createObjectURL(file);
    });
</script>