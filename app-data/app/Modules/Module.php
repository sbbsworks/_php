<?php

declare(strict_types=1);

namespace App\Modules;
use App\Controllers\File\FileController;
use App\Providers\Container\Container;
use App\Providers\Db\DbEnvConfig;
use App\Providers\Db\IDb;
use App\Providers\Notifiers\GmailService;
use App\Providers\Notifiers\IEmailNotifier;
use App\Providers\Notifiers\NotifiersEnvConfig;
use App\Providers\Router\Router;

use App\Controllers\Home\HomeController;
use App\Controllers\Notify\NotifyController;
use App\Controllers\User\UserController;
use App\Providers\Db\Db;
use App\Services\File\FileService;
use App\Services\Home\HomeService;
use App\Services\Notify\NotifyService;
use App\Providers\Notifiers\ISocialNotifier;
use App\Providers\Notifiers\TelegramService;
use App\Services\User\UserService;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\Transport;

class Module
{
    public Router $router;
    public Container $container;
    public function __construct(){
        (new DbEnvConfig());
        $this->container = (new Container())
            ->set(
                HomeService::class,
                HomeService::class
            )
            ->set(
                UserService::class,
                UserService::class
            )
            ->set(
                FileService::class,
                FileService::class
            )
            ->set(
                NotifyService::class,
                NotifyService::class
            )
            ->set(
                DbEnvConfig::class,
                DbEnvConfig::class
            )
            ->set(
                IDb::class,
                fn(Container $c) => Db::getInstance($c->get(DbEnvConfig::class))
            )
            ->set(
                NotifiersEnvConfig::class,
                NotifiersEnvConfig::class
            )
            ->set(
                Mailer::class,
                fn() => (new Mailer(Transport::fromDsn((new NotifiersEnvConfig())->MAIL_DSN)))
            )
            ->set(
                IEmailNotifier::class,
                GmailService::class
            )
            ->set(
                ISocialNotifier::class,
                TelegramService::class
            );
    
        $this->router = (new Router($this->container))
            ->registerRoutesFromAttributes([
                HomeController::class,
                UserController::class,
                FileController::class,
                NotifyController::class
            ]);
    }

}
