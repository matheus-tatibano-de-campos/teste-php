<?php
/**
 * Model Service — Acesso aos dados da tabela `service`
 *
 * Status do serviço:
 * - finished_at NULL      → Pendente
 * - finished_at preenchido → Finalizado
 */

namespace App\Models;

use App\Core\Model;

class Service extends Model
{
    /**
     * Lista todos os serviços com o nome do usuário responsável.
     * Ordenado do mais recente para o mais antigo.
     *
     * @return array
     */
    public function findAllWithUser()
    {
        $sql = 'SELECT
                    s.id_service,
                    s.description,
                    s.price,
                    s.finished_at,
                    s.commission_user,
                    s.created_at,
                    s.user_id_user,
                    u.name AS user_name,
                    CASE
                        WHEN s.finished_at IS NULL THEN \'Pendente\'
                        ELSE \'Finalizado\'
                    END AS status
                FROM `service` s
                INNER JOIN `user` u ON u.id_user = s.user_id_user
                ORDER BY s.created_at DESC';

        return $this->fetchAll($sql);
    }

    /**
     * Soma o valor total dos serviços de um usuário.
     *
     * @param int $userId
     * @return float
     */
    public function getTotalByUser($userId)
    {
        $sql = 'SELECT COALESCE(SUM(price), 0) AS total
                FROM `service`
                WHERE user_id_user = :user_id';

        $row = $this->fetchOne($sql, ['user_id' => $userId]);

        return (float) $row['total'];
    }

    /**
     * Últimos serviços de um usuário (qualquer status).
     *
     * @param int $userId
     * @param int $limit
     * @return array
     */
    public function getLastByUser($userId, $limit = 5)
    {
        $sql = 'SELECT id_service, description, finished_at
                FROM `service`
                WHERE user_id_user = :user_id
                ORDER BY created_at DESC
                LIMIT ' . (int) $limit;

        return $this->fetchAll($sql, ['user_id' => $userId]);
    }

    /**
     * Últimos serviços pendentes de um usuário (sem data de finalização).
     *
     * @param int $userId
     * @param int $limit
     * @return array
     */
    public function getPendingByUser($userId, $limit = 5)
    {
        $sql = 'SELECT id_service, description
                FROM `service`
                WHERE user_id_user = :user_id
                  AND finished_at IS NULL
                ORDER BY created_at DESC
                LIMIT ' . (int) $limit;

        return $this->fetchAll($sql, ['user_id' => $userId]);
    }
}
