-- Crear la base de datos
-- Crear la base de datos
CREATE DATABASE IF NOT EXISTS refugio;
USE refugio;

-- 1. Tipos de mascota
CREATE TABLE TiposMascota (
    id_tipo INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL,
    descripcion VARCHAR(255),
    estado ENUM('Activo','Inactivo') DEFAULT 'Activo' -- borrado lógico
);

-- 2. Mascotas
CREATE TABLE Mascotas (
    id_mascota INT AUTO_INCREMENT PRIMARY KEY,
    id_tipo INT NOT NULL,
    nombre VARCHAR(100) NOT NULL,
    foto VARCHAR(255),
    estado ENUM('Activo','Inactivo') DEFAULT 'Activo', -- borrado lógico
    estado_adopcion ENUM('Disponible','Adoptado') DEFAULT 'Disponible', -- control adopción
    CONSTRAINT fk_mascota_tipo
        FOREIGN KEY (id_tipo) REFERENCES TiposMascota(id_tipo)
        ON UPDATE CASCADE ON DELETE RESTRICT
);

-- 3. Adoptantes
CREATE TABLE Adoptantes (
    id_adoptante INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    telefono VARCHAR(20),
    id_mascota INT UNIQUE, -- para que una mascota solo pueda tener un adoptante
    estado ENUM('Activo','Inactivo') DEFAULT 'Activo', -- borrado lógico
    CONSTRAINT fk_adoptante_mascota
        FOREIGN KEY (id_mascota) REFERENCES Mascotas(id_mascota)
        ON UPDATE CASCADE ON DELETE SET NULL
);
