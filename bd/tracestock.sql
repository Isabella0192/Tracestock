-- TraceStock · Base de datos del proyecto productivo
-- Ficha 3260309 · HTML + CSS + PHP + MySQL (XAMPP)
-- Importar en phpMyAdmin: crear no hace falta, este script crea todo.

CREATE DATABASE IF NOT EXISTS tracestock
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE tracestock;

-- ---------------------------------------------------------
-- 1. USUARIO (solo administrador en el alcance base)
-- ---------------------------------------------------------
CREATE TABLE IF NOT EXISTS usuario (
  id_usuario INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  nombre     VARCHAR(100) NOT NULL,
  correo     VARCHAR(100) NOT NULL UNIQUE,
  clave      VARCHAR(255) NOT NULL COMMENT 'password_hash de PHP',
  rol        VARCHAR(20)  NOT NULL DEFAULT 'administrador',
  activo     TINYINT(1)   NOT NULL DEFAULT 1 COMMENT '1=puede entrar, 0=desactivado'
) ENGINE=InnoDB;

-- ---------------------------------------------------------
-- 2. CATEGORIA
-- ---------------------------------------------------------
CREATE TABLE IF NOT EXISTS categoria (
  id_categoria INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  nombre       VARCHAR(100) NOT NULL UNIQUE
) ENGINE=InnoDB;

-- ---------------------------------------------------------
-- 3. PRODUCTO
-- ---------------------------------------------------------
CREATE TABLE IF NOT EXISTS producto (
  id_producto   INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  codigo        VARCHAR(30)  NOT NULL UNIQUE,
  nombre        VARCHAR(100) NOT NULL,
  descripcion   TEXT NULL,
  precio        DECIMAL(10,2) NOT NULL DEFAULT 0,
  stock_actual  INT NOT NULL DEFAULT 0,
  stock_minimo  INT NOT NULL DEFAULT 0,
  id_categoria  INT UNSIGNED NOT NULL,
  CONSTRAINT fk_producto_categoria
    FOREIGN KEY (id_categoria) REFERENCES categoria(id_categoria)
    ON UPDATE CASCADE
    ON DELETE RESTRICT
) ENGINE=InnoDB;

-- ---------------------------------------------------------
-- 4. MOVIMIENTO (historial: no se edita ni se borra)
-- ---------------------------------------------------------
CREATE TABLE IF NOT EXISTS movimiento (
  id_movimiento INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  tipo          VARCHAR(10)  NOT NULL COMMENT 'entrada o salida',
  cantidad      INT NOT NULL,
  fecha         DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  motivo        VARCHAR(150) NULL,
  id_producto   INT UNSIGNED NOT NULL,
  id_usuario    INT UNSIGNED NOT NULL,
  CONSTRAINT fk_movimiento_producto
    FOREIGN KEY (id_producto) REFERENCES producto(id_producto)
    ON UPDATE CASCADE
    ON DELETE RESTRICT,
  CONSTRAINT fk_movimiento_usuario
    FOREIGN KEY (id_usuario) REFERENCES usuario(id_usuario)
    ON UPDATE CASCADE
    ON DELETE RESTRICT
) ENGINE=InnoDB;

-- ---------------------------------------------------------
-- DATOS DE EJEMPLO
-- Correo: admin@tracestock.com
-- Contraseña: admin123
-- ---------------------------------------------------------
INSERT INTO usuario (nombre, correo, clave, rol, activo) VALUES
('Administrador TraceStock', 'admin@tracestock.com',
 '$2y$10$wlDcsNsti.pP1yyYpydthO/xBNfL870QQVuA4w1K0im2Orn5Sxu3i',
 'administrador', 1);

INSERT INTO categoria (nombre) VALUES
('Herramientas'),
('Electricidad'),
('Ferretería');

INSERT INTO producto (codigo, nombre, descripcion, precio, stock_actual, stock_minimo, id_categoria) VALUES
('ELE-001', 'Cinta aislante',   'Rollo de cinta aislante negra',     3500.00,  3,  10, 2),
('FER-001', 'Chazo plástico',   'Caja de chazos plásticos',          1200.00, 90, 150, 3),
('HER-001', 'Martillo',         'Martillo de carpintero',            25000.00, 2,   5, 1),
('ELE-002', 'Cable 12 AWG',     'Rollo de cable calibre 12',         45000.00, 45, 20, 2),
('HER-002', 'Destornillador',   'Destornillador plano',               8000.00, 18,  5, 1),
('FER-002', 'Tornillos',        'Caja de tornillos surtidos',         9500.00, 200, 50, 3),
('HER-003', 'Alicates',         'Alicates de corte',                 15000.00, 12,  4, 1);

INSERT INTO movimiento (tipo, cantidad, fecha, motivo, id_producto, id_usuario) VALUES
('entrada', 7,  '2026-10-12 09:15:00', 'Reposición', 1, 1),
('salida',  4,  '2026-10-12 13:48:00', NULL,         1, 1),
('salida',  60, '2026-10-13 09:48:00', 'Venta',      2, 1),
('salida',  3,  '2026-10-14 07:48:00', NULL,         3, 1),
('entrada', 10, '2026-10-14 12:48:00', 'Compra',     4, 1),
('salida',  15, '2026-10-14 15:59:00', NULL,         4, 1);
