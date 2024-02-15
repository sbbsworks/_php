<?php

declare(strict_types=1);

namespace App\Providers\Notifiers;

use \CURLFile;

class TelegramService implements ISocialNotifier
{
    public function __construct(
        private NotifiersEnvConfig $config
    ){}

    public function notify(object $parameters): object
    {
        $errors = [];
        $user = $parameters->user;
        $file = $parameters->files[0];
        $_file = $file ? new CURLFile($file) : null;
        $action = $_file ? 'sendDocument' : 'sendMessage';
        $key = trim($this->config->TG_BOT_API_KEY);
        $apiUrl = 'https://api.telegram.org/bot' . $key . DIRECTORY_SEPARATOR . $action;
        $ch = curl_init();
        curl_setopt($ch,CURLOPT_USERAGENT,'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.17 (KHTML, like Gecko) Chrome/24.0.1312.52 Safari/537.17');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, [
            'chat_id' => $user->telegram,
            'text' => 'You have been notified',
            'document' => $_file,
        ]);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT ,30);
        curl_setopt($ch, CURLOPT_URL, $apiUrl);
        $res = curl_exec($ch);
        if (curl_errno($ch)) {
            $errors[] = curl_error($ch);
        }
        curl_close($ch);
        $res = is_string($res) ? json_decode($res) : null;

        return (object)[
            'result' => $res?->ok ? ['Telegrammed'] : [],
            'errors' => $res?->ok ? [] : [json_encode($res->description), ...array_filter($errors)],
        ];
    }
}
