<?php
session_start();
require_once __DIR__ . '/includes/funciones.php';

// Si ya está logueado, va al panel
if (usuarioLogueado()) {
    header('Location: dashboard.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>TraceStock | Inventario</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="estilo.css">
</head>
<body>
<section class="portada">
  <header class="header portada-header">
    <a class="logo" href="index.php"><span class="logo-icon">▱</span>Trace<span class="cyan">Stock</span></a>
    <nav><a href="index.php">Inicio</a><a href="#presentacion">Nosotros</a></nav>
    <a class="btn btn-outline" href="login.php">Ingresar al sistema</a>
  </header>

  <main class="hero" id="presentacion">
    <div class="hero-copy">
      <p class="eyebrow">⚡ GESTIONA, CONTROLA Y CRECE TU INVENTARIO</p>
      <h1>Control de inventario <span>sin complicaciones</span></h1>
      <p class="subtext">Controla la entrada y salida de productos de tu negocio de manera sencilla, rápida y organizada.</p>
      <a class="btn btn-orange" href="login.php">Ingresar al sistema <span>→</span></a>
      <div class="mini-note"><span class="dot"></span> Una forma sencilla de gestionar tu inventario</div>
    </div>
    <div class="preview-card">
      <div class="preview-top">
        <div><small>RESUMEN DE INVENTARIO</small><h3>Actividad reciente</h3></div>
        <span class="status-pill">● En control</span>
      </div>
      <div class="preview-stats">
        <div class="preview-stat">
          <span class="stat-icon cyan-bg">↓</span>
          <div><small>Entradas</small><strong>+1.240</strong><small>unidades</small></div>
        </div>
        <div class="preview-stat">
          <span class="stat-icon orange-bg">↑</span>
          <div><small>Salidas</small><strong>−890</strong><small>unidades</small></div>
        </div>
      </div>
      <div class="preview-lines">
        <div><span><i class="line-dot cyan-dot"></i> Productos registrados</span><strong>07</strong></div>
        <div><span><i class="line-dot orange-dot"></i> Alertas de stock bajo</span><strong>03</strong></div>
        <div class="progress"><span></span></div>
      </div>
    </div>
  </main>

  <section class="landing-features">
    <article>
      <span class="feature-icon">↓</span>
      <h3>Entrada de productos</h3>
      <p>Registra el ingreso de mercancía de forma rápida y sencilla.</p>
    </article>
    <article>
      <span class="feature-icon orange-bg">↑</span>
      <h3>Salida de productos</h3>
      <p>Actualiza las existencias al registrar cada salida.</p>
    </article>
    <article>
      <span class="feature-icon">▤</span>
      <h3>Historial de movimientos</h3>
      <p>Consulta las entradas y salidas registradas.</p>
    </article>
  </section>
  <footer class="landing-footer">© 2026 TraceStock · Gestiona tu inventario sin complicaciones</footer>
</section>
</body>
</html>
