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
    'SELECT nombre, stock_actual, stock_minimo
     FROM producto
     WHERE stock_actual <= stock_minimo
     ORDER BY stock_actual ASC
     LIMIT 10'
)->fetch_all(MYSQLI_ASSOC);

$recientes = $mysqli->query(
    'SELECT m.fecha, m.tipo, m.cantidad, m.motivo,
            p.nombre AS producto, u.nombre AS usuario
     FROM movimiento m
     INNER JOIN producto p ON p.id_producto = m.id_producto
     INNER JOIN usuario u ON u.id_usuario = m.id_usuario
     ORDER BY m.fecha DESC
     LIMIT 5'
)->fetch_all(MYSQLI_ASSOC);

require __DIR__ . '/includes/layout_inicio.php';
?>

<div class="welcome-row">
  <div>
    <h1>¡Bienvenido a TraceStock!</h1>
    <p>Este es el resumen del estado actual de tu inventario.</p>
  </div>
  <a class="btn btn-cyan" href="movimiento.php">＋ Registrar movimiento</a>
</div>

<div class="stat-grid">
  <article class="stat-card">
    <div class="stat-heading"><span>Total de productos</span><span class="stat-icon cyan-bg">▦</span></div>
    <strong><?= $totalProductos ?></strong>
    <small>Productos registrados</small>
  </article>
  <article class="stat-card">
    <div class="stat-heading"><span>Stock bajo</span><span class="stat-icon orange-bg">!</span></div>
    <strong><?= $totalBajo ?></strong>
    <small>Necesitan revisión</small>
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
      <p>Productos por debajo o iguales al mínimo establecido.</p>
    </div>
    <a class="text-button" href="reporte.php">Ver reporte →</a>
  </div>
  <div class="table-wrap">
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
              <td class="product-cell"><?= e($a['nombre']) ?></td>
              <td><?= (int) $a['stock_actual'] ?></td>
              <td><?= (int) $a['stock_minimo'] ?></td>
              <td><?= badgeStock((int) $a['stock_actual'], (int) $a['stock_minimo']) ?></td>
            </tr>
          <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
    </table>
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
  <div class="table-wrap">
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
              <td class="product-cell"><?= e($m['producto']) ?></td>
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
</div>

<?php require __DIR__ . '/includes/layout_fin.php'; ?>
