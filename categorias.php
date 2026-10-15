<?php
session_start();
require_once __DIR__ . '/includes/conexion.php';
require_once __DIR__ . '/includes/funciones.php';
requiereLogin();

$tituloPagina = 'Categorías';
$errorForm = '';

// Crear categoría
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['accion'] ?? '') === 'crear') {
    $nombre = trim($_POST['nombre'] ?? '');
    if ($nombre === '' || mb_strlen($nombre) < 2) {
        $errorForm = 'El nombre debe tener al menos 2 caracteres.';
    } else {
        try {
            $stmt = $mysqli->prepare('INSERT INTO categoria (nombre) VALUES (?)');
            $stmt->bind_param('s', $nombre);
            $stmt->execute();
            guardarMensaje('Categoría agregada correctamente.');
            header('Location: categorias.php');
            exit;
        } catch (mysqli_sql_exception $e) {
            $errorForm = 'Esa categoría ya existe.';
        }
    }
}

// Eliminar categoría (RESTRICT si tiene productos)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['accion'] ?? '') === 'eliminar') {
    $id = (int) ($_POST['id_categoria'] ?? 0);
    $check = $mysqli->prepare('SELECT COUNT(*) AS c FROM producto WHERE id_categoria = ?');
    $check->bind_param('i', $id);
    $check->execute();
    $tiene = (int) $check->get_result()->fetch_assoc()['c'];

    if ($tiene > 0) {
        guardarMensaje('No puedes eliminar una categoría que tiene productos.', 'error');
    } else {
        $del = $mysqli->prepare('DELETE FROM categoria WHERE id_categoria = ?');
        $del->bind_param('i', $id);
        $del->execute();
        guardarMensaje('Categoría eliminada.');
    }
    header('Location: categorias.php');
    exit;
}

// Editar nombre
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['accion'] ?? '') === 'editar') {
    $id = (int) ($_POST['id_categoria'] ?? 0);
    $nombre = trim($_POST['nombre'] ?? '');
    if ($nombre === '' || mb_strlen($nombre) < 2) {
        guardarMensaje('Nombre inválido.', 'error');
    } else {
        try {
            $upd = $mysqli->prepare('UPDATE categoria SET nombre = ? WHERE id_categoria = ?');
            $upd->bind_param('si', $nombre, $id);
            $upd->execute();
            guardarMensaje('Categoría actualizada.');
        } catch (mysqli_sql_exception $e) {
            guardarMensaje('Ya existe otra categoría con ese nombre.', 'error');
        }
    }
    header('Location: categorias.php');
    exit;
}

$categorias = $mysqli->query(
    'SELECT c.id_categoria, c.nombre,
            (SELECT COUNT(*) FROM producto p WHERE p.id_categoria = c.id_categoria) AS total
     FROM categoria c
     ORDER BY c.nombre ASC'
)->fetch_all(MYSQLI_ASSOC);

require __DIR__ . '/includes/layout_inicio.php';
?>

<div class="page-heading">
  <div>
    <h1>Gestión de categorías</h1>
    <p>Organiza los productos por categoría.</p>
  </div>
</div>

<div class="split-layout">
  <div class="section-card">
    <div class="card-heading">
      <div>
        <h3>Categorías registradas</h3>
        <p>Listado de categorías del inventario.</p>
      </div>
    </div>
    <div class="category-list">
      <?php if (!$categorias): ?>
        <p class="empty">Todavía no hay categorías.</p>
      <?php else: ?>
        <?php foreach ($categorias as $c): ?>
          <div class="category-item">
            <span class="category-mark">◇</span>
            <form method="post" class="category-edit-form">
              <input type="hidden" name="accion" value="editar">
              <input type="hidden" name="id_categoria" value="<?= (int) $c['id_categoria'] ?>">
              <input name="nombre" value="<?= e($c['nombre']) ?>" required maxlength="100">
              <button type="submit" class="small-action" title="Guardar">Guardar</button>
            </form>
            <small><?= (int) $c['total'] ?> productos</small>
            <form method="post" onsubmit="return confirm('¿Eliminar esta categoría?');">
              <input type="hidden" name="accion" value="eliminar">
              <input type="hidden" name="id_categoria" value="<?= (int) $c['id_categoria'] ?>">
              <button type="submit" class="delete-button" title="Eliminar">×</button>
            </form>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>
  </div>

  <div class="section-card form-card">
    <h3>Nueva categoría</h3>
    <p class="form-description">Agrega una categoría para clasificar productos.</p>
    <form method="post">
      <input type="hidden" name="accion" value="crear">
      <?php if ($errorForm): ?>
        <p class="form-error"><?= e($errorForm) ?></p>
      <?php endif; ?>
      <label for="nombreCategoria">Nombre de la categoría</label>
      <input id="nombreCategoria" name="nombre" placeholder="Ej. Herramientas" required maxlength="100"
             value="<?= e($_POST['nombre'] ?? '') ?>">
      <button class="btn btn-cyan full" type="submit">＋ Agregar categoría</button>
    </form>
  </div>
</div>

<?php require __DIR__ . '/includes/layout_fin.php'; ?>
