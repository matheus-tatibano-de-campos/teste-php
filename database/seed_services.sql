-- =============================================================
-- Dados de exemplo para testar o Dashboard (Camada 4)
-- Execute no phpMyAdmin APÓS o schema.sql
-- Usuário: José Silva (id_user = 1)
-- =============================================================

USE jm_servicos;

INSERT INTO `service` (`description`, `price`, `user_id_user`, `finished_at`, `commission_user`) VALUES
('Troca de Tela LED',        425.000, 1, NULL, NULL),
('Limpeza de Computador',    100.000, 1, NOW(), 5.000),
('Troca de pasta térmica',    80.000, 1, NULL, NULL),
('Instalação de Office',     150.000, 1, NULL, NULL),
('Reparo de Sistema',        200.000, 1, NULL, NULL);
