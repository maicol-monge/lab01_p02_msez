<div class="container py-3">
    <h1 class="h4 mb-3"><i class="fas fa-paw me-2"></i>Explora mascotas</h1>

    <form action="<?= RUTA; ?>cliente" method="get" class="row g-2 mb-3">
        <div class="col-12">
            <input class="form-control form-control-lg" name="nombre" placeholder="Buscar por nombre"
                value="<?= htmlspecialchars($_GET['nombre'] ?? '') ?>" />
        </div>
        <div class="col-8">
            <select class="form-select form-select-lg" name="tipo">
                <option value="">Todos los tipos</option>
                <?php foreach ($tipos as $t): ?>
                    <option value="<?= $t->getIdTipo() ?>" <?= (!empty($_GET['tipo']) && $_GET['tipo'] == $t->getIdTipo()) ? 'selected' : '' ?>><?= htmlspecialchars($t->getNombre()) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-4 d-grid">
            <button class="btn btn-primary btn-lg" type="submit"><i class="fas fa-search me-1"></i>Buscar</button>
        </div>
    </form>

    <div class="d-flex justify-content-between align-items-center mb-2">
        <small class="text-muted">Resultados: <?= count($mascotas) ?></small>
        <a class="btn btn-outline-secondary btn-sm" href="<?= RUTA; ?>cliente/scan"><i
                class="fas fa-qrcode me-1"></i>Escanear QR</a>
    </div>

    <?php if (empty($mascotas)): ?>
        <div class="alert alert-info">No encontramos mascotas disponibles con esos filtros.</div>
    <?php else: ?>
        <div class="row row-cols-1 g-3">
            <?php foreach ($mascotas as $m): ?>
                <div class="col">
                    <div class="card border-0 shadow-sm">
                        <?php if ($m->getFoto()): ?>
                            <img src="<?= htmlspecialchars($m->getFoto()) ?>" class="card-img-top"
                                style="height:200px;object-fit:cover" />
                        <?php endif; ?>
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <h5 class="card-title mb-1"><?= htmlspecialchars($m->getNomMascota()) ?></h5>
                                <span class="badge bg-success">Disponible</span>
                            </div>
                            <div class="small text-muted mb-2">
                                <i
                                    class="fas fa-tag me-1"></i><?= $m->getTipo() ? htmlspecialchars($m->getTipo()->getNombre()) : 'General' ?>
                            </div>
                            <div class="d-grid gap-2">
                                <a href="<?= RUTA; ?>cliente/mascota/<?= $m->getIdmascota() ?>" class="btn btn-outline-primary">
                                    <i class="fas fa-info-circle me-1"></i>Ver detalles
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>