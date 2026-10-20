<?php
session_start();
require_once __DIR__ . '/includes/conexion.php';
require_once __DIR__ . '/includes/funciones.php';

soloVisitantes();

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $correo = trim($_POST['correo'] ?? '');
    $clave  = $_POST['clave'] ?? '';

    if ($correo === '' || $clave === '') {
        $error = 'Correo y contraseña son obligatorios.';
    } elseif (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        $error = 'El correo no tiene un formato válido.';
    } elseif (strlen($clave) < 6) {
        $error = 'La contraseña debe tener mínimo 6 caracteres.';
    } else {
        $stmt = $mysqli->prepare(
            'SELECT id_usuario, nombre, correo, clave, rol, activo
             FROM usuario WHERE correo = ? LIMIT 1'
        );
        $stmt->bind_param('s', $correo);
        $stmt->execute();
        $usuario = $stmt->get_result()->fetch_assoc();

        if (!$usuario || !(int) $usuario['activo']) {
            $error = 'Usuario no encontrado o desactivado.';
        } elseif (!password_verify($clave, $usuario['clave'])) {
            $error = 'Correo o contraseña incorrectos.';
        } else {
            session_regenerate_id(true);
            $_SESSION['id_usuario'] = (int) $usuario['id_usuario'];
            $_SESSION['nombre']     = $usuario['nombre'];
            $_SESSION['correo']     = $usuario['correo'];
            $_SESSION['rol']        = $usuario['rol'];
            guardarMensaje('Bienvenido/a, ' . $usuario['nombre']);
            header('Location: dashboard.php');
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Iniciar sesión | TraceStock</title>
  <link rel="stylesheet" href="estilo.css">
</head>
<body>
<section class="login-screen">
  <a class="back-link" href="index.php">← Volver a la portada</a>
  <div class="login-card">
    <div class="login-brand">
      <span class="brand-symbol">▱</span>
      <h1>Trace<span class="cyan">Stock</span></h1>
      <p>Acceso al panel de inventario.</p>
    </div>
    <form method="post" autocomplete="on">
      <?php if ($error): ?>
        <p class="form-error" role="alert"><?= e($error) ?></p>
      <?php endif; ?>
      <label for="correo">Correo</label>
      <input id="correo" name="correo" type="email" placeholder="admin@tracestock.com"
             value="<?= e($_POST['correo'] ?? '') ?>" autocomplete="username" required>
      <label for="clave">Contraseña</label>
      <input id="clave" name="clave" type="password" placeholder="Mínimo 6 caracteres"
             autocomplete="current-password" required minlength="6">
      <button class="btn btn-cyan full" type="submit">Iniciar sesión <span>→</span></button>
      <p class="login-hint">
        Admin: <strong>admin@tracestock.com</strong> / <strong>admin123</strong><br>
        Operario: <strong>operario@tracestock.com</strong> / <strong>operario123</strong>
      </p>
    </form>
  </div>
</section>
</body>
</html>
