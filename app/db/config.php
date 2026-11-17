<?php

declare(strict_types=1);

class Database
{
    private string $host = 'db';
    private string $database = 'imobiliaria';
    private string $username = 'root';
    private string $password = 'root123456';
    private string $charset = 'utf8mb4';

    public function connect(): PDO
    {
        try {
            $dsn = "mysql:host={$this->host};dbname={$this->database};charset={$this->charset}";

            return new PDO($dsn, $this->username, $this->password, [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false
            ]);

        } catch (PDOException $e) {
            error_log($e->getMessage());

            throw new RuntimeException(
                'Não foi possível conectar ao banco de dados.'
            );
        }
    }
}