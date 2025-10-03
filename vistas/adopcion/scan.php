<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h2 mb-0 d-flex align-items-center">
            <i class="fas fa-qrcode me-2 text-primary"></i>
            Buscar adopción por QR o ID
        </h1>
        <a href="<?= RUTA; ?>adopcion" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Volver
        </a>
    </div>

    <!-- Búsqueda manual por ID -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-light">
            <h5 class="card-title mb-0">
                <i class="fas fa-keyboard me-2"></i>Búsqueda manual
            </h5>
        </div>
        <div class="card-body">
            <form id="formBuscarTicket" class="row g-3" method="get" action="<?= RUTA; ?>adopcion/scan">
                <div class="col-md-8">
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="fas fa-hashtag"></i>
                        </span>
                        <input type="text" class="form-control" name="id" inputmode="numeric" pattern="\d*"
                            placeholder="Ingrese ID de adopción" value="<?= htmlspecialchars($_GET['id'] ?? '') ?>" />
                    </div>
                </div>
                <div class="col-md-4">
                    <button class="btn btn-primary w-100" type="submit">
                        <i class="fas fa-search me-2"></i>Abrir ticket
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Escaneo por foto -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-light">
            <h5 class="card-title mb-0 d-flex align-items-center">
                <i class="fas fa-camera me-2"></i>Escanear código QR del ticket
            </h5>
        </div>
        <div class="card-body">
            <div id="info" class="alert alert-info d-none"></div>

            <!-- Vista previa de cámara -->
            <div class="ratio ratio-1x1 mb-3" style="max-width:420px;margin:0 auto;">
                <video id="preview" playsinline
                    style="width:100%;height:100%;object-fit:cover;border-radius:.5rem;background:#000"></video>
            </div>div>

            <div class="text-center">
                <div class="mb-3">
                    <i class="fas fa-qrcode fa-3x text-muted mb-3"></i>
                    <p class="text-muted mb-1">Toma una foto del QR del ticket o elige una imagen.</p>
                    <small class="text-muted d-block">Consejos: buena luz, enfoque nítido y código completo en
                        pantalla.</small>
                </div>

                <div class="d-grid gap-2 justify-content-center align-items-center mb-2"
                    style="grid-auto-flow: column;">
                    <button id="btnStart" class="btn btn-outline-primary">
                        <i class="fas fa-play me-2"></i>Iniciar cámara
                    </button>
                    <button id="btnStop" class="btn btn-outline-secondary" disabled>
                        <i class="fas fa-stop me-2"></i>Detener cámara
                    </button>
                </div>

                <div class="d-grid gap-2 d-sm-flex justify-content-sm-center align-items-center">
                    <button id="btnPhoto" class="btn btn-primary btn-lg">
                        <span class="btn-label"><i class="fas fa-camera me-2"></i>Tomar/Elegir foto</span>
                        <span class="btn-loading d-none"><span class="spinner-border spinner-border-sm me-2"
                                role="status" aria-hidden="true"></span>Procesando…</span>
                    </button>
                </div>

                <?php
                // En Android, intentar abrir app ZXing con retorno directo al ticket
                $ret = urlencode(RUTA . 'index.php?url=adopcion/ticket/{CODE}');
                $play = urlencode('https://play.google.com/store/apps/details?id=com.google.zxing.client.android');
                $intent = "intent://scan/?ret={$ret}#Intent;scheme=zxing;package=com.google.zxing.client.android;S.browser_fallback_url={$play};end";
                ?>
                <div class="mt-2">
                    <a id="openNative" class="btn btn-outline-dark btn-sm d-none" href="<?= $intent ?>">
                        <i class="fas fa-external-link-alt me-1"></i> Escanear con app (Android)
                    </a>
                </div>

                <input id="fileInput" type="file" accept="image/*" capture="environment" class="d-none" />
                <canvas id="hiddenCanvas" class="d-none"></canvas>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/jsqr@1.4.0/dist/jsQR.js"></script>
