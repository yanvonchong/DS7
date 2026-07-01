-- ============================================================
--  PARCIAL PRÁCTICO #1 - iTECH Event Registration
--  Universidad Tecnológica de Panamá
--  Ingeniería de Software
-- ============================================================

CREATE DATABASE IF NOT EXISTS parcial1
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE parcial1;

-- ------------------------------------------------------------
-- Tabla: paises
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS paises (
    id_pais     INT          NOT NULL AUTO_INCREMENT,
    nombre_pais VARCHAR(100) NOT NULL,
    PRIMARY KEY (id_pais)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ------------------------------------------------------------
-- Tabla: areas_interes
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS areas_interes (
    id_area     INT          NOT NULL AUTO_INCREMENT,
    nombre_area VARCHAR(100) NOT NULL,
    PRIMARY KEY (id_area)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ------------------------------------------------------------
-- Tabla: inscriptores
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS inscriptores (
    id_inscriptor  INT           NOT NULL AUTO_INCREMENT,
    identidad      VARCHAR(20)   NOT NULL,
    nombre         VARCHAR(100)  NOT NULL,
    apellido       VARCHAR(100)  NOT NULL,
    edad           TINYINT       NOT NULL,
    sexo           ENUM('M','F','Otro') NOT NULL,
    id_pais        INT           NOT NULL,
    nacionalidad   VARCHAR(100)  NOT NULL,
    correo         VARCHAR(150)  NOT NULL,
    celular        VARCHAR(20)   NOT NULL,
    observaciones  TEXT,
    fecha_registro DATETIME      DEFAULT CURRENT_TIMESTAMP,
    firma          TEXT          COMMENT 'Firma digital OpenSSL en base64',
    PRIMARY KEY (id_inscriptor),
    UNIQUE KEY uq_identidad (identidad),
    UNIQUE KEY uq_correo    (correo),
    CONSTRAINT fk_inscriptor_pais
        FOREIGN KEY (id_pais) REFERENCES paises(id_pais)
        ON DELETE RESTRICT
        ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ------------------------------------------------------------
-- Tabla: inscriptor_areas  (relación M:N)
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS inscriptor_areas (
    id_inscriptor INT NOT NULL,
    id_area       INT NOT NULL,
    PRIMARY KEY (id_inscriptor, id_area),
    CONSTRAINT fk_ia_inscriptor
        FOREIGN KEY (id_inscriptor) REFERENCES inscriptores(id_inscriptor)
        ON DELETE RESTRICT
        ON UPDATE CASCADE,
    CONSTRAINT fk_ia_area
        FOREIGN KEY (id_area) REFERENCES areas_interes(id_area)
        ON DELETE RESTRICT
        ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ------------------------------------------------------------
-- Datos iniciales: países (muestra)
-- ------------------------------------------------------------
INSERT INTO paises (nombre_pais) VALUES
('Panamá'), ('Costa Rica'), ('Colombia'), ('Venezuela'),
('México'), ('Argentina'), ('Chile'), ('Perú'),
('Ecuador'), ('Guatemala'), ('Honduras'), ('El Salvador'),
('Nicaragua'), ('Cuba'), ('República Dominicana'),
('Puerto Rico'), ('Bolivia'), ('Paraguay'), ('Uruguay'),
('Brasil'), ('España'), ('Estados Unidos'), ('Otro');

-- ------------------------------------------------------------
-- Datos iniciales: áreas de interés
-- ------------------------------------------------------------
INSERT INTO areas_interes (nombre_area) VALUES
('Cloud Computing'),
('Big Data'),
('Desarrollo Móvil'),
('Ciberseguridad'),
('IoT (Internet de las Cosas)'),
('Machine Learning'),
('DevOps'),
('Python');
