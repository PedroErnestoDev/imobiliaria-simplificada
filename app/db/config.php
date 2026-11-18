<?php

declare(strict_types=1);

final class Database
{
    private const HOST = 'db';
    private const DATABASE = 'imobiliaria';
    private const USERNAME = 'root';
    private const PASSWORD = 'root123456';
    private const CHARSET = 'utf8mb4';
    private const PORT = 3306;

    private ?PDO $connection = null;

    public function connect(): PDO
    {
        if ($this->connection instanceof PDO) {
            return $this->connection;
        }

        $dsn = sprintf(
            'mysql:host=%s;port=%d;dbname=%s;charset=%s',
            self::HOST,
            self::PORT,
            self::DATABASE,
            self::CHARSET
        );

        try {
            $this->connection = new PDO(
                $dsn,
                self::USERNAME,
                self::PASSWORD,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                    PDO::ATTR_TIMEOUT => 5,
                ]
            );

            return $this->connection;

        } catch (PDOException $e) {
            error_log('[Database] ' . $e->getMessage());

            throw new RuntimeException(
                'Erro ao conectar ao banco de dados.',
                0,
                $e
            );
        }
    }
}

$database = new Database();
$pdo = $database->connect();