<script src="https://cdn.jsdelivr.net/npm/exif-js"></script>
<script>
    const video = document.getElementById('preview');
    const btnStart = document.getElementById('btnStart');
    const btnStop = document.getElementById('btnStop');
    const btnPhoto = document.getElementById('btnPhoto');
    const fileInput = document.getElementById('fileInput');
    const info = document.getElementById('info');
    const canvas = document.getElementById('hiddenCanvas');
    const ctx = canvas.getContext('2d');
    const RUTA = '<?= RUTA; ?>';
    const openNative = document.getElementById('openNative');
    let mediaStream = null;
    let scanning = false;

    function showInfo(msg, type = 'info') {
        info.textContent = msg;
        info.className = 'alert alert-' + (type === 'error' ? 'danger' : type === 'success' ? 'success' : 'info');
        if (info.classList.contains('d-none')) info.classList.remove('d-none');
    }

    function setLoading(isLoading) {
        const btn = document.getElementById('btnPhoto');
        btn.disabled = isLoading;
        const lbl = btn.querySelector('.btn-label');
        const load = btn.querySelector('.btn-loading');
        if (lbl && load) {
            lbl.classList.toggle('d-none', isLoading);
            load.classList.toggle('d-none', !isLoading);
        }
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

    function getExifAngle(img) {
        return new Promise((resolve) => {
            let angle = 0;
            try {
                EXIF.getData(img, function () {
                    const ori = EXIF.getTag(this, 'Orientation');
                    if (ori === 3) angle = 180;
                    else if (ori === 6) angle = 90;
                    else if (ori === 8) angle = 270;
                    resolve(angle);
                });
            } catch (_) {
                resolve(0);
            }
        });
    }

    async function scanWithAttempts(img, exifAngle) {
        const sizes = [2048, 1600, 1280, 1024, 800, 640];
        const angles = [exifAngle, 0, 90, 180, 270].filter((v, i, a) => a.indexOf(v) === i);

        // Intentar con diferentes configuraciones de jsQR
        const options = [
            { inversionAttempts: "dontInvert" },
            { inversionAttempts: "onlyInvert" },
            { inversionAttempts: "attemptBoth" }
        ];

        for (const s of sizes) {
            for (const a of angles) {
                drawImageOriented(img, a, s);

                // Probar con diferentes opciones de inversión
                for (const opt of options) {
                    const imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);
                    const code = jsQR(imageData.data, imageData.width, imageData.height, opt);
                    if (code && code.data) return code.data.trim();
                }

                // También probar con mejora de contraste
                const imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);
                enhanceContrast(imageData);
                ctx.putImageData(imageData, 0, 0);
                const enhancedData = ctx.getImageData(0, 0, canvas.width, canvas.height);
                const code = jsQR(enhancedData.data, enhancedData.width, enhancedData.height);
                if (code && code.data) return code.data.trim();
            }
        }
        return null;
    }

    function enhanceContrast(imageData) {
        const data = imageData.data;
        const factor = 1.5; // Factor de contraste
        const intercept = 128 * (1 - factor);

        for (let i = 0; i < data.length; i += 4) {
            data[i] = Math.max(0, Math.min(255, data[i] * factor + intercept));     // R
            data[i + 1] = Math.max(0, Math.min(255, data[i + 1] * factor + intercept)); // G
            data[i + 2] = Math.max(0, Math.min(255, data[i + 2] * factor + intercept)); // B
        }
    }

    // ---- LECTURA DESDE CÁMARA ----
    async function startCamera() {
        if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
            showInfo('Tu navegador no soporta acceso a la cámara. Usa "Tomar/Elegir foto" o la app nativa.', 'error');
            if (openNative) openNative.classList.remove('d-none');
            return;
        }
        try {
            mediaStream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: { ideal: 'environment' } }, audio: false });
            video.srcObject = mediaStream;
            await video.play();
            scanning = true;
            btnStart.disabled = true;
            btnStop.disabled = false;
            showInfo('Apunta la cámara al QR del ticket…', 'info');
            requestAnimationFrame(scanFromVideo);
        } catch (e) {
            showInfo('No se pudo acceder a la cámara: ' + e.message + '. Usa "Tomar/Elegir foto" o la app nativa.', 'error');
            if (openNative) openNative.classList.remove('d-none');
        }
    }

    function stopCamera() {
        scanning = false;
        if (mediaStream) {
            mediaStream.getTracks().forEach(t => t.stop());
            mediaStream = null;
        }
        video.srcObject = null;
        btnStart.disabled = false;
        btnStop.disabled = true;
    }

    function scanFromVideo() {
        if (!scanning || !video || video.readyState < 2) {
            if (scanning) requestAnimationFrame(scanFromVideo);
            return;
        }
        const vw = video.videoWidth;
        const vh = video.videoHeight;
        if (!vw || !vh) {
            if (scanning) requestAnimationFrame(scanFromVideo);
            return;
        }
        canvas.width = vw;
        canvas.height = vh;
        ctx.drawImage(video, 0, 0, vw, vh);

        // Intentos con diferentes inversiones
        const imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);
        const attempts = [
            { inversionAttempts: 'dontInvert' },
            { inversionAttempts: 'onlyInvert' },
            { inversionAttempts: 'attemptBoth' }
        ];
        let result = null;
        for (const opt of attempts) {
            result = jsQR(imageData.data, imageData.width, imageData.height, opt);
            if (result && result.data) break;
        }
        if (result && result.data) {
            const data = result.data.trim();
            onQrDetected(data, true);
            return;
        }
        if (scanning) requestAnimationFrame(scanFromVideo);
    }

    function buildTicketUrlFromData(data) {
        if (/^\d+$/.test(data)) return RUTA + 'index.php?url=adopcion/ticket/' + data;
        const m = data.match(/adopcion\/ticket\/(\d+)(?:$|[\/?#])/);
        if (m && m[1]) return RUTA + 'index.php?url=adopcion/ticket/' + m[1];
        return data; // URL absoluta u otro path
    }

    function onQrDetected(data, fromCamera = false) {
        const dest = buildTicketUrlFromData(data);
        showInfo('QR detectado. Abriendo ticket…', 'success');
        try { info.insertAdjacentHTML('beforeend', '<div class="mt-2"><a class="alert-link" href="' + dest + '">Abrir ticket</a></div>'); } catch (_) { }
        if (fromCamera) stopCamera();
        setTimeout(() => { window.location.href = dest; }, 150);
    }

    // Búsqueda manual: abrir ticket de inmediato por front controller
    const form = document.getElementById('formBuscarTicket');
    if (form) {
        form.addEventListener('submit', (e) => {
            const input = form.querySelector('input[name="id"]');
            const val = (input?.value || '').trim();
            if (!/^\d+$/.test(val)) {
                e.preventDefault();
                showInfo('Ingrese un ID numérico válido.', 'error');
                return false;
            }
            // Evitar ida y vuelta al servidor: redirigir directo al ticket
            e.preventDefault();
            const dest = RUTA + 'index.php?url=adopcion/ticket/' + val;
            window.location.href = dest;
            return false;
        });
    }

    // Botones cámara
    if (btnStart) btnStart.addEventListener('click', startCamera);
    if (btnStop) btnStop.addEventListener('click', stopCamera);

    // Botón foto
    btnPhoto.addEventListener('click', () => fileInput.click());
    fileInput.addEventListener('change', (e) => {
        const file = e.target.files && e.target.files[0];
        if (!file) return;
        if (!file.type || !file.type.startsWith('image/')) {
            showInfo('El archivo seleccionado no es una imagen válida.', 'error');
            return;
        }
        setLoading(true);
        const img = new Image();
        img.onload = async () => {
            // Asegurar que las dependencias estén cargadas
            if (typeof jsQR !== 'function') {
                showInfo('No se pudo cargar el lector de QR. Verifique su conexión.', 'error');
                setLoading(false);
                return;
            }

            const exifAngle = await getExifAngle(img);

            const data = await scanWithAttempts(img, exifAngle);
            if (data) {
                onQrDetected((data || '').trim(), false);
            } else {
                showInfo('No se detectó un QR. Consejos: llene la pantalla con el código, buena iluminación y enfoque nítido. Intente otra toma más cercana.', 'error');
            }
            setLoading(false);
        };
        img.onerror = () => { showInfo('No se pudo leer la imagen seleccionada.', 'error'); setLoading(false); };
        img.src = URL.createObjectURL(file);
    });
</script>