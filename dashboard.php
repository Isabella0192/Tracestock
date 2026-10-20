<?php
session_start();
require_once __DIR__ . '/includes/conexion.php';
require_once __DIR__ . '/includes/funciones.php';
requiereLogin();

$tituloPagina = 'Inicio';

$totalProductos = (int) $mysqli->query('SELECT COUNT(*) AS c FROM producto')->fetch_assoc()['c'];
$totalBajo = (int) $mysqli->query(
    'SELECT COUNT(*) AS c FROM producto WHERE stock_actual <= stock_minimo'
)->fetch_assoc()['c'];
$totalMovimientos = (int) $mysqli->query('SELECT COUNT(*) AS c FROM movimiento')->fetch_assoc()['c'];

$alertas = $mysqli->query(
    'SELECT nombre, codigo, stock_actual, stock_minimo, imagen
     FROM producto
     WHERE stock_actual <= stock_minimo
     ORDER BY stock_actual ASC
     LIMIT 10'
)->fetch_all(MYSQLI_ASSOC);

$recientes = $mysqli->query(
    'SELECT m.fecha, m.tipo, m.cantidad, m.motivo,
            p.nombre AS producto, p.codigo, u.nombre AS usuario
     FROM movimiento m
     INNER JOIN producto p ON p.id_producto = m.id_producto
     INNER JOIN usuario u ON u.id_usuario = m.id_usuario
     ORDER BY m.fecha DESC
     LIMIT 6'
)->fetch_all(MYSQLI_ASSOC);

require __DIR__ . '/includes/layout_inicio.php';
?>

<div class="welcome-row">
  <div>
    <h1>Panel de bodega</h1>
    <p>Resumen en vivo del inventario, alertas y últimos movimientos.</p>
  </div>
  <a class="btn btn-cyan" href="movimiento.php">＋ Registrar movimiento</a>
</div>

<div class="stat-grid">
  <article class="stat-card">
    <div class="stat-heading"><span>Total de productos</span><span class="stat-icon cyan-bg">▦</span></div>
    <strong><?= $totalProductos ?></strong>
    <small>SKUs activos en catálogo</small>
  </article>
  <article class="stat-card">
    <div class="stat-heading"><span>Stock bajo</span><span class="stat-icon orange-bg">!</span></div>
    <strong><?= $totalBajo ?></strong>
    <small>Necesitan reposición</small>
  </article>
  <article class="stat-card">
    <div class="stat-heading"><span>Movimientos</span><span class="stat-icon purple-bg">↕</span></div>
    <strong><?= $totalMovimientos ?></strong>
    <small>Entradas y salidas</small>
  </article>
</div>

<div class="section-card">
  <div class="card-heading">
    <div>
      <h3>Alertas de stock bajo</h3>
      <p>Productos en o por debajo del mínimo.</p>
    </div>
    <a class="text-button" href="reporte.php">Ver reporte →</a>
  </div>
  <div class="table-wrap table-desktop">
    <table>
      <thead>
        <tr><th>Producto</th><th>Stock actual</th><th>Stock mínimo</th><th>Estado</th></tr>
      </thead>
      <tbody>
        <?php if (!$alertas): ?>
          <tr><td colspan="4" class="empty">No hay alertas de stock bajo.</td></tr>
        <?php else: ?>
          <?php foreach ($alertas as $a): ?>
            <tr>
              <td class="product-cell">
                <div class="product-with-img">
                  <img class="product-thumb" src="<?= e(imagenProducto($a['imagen'] ?? null)) ?>" alt="">
                  <span><?= e($a['codigo'] . ' · ' . $a['nombre']) ?></span>
                </div>
              </td>
              <td><?= (int) $a['stock_actual'] ?></td>
              <td><?= (int) $a['stock_minimo'] ?></td>
              <td><?= badgeStock((int) $a['stock_actual'], (int) $a['stock_minimo']) ?></td>
            </tr>
          <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
  <div class="mobile-cards">
    <?php if (!$alertas): ?>
      <p class="empty">No hay alertas de stock bajo.</p>
    <?php else: ?>
      <?php foreach ($alertas as $a): ?>
        <article class="m-card">
          <div class="m-card-head">
            <div class="m-card-title">
              <img class="product-thumb" src="<?= e(imagenProducto($a['imagen'] ?? null)) ?>" alt="">
              <span><?= e($a['nombre']) ?></span>
            </div>
            <?= badgeStock((int) $a['stock_actual'], (int) $a['stock_minimo']) ?>
          </div>
          <div class="m-card-meta">
            <div class="m-row"><span>Código</span><span><?= e($a['codigo']) ?></span></div>
            <div class="m-row"><span>Stock actual</span><span><?= (int) $a['stock_actual'] ?></span></div>
            <div class="m-row"><span>Mínimo</span><span><?= (int) $a['stock_minimo'] ?></span></div>
          </div>
        </article>
      <?php endforeach; ?>
    <?php endif; ?>
  </div>
</div>

<div class="section-card">
  <div class="card-heading">
    <div>
      <h3>Movimientos recientes</h3>
      <p>Últimos cambios en el inventario.</p>
    </div>
    <a class="text-button" href="historial.php">Ver historial →</a>
  </div>
  <div class="table-wrap table-desktop">
    <table>
      <thead>
        <tr><th>Fecha</th><th>Producto</th><th>Tipo</th><th>Cantidad</th><th>Usuario</th><th>Nota</th></tr>
      </thead>
      <tbody>
        <?php if (!$recientes): ?>
          <tr><td colspan="6" class="empty">Aún no hay movimientos.</td></tr>
        <?php else: ?>
          <?php foreach ($recientes as $m): ?>
            <tr>
              <td><?= e(formatearFecha($m['fecha'])) ?></td>
              <td class="product-cell"><?= e($m['codigo'] . ' · ' . $m['producto']) ?></td>
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
    <?php if (!$recientes): ?>
      <p class="empty">Aún no hay movimientos.</p>
    <?php else: ?>
      <?php foreach ($recientes as $m): ?>
        <article class="m-card">
          <div class="m-card-head">
            <div class="m-card-title"><?= e($m['producto']) ?></div>
            <?= badgeTipo($m['tipo']) ?>
          </div>
          <div class="m-card-meta">
            <div class="m-row"><span>Fecha</span><span><?= e(formatearFecha($m['fecha'])) ?></span></div>
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
