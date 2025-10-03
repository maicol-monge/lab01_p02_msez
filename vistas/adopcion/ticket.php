<?php
// Requiere $detalle con campos: id_adopcion, fecha_adopcion, estado, usuario_nombre, usuario_correo, mascota_nombre, mascota_foto, tipo_nombre
?>
<!doctype html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Ticket #<?= htmlspecialchars($detalle['id_adopcion']) ?></title>
    <script src="https://cdn.jsdelivr.net/npm/html2canvas@1.4.1/dist/html2canvas.min.js"></script>
    <style>
        /* Ticket 80mm (~304px) compatible */
        :root {
            --w: 304px;
        }

        body {
            background: #f2f2f2;
            font-family: Arial, Helvetica, sans-serif;
        }

        .ticket {
            width: var(--w);
            margin: 10px auto;
            background: #fff;
            color: #000;
            padding: 12px;
            border: 1px dashed #aaa;
        }

        .center {
            text-align: center;
        }

        .bold {
            font-weight: bold;
        }

        .mt-1 {
            margin-top: 4px
        }

        .mt-2 {
            margin-top: 8px
        }

        .mt-3 {
            margin-top: 12px
        }

        .small {
            font-size: 12px
        }

        .xs {
            font-size: 10px
        }

        img.logo {
            max-width: 64px;
        }

        .line {
            border-top: 1px dashed #aaa;
            margin: 8px 0;
        }

        .kv {
            display: flex;
            justify-content: space-between;
            font-size: 12px;
        }

        .qr {
            display: flex;
            justify-content: center;
        }

        .actions {
            text-align: center;
            margin: 8px auto;
        }

        @media print {
            body {
                background: #fff;
            }

            .actions {
                display: none;
            }

            .ticket {
                border: none;
                box-shadow: none;
            }
        }
    </style>
</head>

<body>
    <div class="ticket">
        <div class="center">
            <img class="logo" src="<?= RUTA ?>img/logo.png" alt="Refugio" crossorigin="anonymous" />
            <div class="bold">Refugio Amigos Fieles</div>
            <div class="xs">RUC 00000000000</div>
            <div class="xs">Dirección: Av. Siempre Viva 123</div>
            <div class="xs">Tel: (01) 123-4567</div>
        </div>
        <div class="line"></div>

        <div class="kv"><span>Ticket</span><span class="bold">#<?= htmlspecialchars($detalle['id_adopcion']) ?></span>
        </div>
        <div class="kv"><span>Fecha</span><span><?= htmlspecialchars($detalle['fecha_adopcion']) ?></span></div>
        <div class="kv"><span>Estado</span><span><?= htmlspecialchars($detalle['estado']) ?></span></div>
        <div class="line"></div>

        <div class="bold small">Adoptante</div>
        <div class="small">Nombre: <?= htmlspecialchars($detalle['usuario_nombre']) ?></div>
        <div class="small">Correo: <?= htmlspecialchars($detalle['usuario_correo']) ?></div>
        <div class="line"></div>

        <div class="bold small">Mascota</div>
        <div class="small">Nombre: <?= htmlspecialchars($detalle['mascota_nombre']) ?></div>
        <div class="small">Tipo: <?= htmlspecialchars($detalle['tipo_nombre']) ?></div>
        <?php if (!empty($detalle['mascota_foto'])): ?>
            <div class="center mt-2"><img src="<?= htmlspecialchars($detalle['mascota_foto']) ?>" alt="Mascota"
                    crossorigin="anonymous" style="max-width:120px;max-height:120px;object-fit:cover" /></div>
        <?php endif; ?>
        <div class="line"></div>

        <div class="center small">Escanea para consultar la adopción</div>
        <div class="qr mt-1">
            <?php $qrUrl = RUTA . 'index.php?url=adopcion/ticket/' . urlencode($detalle['id_adopcion']);
            $qrImg = 'https://quickchart.io/qr?text=' . urlencode($qrUrl) . '&margin=1&size=160&format=png'; ?>
            <img id="qrImage" src="<?= $qrImg ?>" alt="QR" crossorigin="anonymous" />
        </div>

        <div class="center xs mt-3">Gracias por adoptar y cambiar una vida</div>
    </div>

    <div class="actions">
        <button onclick="window.print()">Imprimir</button>
        <button onclick="downloadTicket()">Descargar (PNG)</button>
    </div>

    <script>
        async function downloadTicket() {
            const el = document.querySelector('.ticket');
            // Asegura que imágenes externas no "tainten" el canvas usando CORS
            try {
                const canvas = await html2canvas(el, { backgroundColor: '#ffffff', scale: 2, useCORS: true });
                canvas.toBlob((blob) => {
                    if (!blob) { alert('No se pudo generar la imagen.'); return; }
                    const a = document.createElement('a');
                    a.href = URL.createObjectURL(blob);
                    a.download = 'ticket-<?= htmlspecialchars($detalle['id_adopcion']) ?>.png';
                    document.body.appendChild(a);
                    a.click();
                    a.remove();
                }, 'image/png');
            } catch (e) {
                alert('No se pudo descargar el ticket: ' + e.message);
            }
        }
    </script>
</body>

</html>