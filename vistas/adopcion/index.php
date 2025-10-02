<?php
require_once "vistas/adopcion/index.php";
require_once "modelos/adopcionmodel.php";
$adopcionModel = new AdopcionModel();
$solicitudes = $adopcionModel->search(); // Puedes filtrar por estado si lo deseas

if (isset($_SESSION['usuario']) && $_SESSION['usuario']['rol'] === 'Administrador'):
    ?>
    <h2>Solicitudes de Adopción</h2>
    <div class="mb-3">
        <a class="btn btn-outline-secondary btn-sm" href="<?= RUTA; ?>adopcion/scan"><i class="fas fa-qrcode me-1"></i>
            Escanear/Buscar ticket</a>
    </div>
    <table class="table">
        <thead>
            <tr>
                <th>Cliente</th>
                <th>Mascota</th>
                <th>Fecha</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($solicitudes as $sol): ?>
                <tr>
                    <td><?= htmlspecialchars($sol['usuario_nombre']) ?></td>
                    <td><?= htmlspecialchars($sol['mascota_nombre']) ?></td>
                    <td><?= htmlspecialchars($sol['fecha_adopcion']) ?></td>
                    <td><?= htmlspecialchars($sol['estado']) ?></td>
                    <td>
                        <?php if ($sol['estado'] === 'Pendiente'): ?>
                            <form method="POST" action="index.php?url=adopcion/aprobar" style="display:inline;">
                                <input type="hidden" name="id_adopcion" value="<?= $sol['id_adopcion'] ?>">
                                <button type="submit" class="btn btn-success btn-sm">Aprobar</button>
                            </form>
                            <form method="POST" action="index.php?url=adopcion/rechazar" style="display:inline;">
                                <input type="hidden" name="id_adopcion" value="<?= $sol['id_adopcion'] ?>">
                                <button type="submit" class="btn btn-danger btn-sm">Rechazar</button>
                            </form>
                        <?php endif; ?>
                        <a class="btn btn-outline-primary btn-sm"
                            href="<?= RUTA; ?>adopcion/ticket/<?= $sol['id_adopcion'] ?>">Ticket</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php else: ?>
    <h2>Mis Solicitudes de Adopción</h2>
    <table class="table">
        <thead>
            <tr>
                <th>Mascota</th>
                <th>Fecha</th>
                <th>Estado</th>
                <th>Ticket</th>
            </tr>
        </thead>
        <tbody>
            <?php
            foreach ($solicitudes as $sol):
                if ($sol['id_usuario'] == $_SESSION['usuario']['id_usuario']):
                    ?>
                    <tr>
                        <td><?= htmlspecialchars($sol['mascota_nombre']) ?></td>
                        <td><?= htmlspecialchars($sol['fecha_adopcion']) ?></td>
                        <td><?= htmlspecialchars($sol['estado']) ?></td>
                        <td>
                            <?php if (in_array($sol['estado'], ['Aprobada', 'Rechazada'])): ?>
                                <a class="btn btn-outline-primary btn-sm"
                                    href="<?= RUTA; ?>adopcion/ticket/<?= $sol['id_adopcion'] ?>">Ver/imprimir</a>
                            <?php else: ?>
                                <span class="text-muted small">N/A</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php
                endif;
            endforeach;
            ?>
        </tbody>
    </table>
<?php endif; ?>