<?php
session_start();
require_once __DIR__ . '/includes/conexion.php';
require_once __DIR__ . '/includes/funciones.php';
requiereLogin();

$tituloPagina = 'Productos';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['accion'] ?? '') === 'eliminar') {
    $id = (int) ($_POST['id_producto'] ?? 0);
    $check = $mysqli->prepare('SELECT COUNT(*) AS c FROM movimiento WHERE id_producto = ?');
    $check->bind_param('i', $id);
    $check->execute();
    $tiene = (int) $check->get_result()->fetch_assoc()['c'];

    if ($tiene > 0) {
        guardarMensaje('No se puede eliminar: el producto tiene movimientos en el historial.', 'error');
    } else {
        $del = $mysqli->prepare('DELETE FROM producto WHERE id_producto = ?');
        $del->bind_param('i', $id);
        $del->execute();
        guardarMensaje('Producto eliminado.');
    }
    header('Location: productos.php');
    exit;
}

$buscar = trim($_GET['q'] ?? '');
$filtroCat = (int) ($_GET['categoria'] ?? 0);

$sql = 'SELECT p.*, c.nombre AS categoria
        FROM producto p
        INNER JOIN categoria c ON c.id_categoria = p.id_categoria
        WHERE 1=1';
$types = '';
$params = [];

if ($buscar !== '') {
    $sql .= ' AND (p.nombre LIKE ? OR p.codigo LIKE ?)';
    $like = '%' . $buscar . '%';
    $types .= 'ss';
    $params[] = $like;
    $params[] = $like;
}
if ($filtroCat > 0) {
    $sql .= ' AND p.id_categoria = ?';
    $types .= 'i';
    $params[] = $filtroCat;
}
$sql .= ' ORDER BY p.nombre ASC';

$stmt = $mysqli->prepare($sql);
if ($types !== '') {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$productos = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

$categorias = $mysqli->query('SELECT id_categoria, nombre FROM categoria ORDER BY nombre')->fetch_all(MYSQLI_ASSOC);

require __DIR__ . '/includes/layout_inicio.php';
?>

<div class="page-heading">
  <div>
    <h1>Productos</h1>
    <p>Catálogo con foto, stock y estado de alerta.</p>
  </div>
  <a class="btn btn-cyan" href="producto_form.php">＋ Nuevo producto</a>
</div>

<div class="section-card">
  <form class="toolbar" method="get">
    <input name="q" placeholder="Buscar producto o código..." value="<?= e($buscar) ?>">
    <select name="categoria" onchange="this.form.submit()">
      <option value="">Todas las categorías</option>
      <?php foreach ($categorias as $c): ?>
        <option value="<?= (int) $c['id_categoria'] ?>" <?= $filtroCat === (int) $c['id_categoria'] ? 'selected' : '' ?>>
          <?= e($c['nombre']) ?>
        </option>
      <?php endforeach; ?>
    </select>
    <button class="btn btn-outline" type="submit">Buscar</button>
  </form>

  <div class="product-grid force-grid">
    <?php if (!$productos): ?>
      <p class="empty" style="grid-column:1/-1">No se encontraron productos.</p>
    <?php else: ?>
      <?php foreach ($productos as $p): ?>
        <article class="product-card">
          <div class="product-card-img">
            <img src="<?= e(imagenProducto($p['imagen'] ?? null)) ?>" alt="<?= e($p['nombre']) ?>" loading="lazy">
          </div>
          <div class="product-card-body">
            <span class="product-card-code"><?= e($p['codigo']) ?></span>
            <h3><?= e($p['nombre']) ?></h3>
            <div class="product-card-meta">
              <span><?= e($p['categoria']) ?></span>
              <span><?= formatearPrecio((float) $p['precio']) ?></span>
              <span>Stock <?= (int) $p['stock_actual'] ?> / mín. <?= (int) $p['stock_minimo'] ?></span>
            </div>
            <?= badgeStock((int) $p['stock_actual'], (int) $p['stock_minimo']) ?>
            <div class="product-card-actions">
              <a class="small-action" href="producto_form.php?id=<?= (int) $p['id_producto'] ?>">Editar</a>
              <form method="post" onsubmit="return confirm('¿Eliminar este producto?');">
                <input type="hidden" name="accion" value="eliminar">
                <input type="hidden" name="id_producto" value="<?= (int) $p['id_producto'] ?>">
                <button type="submit" class="small-action danger-action">Eliminar</button>
              </form>
            </div>
          </div>
        </article>
      <?php endforeach; ?>
    <?php endif; ?>
  </div>
</div>

<?php require __DIR__ . '/includes/layout_fin.php'; ?>
