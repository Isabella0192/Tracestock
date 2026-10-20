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
$usuarioRol = $_SESSION['rol'] ?? 'administrador';
$inicial = mb_strtoupper(mb_substr($usuarioNombre, 0, 1));
$flash = obtenerMensaje();
$rolLabel = $usuarioRol === 'operario' ? 'Operario' : 'Administrador';
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= e($tituloPagina) ?> | TraceStock</title>
  <link rel="stylesheet" href="estilo.css">
</head>
<body>
<div class="sidebar-overlay" id="sidebarOverlay" hidden></div>
<div class="app-shell">
  <aside class="sidebar" id="sidebar">
    <a class="logo side-logo" href="dashboard.php">
      <span class="logo-icon">▱</span>Trace<span class="cyan">Stock</span>
    </a>
    <p class="menu-label">Menú principal</p>
    <nav class="side-nav" aria-label="Navegación del panel">
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
        <strong>Bodega bajo control</strong>
        <small>Alertas de mínimo, entradas y salidas en una sola vista.</small>
      </div>
      <a class="side-link logout" href="logout.php"><span>↪</span>Salir</a>
    </div>
  </aside>

  <main class="main-area">
    <header class="topbar">
      <div class="topbar-left">
        <button type="button" class="nav-toggle" id="navToggle" aria-label="Abrir menú" aria-expanded="false" aria-controls="sidebar">☰</button>
        <div>
          <span class="topbar-label">Sistema de inventario</span>
          <h2><?= e($tituloPagina) ?></h2>
        </div>
      </div>
      <div class="user-chip">
        <div class="avatar" aria-hidden="true"><?= e($inicial) ?></div>
        <div>
          <strong><?= e($usuarioNombre) ?></strong>
          <small><?= e($rolLabel) ?></small>
        </div>
      </div>
    </header>

    <div class="content-area">
      <?php if ($flash): ?>
        <div class="flash flash-<?= e($flash['tipo']) ?>" role="status"><?= e($flash['texto']) ?></div>
      <?php endif; ?>
