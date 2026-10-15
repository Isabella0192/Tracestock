<?php
session_start();
require_once __DIR__ . '/includes/conexion.php';
require_once __DIR__ . '/includes/funciones.php';
requiereLogin();

$id = (int) ($_GET['id'] ?? $_POST['id_producto'] ?? 0);
$editando = $id > 0;
$tituloPagina = $editando ? 'Editar producto' : 'Nuevo producto';
$error = '';
$producto = [
    'codigo' => '',
    'nombre' => '',
    'descripcion' => '',
    'precio' => '',
    'stock_actual' => '0',
    'stock_minimo' => '5',
    'id_categoria' => '',
];

if ($editando && $_SERVER['REQUEST_METHOD'] !== 'POST') {
    $stmt = $mysqli->prepare('SELECT * FROM producto WHERE id_producto = ?');
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    if (!$row) {
        guardarMensaje('Producto no encontrado.', 'error');
        header('Location: productos.php');
        exit;
    }
    $producto = $row;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $codigo      = trim($_POST['codigo'] ?? '');
    $nombre      = trim($_POST['nombre'] ?? '');
    $descripcion = trim($_POST['descripcion'] ?? '');
    $precio      = (float) ($_POST['precio'] ?? 0);
    $stockActual = (int) ($_POST['stock_actual'] ?? 0);
    $stockMinimo = (int) ($_POST['stock_minimo'] ?? 0);
    $idCategoria = (int) ($_POST['id_categoria'] ?? 0);

    $producto = [
        'codigo' => $codigo,
        'nombre' => $nombre,
        'descripcion' => $descripcion,
        'precio' => $precio,
        'stock_actual' => $stockActual,
        'stock_minimo' => $stockMinimo,
        'id_categoria' => $idCategoria,
    ];

    if ($codigo === '' || mb_strlen($codigo) > 30) {
        $error = 'El código es obligatorio (máx. 30 caracteres).';
    } elseif ($nombre === '' || mb_strlen($nombre) < 3 || mb_strlen($nombre) > 100) {
        $error = 'El nombre debe tener entre 3 y 100 caracteres.';
    } elseif ($precio <= 0) {
        $error = 'El precio debe ser mayor que 0.';
    } elseif ($stockActual < 0 || $stockMinimo < 0) {
        $error = 'Stock actual y mínimo no pueden ser negativos.';
    } elseif ($idCategoria <= 0) {
        $error = 'Debes elegir una categoría.';
    } else {
        try {
            if ($editando) {
                $stmt = $mysqli->prepare(
                    'UPDATE producto
                     SET codigo=?, nombre=?, descripcion=?, precio=?, stock_actual=?, stock_minimo=?, id_categoria=?
                     WHERE id_producto=?'
                );
                $stmt->bind_param(
                    'sssdiiii',
                    $codigo, $nombre, $descripcion, $precio, $stockActual, $stockMinimo, $idCategoria, $id
                );
                $stmt->execute();
                guardarMensaje('Producto actualizado.');
            } else {
                $stmt = $mysqli->prepare(
                    'INSERT INTO producto (codigo, nombre, descripcion, precio, stock_actual, stock_minimo, id_categoria)
                     VALUES (?, ?, ?, ?, ?, ?, ?)'
                );
                $stmt->bind_param(
                    'sssdiii',
                    $codigo, $nombre, $descripcion, $precio, $stockActual, $stockMinimo, $idCategoria
                );
                $stmt->execute();
                guardarMensaje('Producto creado.');
            }
            header('Location: productos.php');
            exit;
        } catch (mysqli_sql_exception $e) {
            $error = ($mysqli->errno === 1062)
                ? 'Ese código de producto ya existe.'
                : 'No se pudo guardar el producto.';
        }
    }
}

$categorias = $mysqli->query('SELECT id_categoria, nombre FROM categoria ORDER BY nombre')->fetch_all(MYSQLI_ASSOC);

require __DIR__ . '/includes/layout_inicio.php';
?>

<div class="page-heading">
  <div>
    <h1><?= e($tituloPagina) ?></h1>
    <p>Completa la información del producto.</p>
  </div>
  <a class="btn btn-outline" href="productos.php">← Volver a productos</a>
</div>

<div class="section-card form-card wide-form">
  <form method="post">
    <?php if ($editando): ?>
      <input type="hidden" name="id_producto" value="<?= $id ?>">
    <?php endif; ?>
    <?php if ($error): ?>
      <p class="form-error"><?= e($error) ?></p>
    <?php endif; ?>

    <div class="form-grid">
      <div>
        <label for="codigo">Código</label>
        <input id="codigo" name="codigo" maxlength="30" required
               placeholder="Ej. ELE-001" value="<?= e((string) $producto['codigo']) ?>">
      </div>
      <div>
        <label for="nombre">Nombre del producto</label>
        <input id="nombre" name="nombre" maxlength="100" required
               placeholder="Ej. Cable 12 AWG" value="<?= e((string) $producto['nombre']) ?>">
      </div>
      <div class="form-span">
        <label for="descripcion">Descripción (opcional)</label>
        <input id="descripcion" name="descripcion"
               placeholder="Detalle del producto" value="<?= e((string) ($producto['descripcion'] ?? '')) ?>">
      </div>
      <div>
        <label for="id_categoria">Categoría</label>
        <select id="id_categoria" name="id_categoria" required>
          <option value="">Seleccionar</option>
          <?php foreach ($categorias as $c): ?>
            <option value="<?= (int) $c['id_categoria'] ?>"
              <?= (int) $producto['id_categoria'] === (int) $c['id_categoria'] ? 'selected' : '' ?>>
              <?= e($c['nombre']) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>
      <div>
        <label for="precio">Precio</label>
        <input id="precio" name="precio" type="number" min="1" step="0.01" required
               value="<?= e((string) $producto['precio']) ?>">
      </div>
      <div>
        <label for="stock_actual">Stock actual</label>
        <input id="stock_actual" name="stock_actual" type="number" min="0" required
               value="<?= e((string) $producto['stock_actual']) ?>">
      </div>
      <div>
        <label for="stock_minimo">Stock mínimo</label>
        <input id="stock_minimo" name="stock_minimo" type="number" min="0" required
               value="<?= e((string) $producto['stock_minimo']) ?>">
      </div>
    </div>

    <div class="form-actions">
      <a class="btn btn-outline" href="productos.php">Cancelar</a>
      <button class="btn btn-cyan" type="submit">Guardar producto</button>
    </div>
  </form>
</div>

<?php require __DIR__ . '/includes/layout_fin.php'; ?>
