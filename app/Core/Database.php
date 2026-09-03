<?php
/**
 * Classe Database — Conexão PDO (Singleton)
 *
 * O padrão Singleton garante que apenas UMA instância de conexão
 * com o banco seja criada durante toda a requisição, evitando
 * abrir múltiplas conexões desnecessárias.
 */

namespace App\Core;

use PDO;
use PDOException;

class Database
{
    // Armazena a única instância da conexão
    private static ?PDO $instance = null;

    /**
     * Construtor privado: impede que a classe seja instanciada
     * diretamente com "new Database()".
     */
    private function __construct() {}

    /**
     * Retorna a instância PDO, criando-a na primeira chamada.
     *
     * @return PDO
     */
    public static function getInstance(): PDO
    {
        if (self::$instance === null) {
            $dsn = sprintf(
                'mysql:host=%s;dbname=%s;charset=%s',
                DB_HOST,
                DB_NAME,
                DB_CHARSET
            );

            try {
                self::$instance = new PDO($dsn, DB_USER, DB_PASS, [
                    // Lança exceções em caso de erro SQL
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                    // Retorna resultados como array associativo por padrão
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    // Desativa emulação de prepared statements (mais seguro)
                    PDO::ATTR_EMULATE_PREPARES   => false,
                ]);
            } catch (PDOException $e) {
                // Em produção, nunca exiba detalhes do erro para o usuário
                error_log('Erro de conexão com o banco: ' . $e->getMessage());
                die('Erro interno. Tente novamente mais tarde.');
            }
        }

        return self::$instance;
    }
}
