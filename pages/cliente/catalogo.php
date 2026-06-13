<?php
require_once __DIR__ . '/../../includes/auth.php';
requireAuth();
require_once __DIR__ . '/../../config/database.php';

$categorias = $conexion->query("SELECT DISTINCT categoria FROM productos WHERE categoria IS NOT NULL AND categoria != '' ORDER BY categoria")->fetchAll(PDO::FETCH_COLUMN);
$filtro     = $_GET['categoria'] ?? '';

if ($filtro !== '') {
    $stmt = $conexion->prepare('SELECT * FROM productos WHERE stock > 0 AND categoria = ? ORDER BY nombre');
    $stmt->execute([$filtro]);
    $productos = $stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    $productos = $conexion->query('SELECT * FROM productos WHERE stock > 0 ORDER BY nombre')->fetchAll(PDO::FETCH_ASSOC);
}
?>
<?php include __DIR__ . '/../../includes/header.php'; ?>

<div class="container mt-5">
    <h2 class="mb-4">Catálogo de Piezas 3D</h2>

    <?php if (!empty($categorias)): ?>
    <div class="mb-4 d-flex gap-2 flex-wrap">
        <a href="catalogo.php" class="btn btn-sm <?= $filtro === '' ? 'btn-dark' : 'btn-outline-dark' ?>">Todos</a>
        <?php foreach ($categorias as $cat): ?>
            <a href="catalogo.php?categoria=<?= urlencode($cat) ?>"
               class="btn btn-sm <?= $filtro === $cat ? 'btn-dark' : 'btn-outline-dark' ?>">
                <?= htmlspecialchars($cat) ?>
            </a>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <?php if (empty($productos)): ?>
        <div class="alert alert-info">No hay productos disponibles<?= $filtro ? " en la categoría «$filtro»" : '' ?>.</div>
    <?php else: ?>
        <div class="row row-cols-1 row-cols-md-3 row-cols-lg-4 g-4">
            <?php foreach ($productos as $p): ?>
            <div class="col">
                <div class="card h-100 shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title"><?= htmlspecialchars($p['nombre']) ?></h5>
                        <?php if ($p['categoria']): ?>
                            <span class="badge bg-secondary mb-2"><?= htmlspecialchars($p['categoria']) ?></span>
                        <?php endif; ?>
                        <?php if ($p['descripcion']): ?>
                            <p class="card-text text-muted small"><?= htmlspecialchars($p['descripcion']) ?></p>
                        <?php endif; ?>
                    </div>
                    <div class="card-footer d-flex justify-content-between align-items-center">
                        <span class="fs-5 fw-bold text-success">$<?= number_format((float)$p['precio'], 2) ?></span>
                        <span class="badge bg-info text-dark"><?= (int)$p['stock'] ?> en stock</span>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <?php if (hasRole('cliente')): ?>
        <div class="text-center mt-5">
            <a href="pedido_nuevo.php" class="btn btn-success btn-lg">🛒 Hacer un pedido</a>
        </div>
        <?php endif; ?>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/../../includes/footer.php'; ?>
