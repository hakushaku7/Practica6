
-- Crear la base de datos
CREATE DATABASE IF NOT EXISTS Concesionario;
USE Concesionario;

-- Crear tabla 'Marca'
CREATE TABLE Marca (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(255) NOT NULL
);

-- Crear tabla 'Vehículo'
CREATE TABLE Vehiculo (
    id INT AUTO_INCREMENT PRIMARY KEY,
    modelo VARCHAR(255) NOT NULL,
    matricula VARCHAR(255) NOT NULL UNIQUE,
    kilometros INT NOT NULL,
    color VARCHAR(255) NOT NULL,
    reservado TINYINT(1) NOT NULL DEFAULT 0,
    marca_id INT,
    FOREIGN KEY (marca_id) REFERENCES Marca(id)
);

-- Crear tabla 'Reserva'
CREATE TABLE Reserva (
    id INT AUTO_INCREMENT PRIMARY KEY,
    vehiculo_id INT,
    nombre VARCHAR(255) NOT NULL,
    apellidos VARCHAR(255) NOT NULL,
    dni VARCHAR(9) NOT NULL,
    UNIQUE (vehiculo_id),
    FOREIGN KEY (vehiculo_id) REFERENCES Vehiculo(id)
);


-- Insertamos temáticas básicas
INSERT INTO `Marca` (`nombre`) VALUES
('SEAT'),
('Ford'),
('Toyota'),
('KIA'),
('Audi')



