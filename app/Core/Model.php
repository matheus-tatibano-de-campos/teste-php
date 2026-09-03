<?php
/**
 * Classe Model — Base para todos os Models
 *
 * Disponibiliza a conexão PDO para as classes filhas e
 * fornece métodos auxiliares de query reutilizáveis.
 */

namespace App\Core;

use PDO;

abstract class Model
{
    // Instância PDO compartilhada entre todos os models
    protected PDO $db;

    public function __construct()
    {
        // Obtém a conexão singleton ao instanciar qualquer model
        $this->db = Database::getInstance();
    }

    /**
     * Executa uma query com bind de parâmetros e retorna todos os resultados.
     *
     * @param string $sql    Query SQL com placeholders (:param ou ?)
     * @param array  $params Valores a serem vinculados
     * @return array
     */
    protected function fetchAll(string $sql, array $params = []): array
    {
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * Executa uma query e retorna apenas a primeira linha.
     *
     * @param string $sql
     * @param array  $params
     * @return array|false
     */
    protected function fetchOne(string $sql, array $params = [])
    {
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetch();
    }

    /**
     * Executa uma query de INSERT, UPDATE ou DELETE.
     * Retorna true em caso de sucesso.
     *
     * @param string $sql
     * @param array  $params
     * @return bool
     */
    protected function execute(string $sql, array $params = []): bool
    {
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }

    /**
     * Retorna o ID gerado pelo último INSERT.
     *
     * @return string
     */
    protected function lastInsertId(): string
    {
        return $this->db->lastInsertId();
    }
}
