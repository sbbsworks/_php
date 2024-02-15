<?php

declare(strict_types=1);

namespace App\Providers\Notifiers;
use App\Exceptions\EnvException;

class NotifiersEnvConfig
{
    public string|null $MAIL_DSN;
    public string|null $FROM_EMAIL;
    public string|null $TG_BOT_API_KEY;

    public function __construct()
    {
        $notifiersEnvPath = __DIR__ . '/.env';
        $notifiersEnv = [];
        array_map(function(string $item) use (&$notifiersEnv) {
            [$key, $value] = explode('=', $item);
            $notifiersEnv[$key] = $value;
        }, file($notifiersEnvPath) ?? []);
        $notifiersEnv = [
            'MAIL_DSN' => $notifiersEnv['MAIL_DSN'],
            'FROM_EMAIL' => $notifiersEnv['FROM_EMAIL'],
            'TG_BOT_API_KEY' => $notifiersEnv['TG_BOT_API_KEY'],
        ];
        $missing = [];
        foreach($notifiersEnv as $key => $value) {
            if(!$value) {
                $missing[] = $key;
            }
            $this->{$key} = $value;
        }
        if(count($missing)) {
            throw new EnvException('Notifier Env vars validation failed: ' . implode(' ', $missing));
        }
    }
}
