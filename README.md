# TraceStock — Control de inventario

Proyecto productivo · Ficha **3260309** · Liceo Gran Virrey · SENA 2026  
Stack: **HTML + CSS + PHP + MySQL (XAMPP)**

## Qué es

Sistema interno para registrar productos, entradas y salidas de inventario, con alertas de stock bajo e historial de movimientos.

## Cómo arrancarlo (XAMPP)

1. Activa **Apache** y **MySQL** en XAMPP.
2. Abre [phpMyAdmin](http://localhost/phpmyadmin).
3. Ve a **Importar** y elige el archivo `bd/tracestock.sql`.
4. En el navegador abre:  
   **http://localhost/Tracestock/**

### Usuario de prueba

| Campo | Valor |
|-------|--------|
| Correo | `admin@tracestock.com` |
| Contraseña | `admin123` |

## Estructura del proyecto

```
Tracestock/
├── bd/tracestock.sql      ← base de datos (importar primero)
├── includes/
│   ├── conexion.php       ← conexión MySQL
│   ├── funciones.php      ← login, mensajes, helpers
│   ├── layout_inicio.php  ← menú lateral + topbar
│   └── layout_fin.php
├── index.php              ← portada
├── login.php / logout.php
├── dashboard.php          ← inicio (resumen)
├── categorias.php
├── productos.php
├── producto_form.php
├── movimiento.php         ← entrada/salida + transacción
├── historial.php          ← JOIN movimiento + producto + usuario
├── reporte.php            ← stock bajo
├── estilo.css
└── codigo.html            ← maqueta original (solo referencia)
```

## Reglas de negocio (ya implementadas)

1. El stock **nunca** puede quedar negativo (una salida mayor al stock se rechaza).
2. Cada movimiento hace `INSERT` en `movimiento` y `UPDATE` en `producto` **dentro de una transacción**.
3. Los movimientos **no se editan ni se borran**.
4. Si `stock_actual <= stock_minimo`, el producto aparece en alerta.
5. Usuario con `activo = 0` no puede iniciar sesión.
6. Todas las páginas del panel exigen sesión PHP.

## Tablas

- `usuario` → `categoria` → `producto` → `movimiento`
- Relación: una categoría tiene muchos productos; un producto tiene muchos movimientos.

## Para las aprendices

- `codigo.html` era el prototipo en JavaScript (datos inventados).
- El sistema real son los archivos `.php` que leen y escriben en MySQL.
- Si algo falla al entrar, revisa que hayas importado el SQL y que MySQL esté encendido.
