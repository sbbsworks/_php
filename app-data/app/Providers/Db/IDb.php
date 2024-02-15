<?php

declare(strict_types=1);

namespace App\Providers\Db;

interface IDb {
    public function begin(): void;

    public function commit(): void;

    public function rollback(): void;

    public function exec(string $query, array $parameters = []): int;

    public function all(string $query, array $parameters = []): array;

    public function one(string $query, array $parameters = []): array|null;

    public function lastId(): bool|string;
}
