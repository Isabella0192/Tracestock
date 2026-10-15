<?php
/**
 * Cabecera del panel (sidebar + topbar).
 * Variables esperadas:
 *   $tituloPagina (string)
 *   $usuarioNombre (string) — opcional, se toma de sesión
 */
if (!isset($tituloPagina)) {
    $tituloPagina = 'Inicio';
}
$usuarioNombre = $_SESSION['nombre'] ?? 'Admin';
$inicial = mb_strtoupper(mb_substr($usuarioNombre, 0, 1));
$flash = obtenerMensaje();
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= e($tituloPagina) ?> | TraceStock</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="estilo.css">
</head>
<body>
<div class="app-shell">
  <aside class="sidebar">
    <a class="logo side-logo" href="dashboard.php">
      <span class="logo-icon">▱</span>Trace<span class="cyan">Stock</span>
    </a>
    <p class="menu-label">MENÚ PRINCIPAL</p>
    <nav class="side-nav">
      <a class="side-link <?= claseActiva('dashboard.php') ?>" href="dashboard.php"><span>⌂</span>Inicio</a>
      <a class="side-link <?= claseActiva('productos.php') ?> <?= claseActiva('producto_form.php') ?>" href="productos.php"><span>▦</span>Productos</a>
      <a class="side-link <?= claseActiva('categorias.php') ?>" href="categorias.php"><span>◇</span>Categorías</a>
      <a class="side-link <?= claseActiva('movimiento.php') ?>" href="movimiento.php"><span>↕</span>Registrar movimiento</a>
      <a class="side-link <?= claseActiva('historial.php') ?>" href="historial.php"><span>◷</span>Historial</a>
      <a class="side-link <?= claseActiva('reporte.php') ?>" href="reporte.php"><span>▤</span>Reporte de stock</a>
    </nav>
    <div class="sidebar-bottom">
      <div class="sidebar-tip">
        <span>✦</span>
        <strong>Control sencillo</strong>
        <small>Todo tu inventario en un solo lugar.</small>
      </div>
      <a class="side-link logout" href="logout.php"><span>↪</span>Salir</a>
    </div>
  </aside>

  <main class="main-area">
    <header class="topbar">
      <div>
        <span class="topbar-label">SISTEMA DE INVENTARIO</span>
        <h2><?= e($tituloPagina) ?></h2>
      </div>
      <div class="user-chip">
        <div class="avatar"><?= e($inicial) ?></div>
        <div>
          <strong><?= e($usuarioNombre) ?></strong>
          <small>Administrador</small>
        </div>
      </div>
    </header>

    <div class="content-area">
      <?php if ($flash): ?>
        <div class="flash flash-<?= e($flash['tipo']) ?>"><?= e($flash['texto']) ?></div>
      <?php endif; ?>
