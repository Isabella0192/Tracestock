<?php
session_start();
require_once __DIR__ . '/includes/conexion.php';
require_once __DIR__ . '/includes/funciones.php';
requiereLogin();

$tituloPagina = 'Reporte de stock';

$bajo = $mysqli->query(
    'SELECT p.nombre, p.codigo, p.stock_actual, p.stock_minimo, p.imagen, c.nombre AS categoria
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
  <a class="btn btn-outline" href="movimiento.php">Registrar entrada</a>
</div>

<div class="stat-grid report-stat">
  <article class="stat-card">
    <div class="stat-heading">
      <span>Productos con stock bajo</span>
      <span class="stat-icon orange-bg">!</span>
    </div>
    <strong><?= $total ?></strong>
    <small>Requieren atención inmediata</small>
  </article>
</div>

<div class="section-card">
  <div class="card-heading">
    <div>
      <h3>Productos para revisar</h3>
      <p>Compara el stock actual con el mínimo definido.</p>
    </div>
  </div>
  <div class="table-wrap table-desktop">
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
              <td class="product-cell">
                <div class="product-with-img">
                  <img class="product-thumb" src="<?= e(imagenProducto($p['imagen'] ?? null)) ?>" alt="">
                  <span><?= e($p['nombre']) ?></span>
                </div>
              </td>
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
  <div class="mobile-cards">
    <?php if (!$bajo): ?>
      <p class="empty">Todos los productos tienen stock suficiente.</p>
    <?php else: ?>
      <?php foreach ($bajo as $p): ?>
        <article class="m-card">
          <div class="m-card-head">
            <div class="m-card-title">
              <img class="product-thumb" src="<?= e(imagenProducto($p['imagen'] ?? null)) ?>" alt="">
              <span><?= e($p['nombre']) ?></span>
            </div>
            <?= badgeStock((int) $p['stock_actual'], (int) $p['stock_minimo']) ?>
          </div>
          <div class="m-card-meta">
            <div class="m-row"><span>Código</span><span><?= e($p['codigo']) ?></span></div>
            <div class="m-row"><span>Categoría</span><span><?= e($p['categoria']) ?></span></div>
            <div class="m-row"><span>Stock actual</span><span><?= (int) $p['stock_actual'] ?></span></div>
            <div class="m-row"><span>Mínimo</span><span><?= (int) $p['stock_minimo'] ?></span></div>
          </div>
        </article>
      <?php endforeach; ?>
    <?php endif; ?>
  </div>
</div>

<?php require __DIR__ . '/includes/layout_fin.php'; ?>
