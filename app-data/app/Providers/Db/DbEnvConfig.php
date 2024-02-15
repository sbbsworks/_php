<?php

declare(strict_types=1);

namespace App\Providers\Db;
use App\Exceptions\EnvException;

class DbEnvConfig
{
    public string|null $host;
    public string|null $port;
    public string|null $db;
    public string|null $user;
    public string|null $password;

    public function __construct()
    {
        $dbEnvPath = __DIR__ . '/../../../../containers/postgres/.env';
        $dbEnv = [];
        array_map(function(string $item) use (&$dbEnv) {
            [$key, $value] = explode('=', $item);
            $dbEnv[$key] = $value;
        }, file($dbEnvPath) ?? []);
        $dbEnv = [
            'host' => $dbEnv['POSTGRES_INITDB_CONTAINER'],
            'port' => $dbEnv['POSTGRES_INITDB_PORT'],
            'db' => $dbEnv['POSTGRES_DEFAULT_DB_NAME'],
            'user' => $dbEnv['POSTGRES_INITDB_ROOT_USERNAME'],
            'password' => $dbEnv['POSTGRES_INITDB_ROOT_PASSWORD'],
        ];
        $missing = [];
        foreach($dbEnv as $key => $value) {
            if(!$value) {
                $missing[] = $key;
            }
            $this->{$key} = $value;
        }
        if(count($missing)) {
            throw new EnvException('Env vars validation failed: ' . implode(' ', $missing));
        }
    }
}
