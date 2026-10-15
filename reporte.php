<?php
session_start();
require_once __DIR__ . '/includes/conexion.php';
require_once __DIR__ . '/includes/funciones.php';
requiereLogin();

$tituloPagina = 'Reporte de stock';

$bajo = $mysqli->query(
    'SELECT p.nombre, p.codigo, p.stock_actual, p.stock_minimo, c.nombre AS categoria
     FROM producto p
     INNER JOIN categoria c ON c.id_categoria = p.id_categoria
     WHERE p.stock_actual <= p.stock_minimo
     ORDER BY p.stock_actual ASC'
)->fetch_all(MYSQLI_ASSOC);

$total = count($bajo);

require __DIR__ . '/includes/layout_inicio.php';
?>

<div class="page-heading">
  <div>
    <h1>Reporte de stock</h1>
    <p>Productos que necesitan reposición por estar en o debajo del mínimo.</p>
  </div>
</div>

<div class="stat-grid report-stat">
  <article class="stat-card">
    <div class="stat-heading">
      <span>Productos con stock bajo</span>
      <span class="stat-icon orange-bg">!</span>
    </div>
    <strong><?= $total ?></strong>
    <small>Requieren atención</small>
  </article>
</div>

<div class="section-card">
  <div class="card-heading">
    <div>
      <h3>Productos para revisar</h3>
      <p>Compara el stock actual con el mínimo definido.</p>
    </div>
  </div>
  <div class="table-wrap">
    <table>
      <thead>
        <tr>
          <th>Código</th>
          <th>Producto</th>
          <th>Categoría</th>
          <th>Stock actual</th>
          <th>Stock mínimo</th>
          <th>Estado</th>
        </tr>
      </thead>
      <tbody>
        <?php if (!$bajo): ?>
          <tr><td colspan="6" class="empty">Todos los productos tienen stock suficiente.</td></tr>
        <?php else: ?>
          <?php foreach ($bajo as $p): ?>
            <tr>
              <td><?= e($p['codigo']) ?></td>
              <td class="product-cell"><?= e($p['nombre']) ?></td>
              <td><?= e($p['categoria']) ?></td>
              <td><?= (int) $p['stock_actual'] ?></td>
              <td><?= (int) $p['stock_minimo'] ?></td>
              <td><?= badgeStock((int) $p['stock_actual'], (int) $p['stock_minimo']) ?></td>
            </tr>
          <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<?php require __DIR__ . '/includes/layout_fin.php'; ?>
