-- Crear la base de datos
CREATE DATABASE IF NOT EXISTS refugio2;
USE refugio2;

-- 0. Usuarios (login y roles)
CREATE TABLE Usuarios (
    id_usuario INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    correo VARCHAR(100) UNIQUE NOT NULL,
    PASSWORD VARCHAR(255) NOT NULL,
    rol ENUM('Administrador','Cliente') DEFAULT 'Cliente',
    estado ENUM('Activo','Inactivo') DEFAULT 'Activo'
);


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
    qr_code VARCHAR(255) UNIQUE,
    CONSTRAINT fk_mascota_tipo
        FOREIGN KEY (id_tipo) REFERENCES TiposMascota(id_tipo)
        ON UPDATE CASCADE ON DELETE RESTRICT
);

-- 3. Adopciones
CREATE TABLE Adopciones (
    id_adopcion INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT NOT NULL, -- cliente que adopta
    id_mascota INT NOT NULL,
    fecha_adopcion DATETIME DEFAULT CURRENT_TIMESTAMP,
    estado ENUM('Pendiente','Aprobada','Rechazada','Finalizada') DEFAULT 'Pendiente',
    CONSTRAINT fk_adopcion_usuario FOREIGN KEY (id_usuario) REFERENCES Usuarios(id_usuario)
        ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT fk_adopcion_mascota FOREIGN KEY (id_mascota) REFERENCES Mascotas(id_mascota)
        ON UPDATE CASCADE ON DELETE CASCADE
);