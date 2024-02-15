<?php

declare(strict_types=1);

namespace App\Providers\Db;

use PDO;
use PDOException;
use PDOStatement;

class Db implements IDb
{
    private static $instance;
    private $pdo;

    private $query;
    private $parameters;

    private function __construct(private DbEnvConfig $config) {}

    private function __clone()
    {
    }
    public static function getInstance(DbEnvConfig $config): self
    {
        if (!isset(self::$instance)) {
            self::$instance = new self($config);
        }
        return self::$instance;
    }

    private function connect(): ?PDO
    {
        if (!isset($this->pdo)) {
            $db = $this->config->db;
            $host = $this->config->host;
            $port = $this->config->port;
            $user = $this->config->user;
            $password = $this->config->password;
            $dsn = "pgsql:host=$host;port=$port;dbname=$db;user=$user;password=$password";
            $this->pdo = new PDO($dsn);
        }
        return $this->pdo;
    }

    private function callDB(): PDOStatement
    {
        if(!$this->pdo) {
            $this->connect();
        }
        $callDB = $this->pdo->prepare($this->query);
        $result = $callDB->execute($this->parameters);
        if (!$result) {
            throw new PDOException(implode(' ', $callDB->errorInfo()));
        }
        return $callDB;
    }

    public function begin(): void
    {
        if(!$this->pdo) {
            $this->connect();
        }
        $this->pdo->beginTransaction();
    }

    public function commit(): void
    {
        $this->pdo->commit();
    }

    public function rollback(): void
    {
        $this->pdo->rollBack();
    }

    public function exec(string $query, array $parameters = []): int
    {
        $this->query = $query;
        $this->parameters = $parameters;
        return $this->callDB()->rowCount();
    }

    public function all(string $query, array $parameters = []): array
    {
        $this->query = $query;
        $this->parameters = $parameters;
        $callDB = $this->callDB();
        return $callDB->fetchAll(PDO::FETCH_ASSOC);
    }

    public function one(string $query, array $parameters = []): array|null
    {
        $this->query = $query;
        $this->parameters = $parameters;
        $callDB = $this->callDB();
        if (!$callDB ) {
            return null;
        }

        $result = $callDB->fetchAll(PDO::FETCH_ASSOC);
        reset($result);
        if (!is_array($result)) {
            $result = [];
        }
        return $result[0] ?? null;
    }

    public function lastId(): bool|string
    {
        $this->connect();
        return $this->pdo->lastInsertId();
    }
}
