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

    /**
     * Busca um serviço pelo ID.
     *
     * @param int $id
     * @return array|false
     */
    public function findById($id)
    {
        $sql = 'SELECT
                    id_service,
                    description,
                    price,
                    finished_at,
                    commission_user,
                    created_at,
                    user_id_user
                FROM `service`
                WHERE id_service = :id
                LIMIT 1';

        return $this->fetchOne($sql, ['id' => $id]);
    }

    /**
     * Cadastra um novo serviço (sempre inicia como Pendente).
     *
     * @param string $description
     * @param float  $price
     * @param int    $userId
     * @return bool
     */
    public function create($description, $price, $userId)
    {
        $sql = 'INSERT INTO `service` (description, price, user_id_user, finished_at)
                VALUES (:description, :price, :user_id, NULL)';

        return $this->execute($sql, [
            'description' => $description,
            'price'       => $price,
            'user_id'     => $userId,
        ]);
    }

    /**
     * Atualiza descrição e preço de um serviço.
     *
     * @param int    $id
     * @param string $description
     * @param float  $price
     * @return bool
     */
    public function update($id, $description, $price)
    {
        $sql = 'UPDATE `service`
                SET description = :description,
                    price = :price
                WHERE id_service = :id';

        return $this->execute($sql, [
            'description' => $description,
            'price'       => $price,
            'id'          => $id,
        ]);
    }

    /**
     * Exclui um serviço pelo ID.
     *
     * @param int $id
     * @return bool
     */
    public function delete($id)
    {
        $sql = 'DELETE FROM `service` WHERE id_service = :id';

        return $this->execute($sql, ['id' => $id]);
    }

    /**
     * Calcula a comissão conforme o valor do serviço.
     *
     * - até R$ 1.000,00       → 5%
     * - acima de R$ 1.000,00  → 10%
     * - acima de R$ 10.000,00 → 20%
     *
     * @param float $price
     * @return float
     */
    public function calculateCommission($price)
    {
        $price = (float) $price;

        if ($price > 10000) {
            return round($price * 0.20, 3);
        }

        if ($price > 1000) {
            return round($price * 0.10, 3);
        }

        return round($price * 0.05, 3);
    }

    /**
     * Finaliza um serviço pendente: grava data e comissão.
     *
     * @param int $id
     * @return bool
     */
    public function finish($id)
    {
        $service = $this->findById($id);

        if (!$service || $service['finished_at'] !== null) {
            return false;
        }

        $commission = $this->calculateCommission($service['price']);

        $sql = 'UPDATE `service`
                SET finished_at = NOW(),
                    commission_user = :commission
                WHERE id_service = :id
                  AND finished_at IS NULL';

        return $this->execute($sql, [
            'commission' => $commission,
            'id'         => $id,
        ]);
    }
}
