-- database.sql
-- Crear la base de datos
CREATE DATABASE IF NOT EXISTS crud_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE crud_db;

-- Tabla de productos
CREATE TABLE IF NOT EXISTS productos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(255) NOT NULL,
    descripcion TEXT,
    precio DECIMAL(10, 2) NOT NULL DEFAULT 0.00,
    stock INT NOT NULL DEFAULT 0,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabla de registro de accesos de usuarios
CREATE TABLE IF NOT EXISTS registro_accesos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario VARCHAR(100) NOT NULL,
    ip_address VARCHAR(45) NOT NULL,
    user_agent TEXT,
    fecha_ingreso DATETIME NOT NULL,
    fecha_salida DATETIME NULL,
    duracion_sesion INT NULL COMMENT 'Duración en segundos',
    INDEX idx_usuario (usuario),
    INDEX idx_fecha_ingreso (fecha_ingreso)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insertar algunos datos de ejemplo (opcional)
INSERT INTO productos (nombre, descripcion, precio, stock) VALUES
('Laptop Dell XPS 15', 'Laptop de alto rendimiento con procesador Intel i7', 1299.99, 15),
('Mouse Logitech MX Master', 'Mouse inalámbrico ergonómico para productividad', 99.99, 50),
('Teclado Mecánico Keychron', 'Teclado mecánico RGB con switches azules', 149.99, 30);