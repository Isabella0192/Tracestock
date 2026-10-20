<?php
session_start();
require_once __DIR__ . '/includes/conexion.php';
require_once __DIR__ . '/includes/funciones.php';
requiereLogin();

$tituloPagina = 'Historial de movimientos';

$tipo   = $_GET['tipo'] ?? '';
$buscar = trim($_GET['q'] ?? '');

$sql = 'SELECT m.fecha, m.tipo, m.cantidad, m.motivo,
               p.nombre AS producto, p.codigo, p.imagen,
               u.nombre AS usuario
        FROM movimiento m
        INNER JOIN producto p ON p.id_producto = m.id_producto
        INNER JOIN usuario u ON u.id_usuario = m.id_usuario
        WHERE 1=1';
$types = '';
$params = [];

if ($tipo === 'entrada' || $tipo === 'salida') {
    $sql .= ' AND m.tipo = ?';
    $types .= 's';
    $params[] = $tipo;
}
if ($buscar !== '') {
    $sql .= ' AND (p.nombre LIKE ? OR p.codigo LIKE ?)';
    $like = '%' . $buscar . '%';
    $types .= 'ss';
    $params[] = $like;
    $params[] = $like;
}
$sql .= ' ORDER BY m.fecha DESC, m.id_movimiento DESC';

$stmt = $mysqli->prepare($sql);
if ($types !== '') {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$movimientos = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

require __DIR__ . '/includes/layout_inicio.php';
?>

<div class="page-heading">
  <div>
    <h1>Historial de movimientos</h1>
    <p>Entradas y salidas son inmutables: no se editan ni se borran.</p>
  </div>
  <a class="btn btn-cyan" href="movimiento.php">＋ Registrar movimiento</a>
</div>

<div class="section-card">
  <form class="toolbar" method="get">
    <select name="tipo" onchange="this.form.submit()">
      <option value="">Todos los movimientos</option>
      <option value="entrada" <?= $tipo === 'entrada' ? 'selected' : '' ?>>Entrada</option>
      <option value="salida" <?= $tipo === 'salida' ? 'selected' : '' ?>>Salida</option>
    </select>
    <input name="q" placeholder="Buscar producto..." value="<?= e($buscar) ?>">
    <button class="btn btn-outline" type="submit">Filtrar</button>
  </form>
  <div class="table-wrap table-desktop">
    <table>
      <thead>
        <tr>
          <th>Fecha</th>
          <th>Producto</th>
          <th>Tipo</th>
          <th>Cantidad</th>
          <th>Usuario</th>
          <th>Nota</th>
        </tr>
      </thead>
      <tbody>
        <?php if (!$movimientos): ?>
          <tr><td colspan="6" class="empty">No hay movimientos para mostrar.</td></tr>
        <?php else: ?>
          <?php foreach ($movimientos as $m): ?>
            <tr>
              <td><?= e(formatearFecha($m['fecha'])) ?></td>
              <td class="product-cell">
                <div class="product-with-img">
                  <img class="product-thumb" src="<?= e(imagenProducto($m['imagen'] ?? null)) ?>" alt="">
                  <span><?= e($m['codigo'] . ' · ' . $m['producto']) ?></span>
                </div>
              </td>
              <td><?= badgeTipo($m['tipo']) ?></td>
              <td><?= (int) $m['cantidad'] ?></td>
              <td><?= e($m['usuario']) ?></td>
              <td><?= e($m['motivo'] ?: '—') ?></td>
            </tr>
          <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
  <div class="mobile-cards">
    <?php if (!$movimientos): ?>
      <p class="empty">No hay movimientos para mostrar.</p>
    <?php else: ?>
      <?php foreach ($movimientos as $m): ?>
        <article class="m-card">
          <div class="m-card-head">
            <div class="m-card-title">
              <img class="product-thumb" src="<?= e(imagenProducto($m['imagen'] ?? null)) ?>" alt="">
              <span><?= e($m['producto']) ?></span>
            </div>
            <?= badgeTipo($m['tipo']) ?>
          </div>
          <div class="m-card-meta">
            <div class="m-row"><span>Fecha</span><span><?= e(formatearFecha($m['fecha'])) ?></span></div>
            <div class="m-row"><span>Código</span><span><?= e($m['codigo']) ?></span></div>
            <div class="m-row"><span>Cantidad</span><span><?= (int) $m['cantidad'] ?></span></div>
            <div class="m-row"><span>Usuario</span><span><?= e($m['usuario']) ?></span></div>
            <div class="m-row"><span>Nota</span><span><?= e($m['motivo'] ?: '—') ?></span></div>
          </div>
        </article>
      <?php endforeach; ?>
    <?php endif; ?>
  </div>
</div>

<?php require __DIR__ . '/includes/layout_fin.php'; ?>
