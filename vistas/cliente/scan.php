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
    const btnStop = document.getElementById('btnStop');
    const btnPhoto = document.getElementById('btnPhoto');
    const fileInput = document.getElementById('fileInput');
    const info = document.getElementById('info');
    const canvas = document.getElementById('hiddenCanvas');
    const ctx = canvas.getContext('2d');
    let stream = null, rafId = null;

    // Polyfill para getUserMedia con prefijos antiguos
    (function polyfillGetUserMedia() {
        if (!navigator.mediaDevices) navigator.mediaDevices = {};
        if (!navigator.mediaDevices.getUserMedia) {
            navigator.mediaDevices.getUserMedia = function (constraints) {
                const getUM = navigator.getUserMedia || navigator.webkitGetUserMedia || navigator.mozGetUserMedia;
                if (!getUM) return Promise.reject(new Error('getUserMedia no soportado'));
                return new Promise((resolve, reject) => getUM.call(navigator, constraints, resolve, reject));
            }
        }
    })();

    const isSecure = location.protocol === 'https:' || location.hostname === 'localhost' || location.hostname === '127.0.0.1';

    async function startCamera() {
        try {
            if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) throw new Error('API no disponible');
            stream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: { ideal: 'environment' } }, audio: false });
            video.srcObject = stream;
            await video.play();
            btnStop.disabled = false;
            scanLoop();
        } catch (e) {
            showInfo('No se pudo acceder a la cámara. Puedes usar el modo foto para escanear el código.\nDetalle: ' + e.message);
        }
    }

    function stopCamera() {
        if (rafId) cancelAnimationFrame(rafId);
        if (stream) { stream.getTracks().forEach(t => t.stop()); stream = null; }
        btnStop.disabled = true;
    }

    function scanLoop() {
        if (video.readyState === video.HAVE_ENOUGH_DATA) {
            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;
            ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
            const imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);
            const code = jsQR(imageData.data, imageData.width, imageData.height);
            if (code && code.data) return handleCode(code.data);
        }
        rafId = requestAnimationFrame(scanLoop);
    }

    function handleCode(data) {
        stopCamera();
        let url = data;
        if (/^\d+$/.test(url)) url = '<?= RUTA; ?>cliente/mascota/' + url;
        window.location.href = url;
    }

    function showInfo(msg) {
        info.textContent = msg;
        info.classList.remove('d-none');
    }

    // Modo foto (funciona en HTTP y navegadores sin getUserMedia)
    btnPhoto.addEventListener('click', () => fileInput.click());
    fileInput.addEventListener('change', async (e) => {
        const file = e.target.files && e.target.files[0];
        if (!file) return;
        const img = new Image();
        img.onload = () => {
            // Redimensiona canvas a la imagen cargada
            canvas.width = img.naturalWidth;
            canvas.height = img.naturalHeight;
            ctx.drawImage(img, 0, 0, canvas.width, canvas.height);
            const imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);
            const code = jsQR(imageData.data, imageData.width, imageData.height);
            if (code && code.data) {
                handleCode(code.data);
            } else {
                showInfo('No se detectó un QR en la imagen. Intenta de nuevo con una foto más cercana y bien iluminada.');
            }
        };
        img.onerror = () => showInfo('No se pudo leer la imagen seleccionada.');
        img.src = URL.createObjectURL(file);
    });

    btnStop.addEventListener('click', stopCamera);
    document.addEventListener('visibilitychange', () => { if (document.hidden) stopCamera(); });

    // Estrategia: si el contexto no es seguro (HTTP en IP), forzamos modo foto y avisamos.
    if (!isSecure) {
        showInfo('Por seguridad del navegador, la cámara del navegador solo funciona en HTTPS o localhost. Puedes: 1) usar el botón "Usar foto", o 2) imprimir QR y escanear con la cámara nativa.');
    } else {
        startCamera();
    }

    // Mostrar botón de app nativa en Android
    const isAndroid = /Android/i.test(navigator.userAgent);
    if (isAndroid) {
        document.getElementById('openNative').classList.remove('d-none');
    }
</script>