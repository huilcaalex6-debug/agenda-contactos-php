CREATE DATABASE IF NOT EXISTS agenda_personal CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE agenda_personal;

CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    correo VARCHAR(120) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE categorias (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL
);

INSERT INTO categorias (nombre) VALUES 
('Familia'),
('Amigos'),
('Trabajo'),
('Universidad'),
('Otro');

CREATE TABLE contactos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT NOT NULL,
    id_categoria INT NOT NULL DEFAULT 5,
    nombre VARCHAR(100) NOT NULL,
    telefono VARCHAR(25) NOT NULL,
    correo VARCHAR(120) NOT NULL,
    ciudad VARCHAR(80) NOT NULL,
    favorito TINYINT(1) NOT NULL DEFAULT 0,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id) ON DELETE CASCADE,
    FOREIGN KEY (id_categoria) REFERENCES categorias(id)
);