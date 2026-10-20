<?php
session_start();
require_once __DIR__ . '/includes/funciones.php';

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
  <title>TraceStock | Inventario bajo control</title>
  <link rel="stylesheet" href="estilo.css">
</head>
<body>
<section class="portada">
  <header class="header portada-header">
    <a class="logo" href="index.php"><span class="logo-icon">▱</span>Trace<span class="cyan">Stock</span></a>
    <nav aria-label="Secciones">
      <a href="index.php">Inicio</a>
      <a href="#presentacion">Sistema</a>
      <a href="#modulos">Módulos</a>
    </nav>
    <a class="btn btn-outline" href="login.php">Ingresar</a>
  </header>

  <main class="hero" id="presentacion">
    <div class="hero-visual" aria-hidden="true">
      <img src="img/hero-warehouse.jpg" alt="" width="1400" height="900">
    </div>
    <div class="hero-copy">
      <p class="eyebrow">Bodega · Entradas · Alertas</p>
      <h1>Inventario real <span>sin hojas sueltas</span></h1>
      <p class="subtext">Registra productos, controla entradas y salidas, y recibe alertas cuando el stock toca el mínimo. Pensado para ferreterías, talleres y bodegas.</p>
      <div class="hero-actions">
        <a class="btn btn-orange" href="login.php">Entrar al sistema <span>→</span></a>
        <a class="btn btn-outline" href="#modulos">Ver módulos</a>
      </div>
      <div class="mini-note"><span class="dot"></span> Demo lista: admin@tracestock.com / admin123</div>
    </div>
    <div class="preview-card">
      <div class="preview-top">
        <div><small>Resumen de bodega</small><h3>Actividad reciente</h3></div>
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
        <div><span><i class="line-dot cyan-dot"></i> Productos registrados</span><strong>14</strong></div>
        <div><span><i class="line-dot orange-dot"></i> Alertas de stock bajo</span><strong>05</strong></div>
        <div class="progress"><span></span></div>
      </div>
    </div>
  </main>

  <section class="landing-features" id="modulos">
    <article>
      <span class="feature-icon">↓</span>
      <h3>Entradas con traza</h3>
      <p>Cada ingreso suma stock y queda en historial con usuario, fecha y motivo.</p>
    </article>
    <article>
      <span class="feature-icon orange-bg">↑</span>
      <h3>Salidas sin negativos</h3>
      <p>El sistema bloquea salidas mayores al stock disponible. Sin sorpresas en bodega.</p>
    </article>
    <article>
      <span class="feature-icon">▤</span>
      <h3>Alertas de mínimo</h3>
      <p>Cuando el stock toca el mínimo, el producto aparece en el reporte para reponer.</p>
    </article>
  </section>
  <footer class="landing-footer">© 2026 TraceStock · Ficha 3260309 · Liceo Gran Virrey · SENA</footer>
</section>
</body>
</html>
