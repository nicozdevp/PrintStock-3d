<?php
require_once __DIR__ . '/../../includes/auth.php';
requireRole('vendedor', 'admin');
require_once __DIR__ . '/../../config/database.php';

$clientes  = $conexion->query("SELECT id, nombre, email FROM usuarios WHERE rol = 'cliente' ORDER BY nombre")->fetchAll(PDO::FETCH_ASSOC);
$productos = $conexion->query("SELECT * FROM productos WHERE stock > 0 ORDER BY nombre")->fetchAll(PDO::FETCH_ASSOC);
$error     = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    validateCsrf();

    $clienteId  = (int) ($_POST['cliente_id'] ?? 0);
    $vendedorId = (int) $_SESSION['usuario_id'];
    $notas      = trim($_POST['notas'] ?? '');
    $cantidades = $_POST['cantidad'] ?? [];

    if ($clienteId <= 0) {
        $error = 'Debes seleccionar un cliente.';
    } else {
        $items = [];
        foreach ($cantidades as $prodId => $qty) {
            $qty = (int) $qty;
            if ($qty > 0) {
                $items[(int)$prodId] = $qty;
            }
        }

        if (empty($items)) {
            $error = 'Agrega al menos un producto al pedido.';
        } else {
            $conexion->beginTransaction();
            try {
                $conexion->prepare('INSERT INTO pedidos (cliente_id, vendedor_id, notas, total) VALUES (?, ?, ?, 0)')
                         ->execute([$clienteId, $vendedorId, $notas]);
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
                header('Location: pedidos.php');
                exit();
            } catch (Exception $e) {
                $conexion->rollBack();
                $error = $e->getMessage();
            }
        }
    }
}
?>
<?php include __DIR__ . '/../../includes/header.php'; ?>

<div class="container mt-5">
    <h2 class="mb-4">Nuevo pedido</h2>

    <?php if (!empty($error)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <?php if (empty($clientes)): ?>
        <div class="alert alert-warning">No hay clientes registrados. Primero debe existir un usuario con rol <strong>cliente</strong>.</div>
    <?php elseif (empty($productos)): ?>
        <div class="alert alert-warning">No hay productos con stock disponible.</div>
    <?php else: ?>
    <form method="POST">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrfToken()) ?>">

        <div class="row mb-4">
            <div class="col-md-6">
                <label class="form-label fw-bold">Cliente <span class="text-danger">*</span></label>
                <select name="cliente_id" class="form-select" required>
                    <option value="">— Seleccionar cliente —</option>
                    <?php foreach ($clientes as $c): ?>
                        <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['nombre']) ?> &lt;<?= htmlspecialchars($c['email']) ?>&gt;</option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-bold">Notas</label>
                <input type="text" name="notas" class="form-control" placeholder="Observaciones del pedido (opcional)">
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header fw-bold">Productos disponibles</div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr><th>Producto</th><th>Categoría</th><th>Precio</th><th>Stock</th><th style="width:120px">Cantidad</th></tr>
                    </thead>
                    <tbody>
                        <?php foreach ($productos as $p): ?>
                        <tr>
                            <td><?= htmlspecialchars($p['nombre']) ?></td>
                            <td class="text-muted small"><?= htmlspecialchars($p['categoria'] ?? '—') ?></td>
                            <td>$<?= number_format((float)$p['precio'], 2) ?></td>
                            <td><span class="badge bg-success"><?= (int)$p['stock'] ?></span></td>
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

        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-success">Crear pedido</button>
            <a href="pedidos.php" class="btn btn-secondary">Cancelar</a>
        </div>
    </form>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/../../includes/footer.php'; ?>
