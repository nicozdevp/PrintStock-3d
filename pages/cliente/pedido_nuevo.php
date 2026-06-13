<?php
require_once __DIR__ . '/../../includes/auth.php';
requireRole('cliente');
require_once __DIR__ . '/../../config/database.php';

$productos = $conexion->query('SELECT * FROM productos WHERE stock > 0 ORDER BY nombre')->fetchAll(PDO::FETCH_ASSOC);
$error     = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    validateCsrf();

    $clienteId  = (int) $_SESSION['usuario_id'];
    $notas      = trim($_POST['notas'] ?? '');
    $cantidades = $_POST['cantidad'] ?? [];

    $items = [];
    foreach ($cantidades as $prodId => $qty) {
        $qty = (int) $qty;
        if ($qty > 0) {
            $items[(int)$prodId] = $qty;
        }
    }

    if (empty($items)) {
        $error = 'Selecciona al menos un producto y una cantidad mayor a 0.';
    } else {
        $conexion->beginTransaction();
        try {
            $conexion->prepare('INSERT INTO pedidos (cliente_id, notas, total) VALUES (?, ?, 0)')
                     ->execute([$clienteId, $notas]);
            $pedidoId = (int) $conexion->lastInsertId();

            $total    = 0.0;
            $stmtItem  = $conexion->prepare('INSERT INTO pedido_items (pedido_id, producto_id, cantidad, precio_unitario) VALUES (?, ?, ?, ?)');
            $stmtStock = $conexion->prepare('UPDATE productos SET stock = stock - ? WHERE id = ? AND stock >= ?');

            foreach ($items as $prodId => $qty) {
                $prod = $conexion->prepare('SELECT nombre, precio, stock FROM productos WHERE id = ?');
                $prod->execute([$prodId]);
                $p = $prod->fetch(PDO::FETCH_ASSOC);

                if (!$p || $p['stock'] < $qty) {
                    throw new Exception('Stock insuficiente para: ' . ($p['nombre'] ?? "#$prodId"));
                }
                $stmtItem->execute([$pedidoId, $prodId, $qty, $p['precio']]);
                $stmtStock->execute([$qty, $prodId, $qty]);
                $total += $p['precio'] * $qty;
            }

            $conexion->prepare('UPDATE pedidos SET total = ? WHERE id = ?')->execute([$total, $pedidoId]);
            $conexion->commit();
            header('Location: mis_pedidos.php');
            exit();
        } catch (Exception $e) {
            $conexion->rollBack();
            $error = $e->getMessage();
        }
    }
}
?>
<?php include __DIR__ . '/../../includes/header.php'; ?>

<div class="container mt-5">
    <h2 class="mb-4">Hacer un pedido</h2>

    <?php if (!empty($error)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <?php if (empty($productos)): ?>
        <div class="alert alert-warning">No hay productos disponibles en este momento.</div>
    <?php else: ?>
    <form method="POST">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrfToken()) ?>">

        <div class="card mb-4">
            <div class="card-header fw-bold">Selecciona tus productos</div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr><th>Producto</th><th>Descripción</th><th>Precio</th><th>Disponible</th><th style="width:110px">Cantidad</th></tr>
                    </thead>
                    <tbody>
                        <?php foreach ($productos as $p): ?>
                        <tr>
                            <td class="fw-bold"><?= htmlspecialchars($p['nombre']) ?></td>
                            <td class="text-muted small"><?= htmlspecialchars($p['descripcion'] ?? '') ?></td>
                            <td class="text-success fw-bold">$<?= number_format((float)$p['precio'], 2) ?></td>
                            <td><span class="badge bg-info text-dark"><?= (int)$p['stock'] ?></span></td>
                            <td>
                                <input type="number" name="cantidad[<?= $p['id'] ?>]"
                                       class="form-control form-control-sm"
                                       min="0" max="<?= (int)$p['stock'] ?>" value="0">
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mb-4" style="max-width:500px">
            <label class="form-label">Notas adicionales (opcional)</label>
            <textarea name="notas" class="form-control" rows="2" placeholder="Instrucciones especiales, colores, tamaños, etc."></textarea>
        </div>

        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-success btn-lg">✅ Confirmar pedido</button>
            <a href="catalogo.php" class="btn btn-outline-secondary btn-lg">Ver catálogo</a>
        </div>
    </form>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/../../includes/footer.php'; ?>
