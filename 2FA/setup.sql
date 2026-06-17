-- ============================================================
-- SETUP 2FA - Desarrollo de Software VII | UTP
-- Ejecutar en la base de datos: company_info
-- ============================================================

-- 1. Crear tabla de usuarios (si no existe)
CREATE TABLE IF NOT EXISTS `usuarios` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `Nombre` VARCHAR(255) NOT NULL,
  `Apellido` VARCHAR(255) NOT NULL,
  `Usuario` VARCHAR(255) NOT NULL,
  `Correo` VARCHAR(255) NOT NULL,
  `HashMagic` VARCHAR(255) NOT NULL,
  `Sexo` CHAR(1) NOT NULL,
  `FechaSistema` DATETIME NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 2. Agregar columna secret_2fa (si no existe)
ALTER TABLE `usuarios`
  ADD COLUMN IF NOT EXISTS `secret_2fa` VARCHAR(255) NULL AFTER `HashMagic`;

-- 3. Crear tabla de intentos de login (auditoría)
CREATE TABLE IF NOT EXISTS `intentos_login` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `Usuario` VARCHAR(255) NOT NULL,
  `ipRemoto` VARCHAR(255) NULL,
  `timestamp` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deteccion_anomalia` TINYINT(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- NOTA: Si ADD COLUMN IF NOT EXISTS falla en tu versión MySQL,
-- usa esta alternativa:
-- ALTER TABLE `usuarios` ADD `secret_2fa` VARCHAR(255) NULL AFTER `HashMagic`;
-- ============================================================
