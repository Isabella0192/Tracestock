-- TraceStock · Base de datos del proyecto productivo
-- Ficha 3260309 · HTML + CSS + PHP + MySQL (XAMPP)
-- Importar: DROP + CREATE + datos demo creíbles

DROP DATABASE IF EXISTS tracestock;

CREATE DATABASE tracestock
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE tracestock;

-- ---------------------------------------------------------
-- 1. USUARIO
-- ---------------------------------------------------------
CREATE TABLE usuario (
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
CREATE TABLE categoria (
  id_categoria INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  nombre       VARCHAR(100) NOT NULL UNIQUE
) ENGINE=InnoDB;

-- ---------------------------------------------------------
-- 3. PRODUCTO
-- ---------------------------------------------------------
CREATE TABLE producto (
  id_producto   INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  codigo        VARCHAR(30)  NOT NULL UNIQUE,
  nombre        VARCHAR(100) NOT NULL,
  descripcion   TEXT NULL,
  precio        DECIMAL(10,2) NOT NULL DEFAULT 0,
  stock_actual  INT NOT NULL DEFAULT 0,
  stock_minimo  INT NOT NULL DEFAULT 0,
  imagen        VARCHAR(120) NULL,
  id_categoria  INT UNSIGNED NOT NULL,
  CONSTRAINT fk_producto_categoria
    FOREIGN KEY (id_categoria) REFERENCES categoria(id_categoria)
    ON UPDATE CASCADE
    ON DELETE RESTRICT
) ENGINE=InnoDB;

-- ---------------------------------------------------------
-- 4. MOVIMIENTO (historial: no se edita ni se borra)
-- ---------------------------------------------------------
CREATE TABLE movimiento (
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
-- DATOS DEMO
-- Correo: admin@tracestock.com  |  Contraseña: admin123
-- Operario: operario@tracestock.com | operario123
-- ---------------------------------------------------------
INSERT INTO usuario (nombre, correo, clave, rol, activo) VALUES
('Laura Méndez', 'admin@tracestock.com',
 '$2y$10$XuuBOsmPrGQLtD3NGVajTu1LI8rUh3UrQ8vd70eDfyZSo3CdfAShm',
 'administrador', 1),
('Carlos Ríos', 'operario@tracestock.com',
 '$2y$10$e2Yr9O23TAlcHGg2UnBF1uHF5eVKZ.wPW10px0VgBgOiqBMe5QwoW',
 'operario', 1);

INSERT INTO categoria (nombre) VALUES
('Herramientas manuales'),
('Electricidad'),
('Ferretería'),
('Pinturas y acabados'),
('Maquinaria ligera');

INSERT INTO producto (codigo, nombre, descripcion, precio, stock_actual, stock_minimo, imagen, id_categoria) VALUES
('ELE-001', 'Cinta aislante negra',     'Rollo 20 m · uso eléctrico general',           4500.00,   8,  15, 'prod-cinta.jpg',          2),
('FER-001', 'Chazo plástico 1/4"',      'Caja ×100 · muro seco y concreto',             1800.00,  72, 120, 'prod-chazo.jpg',          3),
('HER-001', 'Martillo de carpintero',   'Mango fibra · cabeza 16 oz',                  28500.00,   3,   6, 'prod-martillo.jpg',       1),
('ELE-002', 'Cable THW 12 AWG',         'Rollo 100 m · cobre',                         52000.00,  38,  20, 'prod-cable.jpg',          2),
('HER-002', 'Destornillador plano 6"',  'Acero cromo-vanadio · mango antideslizante',  9500.00,  22,   8, 'prod-destornillador.jpg', 1),
('FER-002', 'Tornillos autorroscantes', 'Caja ×200 · 1" zincados',                     12500.00, 185,  50, 'prod-tornillos.jpg',      3),
('HER-003', 'Alicates de corte',        '8" aislados 1000 V',                          18900.00,  14,   5, 'prod-alicates.jpg',       1),
('HER-004', 'Llave inglesa 10"',        'Ajuste rápido · acabado mate',                24500.00,   9,   4, 'prod-llave.jpg',          1),
('MAQ-001', 'Taladro percutor 750 W',   'Mandril 13 mm · maletín incluido',            189000.00,  4,   3, 'prod-taladro.jpg',        5),
('ELE-003', 'Interruptor sencillo',     'Blanco · empotrar 110/220 V',                  6200.00,  45,  30, 'prod-interruptor.jpg',    2),
('FER-003', 'Tuercas hexagonales M8',   'Bolsa ×50 · acero galvanizado',                7800.00,  28,  40, 'prod-tuercas.jpg',        3),
('PIN-001', 'Pintura vinilo blanco',    'Galón 4 L · interiores',                       42000.00,  11,  10, 'prod-pintura.jpg',        4),
('MAQ-002', 'Sierra circular 7¼"',      '1400 W · guía láser',                         265000.00,  2,   2, 'prod-sierra.jpg',         5),
('HER-005', 'Juego destornilladores',   '6 piezas · plano y phillips',                 32000.00,   7,  10, 'prod-destornillador.jpg', 1);

INSERT INTO movimiento (tipo, cantidad, fecha, motivo, id_producto, id_usuario) VALUES
('entrada', 20, '2026-09-28 08:30:00', 'Compra proveedor ElectroAndes', 1, 1),
('salida',  12, '2026-09-29 11:15:00', 'Pedido obra Calle 45',           1, 2),
('entrada', 50, '2026-10-01 09:00:00', 'Reposición ferretería',          2, 1),
('salida',  80, '2026-10-02 14:20:00', 'Venta mostrador',                2, 2),
('entrada',  5, '2026-10-03 10:05:00', 'Compra Herramientas del Sur',    3, 1),
('salida',   2, '2026-10-04 16:40:00', 'Salida a taller interno',        3, 2),
('entrada', 15, '2026-10-06 08:50:00', 'Compra cable THW',               4, 1),
('salida',  10, '2026-10-07 13:10:00', 'Instalación residencia',         4, 2),
('entrada', 12, '2026-10-08 09:25:00', 'Compra herramientas',            5, 1),
('salida',   4, '2026-10-09 15:00:00', 'Entrega a cliente',              5, 2),
('entrada', 80, '2026-10-10 08:15:00', 'Pedido mayorista tornillería',   6, 1),
('salida',  30, '2026-10-11 12:30:00', 'Venta caja completa',            6, 2),
('entrada',  8, '2026-10-12 09:15:00', 'Reposición alicates',            7, 1),
('salida',   3, '2026-10-12 13:48:00', 'Uso en obra',                    7, 2),
('entrada',  6, '2026-10-13 10:00:00', 'Compra llaves',                  8, 1),
('salida',   2, '2026-10-13 17:20:00', 'Préstamo taller',                8, 2),
('entrada',  3, '2026-10-14 08:40:00', 'Compra maquinaria',              9, 1),
('salida',   1, '2026-10-14 11:05:00', 'Asignación bodega 2',            9, 1),
('entrada', 40, '2026-10-14 14:00:00', 'Compra interruptores',          10, 1),
('salida',  15, '2026-10-15 09:30:00', 'Proyecto edificio Norte',       10, 2),
('entrada', 20, '2026-10-15 11:45:00', 'Compra tuercas M8',             11, 1),
('salida',  25, '2026-10-15 16:10:00', 'Consumo producción',            11, 2),
('entrada',  8, '2026-10-16 08:20:00', 'Compra pintura',                12, 1),
('salida',   3, '2026-10-16 12:00:00', 'Remodelación oficina',          12, 2),
('entrada',  2, '2026-10-16 14:30:00', 'Compra sierra circular',        13, 1),
('salida',   1, '2026-10-16 16:45:00', 'Asignación carpintería',        13, 1),
('entrada', 10, '2026-10-16 17:00:00', 'Juego destornilladores',        14, 1),
('salida',   5, '2026-10-16 18:10:00', 'Kit para técnicos',             14, 2);
