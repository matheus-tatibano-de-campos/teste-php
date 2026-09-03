-- =============================================================
-- Sistema de Ordem de Serviços - JM Informática
-- Script de criação das tabelas
-- =============================================================

CREATE DATABASE IF NOT EXISTS jm_servicos
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE jm_servicos;

-- -------------------------------------------------------------
-- Tabela: user
-- Armazena os usuários (funcionários) do sistema
-- -------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `user` (
  `id_user`    BIGINT(20)   NOT NULL AUTO_INCREMENT,
  `name`       VARCHAR(150) NOT NULL,
  `email`      VARCHAR(100) NOT NULL UNIQUE,
  `password`   VARCHAR(255) NOT NULL COMMENT 'Hash gerado por password_hash()',
  `created_at` DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `update_at`  DATETIME     NULL     ON UPDATE CURRENT_TIMESTAMP,
  `ativo`      TINYINT(1)   NOT NULL DEFAULT 1,
  PRIMARY KEY (`id_user`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------------
-- Tabela: service
-- Armazena as ordens de serviço prestadas pelos funcionários
-- finished_at NULL = Pendente | finished_at preenchido = Finalizado
-- -------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `service` (
  `id_service`      BIGINT(20)    NOT NULL AUTO_INCREMENT,
  `description`     VARCHAR(45)   NOT NULL,
  `price`           DECIMAL(11,3) NOT NULL,
  `created_at`      DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `update_at`       DATETIME      NULL     ON UPDATE CURRENT_TIMESTAMP,
  `finished_at`     DATETIME      NULL     COMMENT 'Data de finalização do serviço',
  `commission_user` DECIMAL(11,3) NULL     COMMENT 'Valor da comissão calculada ao finalizar',
  `user_id_user`    BIGINT(20)    NOT NULL,
  PRIMARY KEY (`id_service`),
  CONSTRAINT `fk_service_user`
    FOREIGN KEY (`user_id_user`) REFERENCES `user` (`id_user`)
    ON DELETE RESTRICT
    ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------------
-- Usuário de teste para primeiro acesso
-- Senha: 123456
-- -------------------------------------------------------------
INSERT INTO `user` (`name`, `email`, `password`, `ativo`) VALUES
('José Silva', 'jose@jm.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1);
