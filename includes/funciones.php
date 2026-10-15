<?php
/**
 * Funciones compartidas de TraceStock.
 * Incluir DESPUÉS de session_start() y (si aplica) conexion.php.
 */

function e(string $texto): string
{
    return htmlspecialchars($texto, ENT_QUOTES, 'UTF-8');
}

function usuarioLogueado(): bool
{
    return isset($_SESSION['id_usuario']);
}

/** Si no hay sesión, manda al login. */
function requiereLogin(): void
{
    if (!usuarioLogueado()) {
        guardarMensaje('Inicia sesión para continuar.', 'error');
        header('Location: login.php');
        exit;
    }
}

/** Si ya hay sesión, no debe estar en login. */
function soloVisitantes(): void
{
    if (usuarioLogueado()) {
        header('Location: dashboard.php');
        exit;
    }
}

function claseActiva(string $archivo): string
{
    return basename($_SERVER['PHP_SELF']) === $archivo ? 'active' : '';
}

function guardarMensaje(string $texto, string $tipo = 'ok'): void
{
    $_SESSION['flash'] = ['texto' => $texto, 'tipo' => $tipo];
}

function obtenerMensaje(): ?array
{
    if (empty($_SESSION['flash'])) {
        return null;
    }
    $msg = $_SESSION['flash'];
    unset($_SESSION['flash']);
    return $msg;
}

/** Badge de estado de stock según regla: stock_actual <= stock_minimo = alerta. */
function badgeStock(int $stock, int $minimo): string
{
    if ($stock <= $minimo) {
        return '<span class="badge badge-low">Stock bajo</span>';
    }
    return '<span class="badge badge-ok">Disponible</span>';
}

function formatearFecha(?string $fecha): string
{
    if (!$fecha) {
        return '—';
    }
    $dt = DateTime::createFromFormat('Y-m-d H:i:s', $fecha)
        ?: DateTime::createFromFormat('Y-m-d', $fecha);
    return $dt ? $dt->format('d/m/y, g:i a') : $fecha;
}

function badgeTipo(string $tipo): string
{
    $clase = $tipo === 'entrada' ? 'badge-ok' : 'badge-out';
    $texto = $tipo === 'entrada' ? 'Entrada' : 'Salida';
    return '<span class="badge ' . $clase . '">' . e($texto) . '</span>';
}
