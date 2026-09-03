<?php
/**
 * Model User — Acesso aos dados da tabela `user`
 *
 * Responsável por buscar, criar e validar usuários no banco.
 */

namespace App\Models;

use App\Core\Model;

class User extends Model
{
    /**
     * Busca um usuário ativo pelo e-mail.
     *
     * @param string $email
     * @return array|false
     */
    public function findByEmail(string $email)
    {
        $sql = 'SELECT id_user, name, email, password, ativo
                FROM `user`
                WHERE email = :email AND ativo = 1
                LIMIT 1';

        return $this->fetchOne($sql, ['email' => $email]);
    }

    /**
     * Verifica se o e-mail já está cadastrado.
     *
     * @param string $email
     * @return bool
     */
    public function emailExists(string $email): bool
    {
        $sql = 'SELECT id_user FROM `user` WHERE email = :email LIMIT 1';
        $user = $this->fetchOne($sql, ['email' => $email]);

        return $user !== false;
    }

    /**
     * Busca um usuário pelo ID.
     *
     * @param int $id
     * @return array|false
     */
    public function findById($id)
    {
        $sql = 'SELECT id_user, name, email, ativo
                FROM `user`
                WHERE id_user = :id
                LIMIT 1';

        return $this->fetchOne($sql, ['id' => $id]);
    }

    /**
     * Cadastra um novo usuário com senha criptografada.
     *
     * @param string $name
     * @param string $email
     * @param string $plainPassword Senha em texto puro (será hasheada aqui)
     * @return bool
     */
    public function create(string $name, string $email, string $plainPassword): bool
    {
        $sql = 'INSERT INTO `user` (name, email, password, ativo)
                VALUES (:name, :email, :password, 1)';

        return $this->execute($sql, [
            'name'     => $name,
            'email'    => $email,
            'password' => password_hash($plainPassword, PASSWORD_DEFAULT),
        ]);
    }
}
