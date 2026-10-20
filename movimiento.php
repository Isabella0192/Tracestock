<?php
session_start();
require_once __DIR__ . '/includes/conexion.php';
require_once __DIR__ . '/includes/funciones.php';
requiereLogin();

$tituloPagina = 'Registrar movimiento';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tipo       = $_POST['tipo'] ?? '';
    $idProducto = (int) ($_POST['id_producto'] ?? 0);
    $cantidad   = (int) ($_POST['cantidad'] ?? 0);
    $motivo     = trim($_POST['motivo'] ?? '');
    $idUsuario  = (int) $_SESSION['id_usuario'];

    if (!in_array($tipo, ['entrada', 'salida'], true)) {
        $error = 'El tipo debe ser entrada o salida.';
    } elseif ($idProducto <= 0) {
        $error = 'Debes elegir un producto.';
    } elseif ($cantidad < 1) {
        $error = 'La cantidad debe ser un entero mayor que 0.';
    } elseif (mb_strlen($motivo) > 150) {
        $error = 'El motivo no puede superar 150 caracteres.';
    } else {
        // Transacción: INSERT movimiento + UPDATE stock (o ninguno)
        $mysqli->begin_transaction();
        try {
            $lock = $mysqli->prepare(
                'SELECT stock_actual, nombre FROM producto WHERE id_producto = ? FOR UPDATE'
            );
            $lock->bind_param('i', $idProducto);
            $lock->execute();
            $prod = $lock->get_result()->fetch_assoc();

            if (!$prod) {
                throw new RuntimeException('Producto no encontrado.');
            }

            $stock = (int) $prod['stock_actual'];
            if ($tipo === 'salida' && $cantidad > $stock) {
                throw new RuntimeException(
                    'No hay suficiente stock. Disponible: ' . $stock . ' unidades.'
                );
            }

            if ($motivo === '') {
                $ins = $mysqli->prepare(
                    'INSERT INTO movimiento (tipo, cantidad, motivo, id_producto, id_usuario)
                     VALUES (?, ?, NULL, ?, ?)'
                );
                $ins->bind_param('siii', $tipo, $cantidad, $idProducto, $idUsuario);
            } else {
                $ins = $mysqli->prepare(
                    'INSERT INTO movimiento (tipo, cantidad, motivo, id_producto, id_usuario)
                     VALUES (?, ?, ?, ?, ?)'
                );
                $ins->bind_param('sisii', $tipo, $cantidad, $motivo, $idProducto, $idUsuario);
            }
            $ins->execute();

            if ($tipo === 'entrada') {
                $upd = $mysqli->prepare(
                    'UPDATE producto SET stock_actual = stock_actual + ? WHERE id_producto = ?'
                );
            } else {
                $upd = $mysqli->prepare(
                    'UPDATE producto SET stock_actual = stock_actual - ? WHERE id_producto = ?'
                );
            }
            $upd->bind_param('ii', $cantidad, $idProducto);
            $upd->execute();

            $mysqli->commit();

            $msg = $tipo === 'entrada' ? 'Entrada registrada.' : 'Salida registrada.';
            // Aviso extra si quedó en alerta
            $check = $mysqli->prepare(
                'SELECT stock_actual, stock_minimo FROM producto WHERE id_producto = ?'
            );
            $check->bind_param('i', $idProducto);
            $check->execute();
            $st = $check->get_result()->fetch_assoc();
            if ($st && (int) $st['stock_actual'] <= (int) $st['stock_minimo']) {
                $msg .= ' Atención: el producto quedó con stock bajo.';
            }

            guardarMensaje($msg);
            header('Location: historial.php');
            exit;
        } catch (Throwable $e) {
            $mysqli->rollback();
            $error = $e->getMessage();
        }
    }
}

$listaProductos = $mysqli->query(
    'SELECT id_producto, nombre, codigo, stock_actual
     FROM producto
     ORDER BY nombre ASC'
)->fetch_all(MYSQLI_ASSOC);

require __DIR__ . '/includes/layout_inicio.php';
?>

<div class="page-heading">
  <div>
    <h1>Registrar movimiento</h1>
    <p>Entrada suma stock · Salida resta · Nunca permite negativos.</p>
  </div>
  <a class="btn btn-outline" href="historial.php">Ver historial</a>
</div>

<div class="section-card form-card movement-form">
  <form method="post">
    <?php if ($error): ?>
      <p class="form-error"><?= e($error) ?></p>
    <?php endif; ?>

    <div class="movement-choice">
      <label class="choice">
        <input type="radio" name="tipo" value="entrada" <?= (($_POST['tipo'] ?? 'entrada') === 'entrada') ? 'checked' : '' ?>>
        <span class="choice-icon cyan-bg">↓</span>
        <span><strong>Entrada</strong><small>Agregar unidades al stock</small></span>
      </label>
      <label class="choice">
        <input type="radio" name="tipo" value="salida" <?= (($_POST['tipo'] ?? '') === 'salida') ? 'checked' : '' ?>>
        <span class="choice-icon orange-bg">↑</span>
        <span><strong>Salida</strong><small>Restar unidades del stock</small></span>
      </label>
    </div>

    <div class="form-grid">
      <div>
        <label for="id_producto">Producto</label>
        <select id="id_producto" name="id_producto" required>
          <option value="">Seleccionar</option>
          <?php foreach ($listaProductos as $p): ?>
            <option value="<?= (int) $p['id_producto'] ?>"
              <?= (int) ($_POST['id_producto'] ?? 0) === (int) $p['id_producto'] ? 'selected' : '' ?>>
              <?= e($p['codigo'] . ' — ' . $p['nombre'] . ' (stock: ' . $p['stock_actual'] . ')') ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>
      <div>
        <label for="cantidad">Cantidad</label>
        <input id="cantidad" name="cantidad" type="number" min="1" required
               placeholder="Ej. 10" value="<?= e((string) ($_POST['cantidad'] ?? '')) ?>">
      </div>
      <div class="form-span">
        <label for="motivo">Motivo (opcional)</label>
        <input id="motivo" name="motivo" maxlength="150"
               placeholder="Compra, venta, daño, devolución..."
               value="<?= e((string) ($_POST['motivo'] ?? '')) ?>">
      </div>
    </div>

    <button class="btn btn-cyan" type="submit">Guardar movimiento</button>
  </form>
</div>

<?php require __DIR__ . '/includes/layout_fin.php'; ?>
