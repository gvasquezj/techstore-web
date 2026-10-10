-- Script SQL de Creacion de Base de Datos y Tablas
-- Asignatura: Programacion Web II - Semana 6
-- Base de Datos: TIENDA
-- Proyecto: TechStore

CREATE DATABASE IF NOT EXISTS TIENDA CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE TIENDA;

-- 1. Tabla PRODUCTO
CREATE TABLE IF NOT EXISTS PRODUCTO (
    id_producto INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    descripcion TEXT,
    precio INT NOT NULL,
    stock INT NOT NULL
) ENGINE=InnoDB;

-- 2. Tabla CLIENTE
CREATE TABLE IF NOT EXISTS CLIENTE (
    id_cliente INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    direccion VARCHAR(150) NOT NULL
) ENGINE=InnoDB;

-- 3. Tabla COMPRA
CREATE TABLE IF NOT EXISTS COMPRA (
    id_compra INT AUTO_INCREMENT PRIMARY KEY,
    cantidad INT NOT NULL,
    total INT NOT NULL,
    fecha DATE NOT NULL,
    id_producto INT NOT NULL,
    id_cliente INT NOT NULL,
    FOREIGN KEY (id_producto) REFERENCES PRODUCTO(id_producto) ON DELETE CASCADE,
    FOREIGN KEY (id_cliente) REFERENCES CLIENTE(id_cliente) ON DELETE CASCADE
) ENGINE=InnoDB;
