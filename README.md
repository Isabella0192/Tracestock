# TraceStock — Control de inventario

Proyecto productivo · Ficha **3260309** · Liceo Gran Virrey · SENA 2026  
Stack: **HTML + CSS + PHP + MySQL (XAMPP)**

## Qué es

Sistema interno para registrar productos (con foto), entradas y salidas de inventario, con alertas de stock bajo e historial de movimientos.

## URL

**http://localhost/Tracestock/**

## Cómo arrancarlo (XAMPP)

1. Activa **Apache** y **MySQL** en XAMPP.
2. Importa la base (opción A o B):

**A · Terminal**
```bash
/Applications/XAMPP/xamppfiles/bin/mysql -u root < /Applications/XAMPP/xamppfiles/htdocs/Tracestock/bd/tracestock.sql
```

**B · phpMyAdmin** → Importar → `bd/tracestock.sql`

3. Abre: **http://localhost/Tracestock/**

## Credenciales demo

| Rol | Correo | Contraseña |
|-----|--------|------------|
| Administrador | `admin@tracestock.com` | `admin123` |
| Operario | `operario@tracestock.com` | `operario123` |

## Estructura

```
Tracestock/
├── bd/tracestock.sql
├── img/                   ← fotos de bodega y productos
├── includes/
│   ├── conexion.php
│   ├── funciones.php
│   ├── layout_inicio.php  ← menú + topbar + nav móvil
│   └── layout_fin.php
├── index.php              ← portada
├── login.php / logout.php
├── dashboard.php
├── categorias.php
├── productos.php          ← grid con imágenes
├── producto_form.php
├── movimiento.php
├── historial.php
├── reporte.php
└── estilo.css
```

## Reglas de negocio

1. El stock **nunca** puede quedar negativo.
2. Cada movimiento hace `INSERT` + `UPDATE` en **transacción**.
3. Los movimientos **no se editan ni se borran**.
4. Si `stock_actual <= stock_minimo` → alerta / reporte.
5. Usuario con `activo = 0` no puede iniciar sesión.
6. El panel exige sesión PHP.

## Datos demo incluidos

- 5 categorías (herramientas, electricidad, ferretería, pinturas, maquinaria)
- 14 productos con imagen y precios COP
- 28 movimientos de entrada/salida con motivos reales
- 2 usuarios (admin + operario)
