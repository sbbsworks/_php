<?php

declare(strict_types=1);

namespace App\Services\Notify;

use App\Exceptions\NotifierException;
use App\Providers\Notifiers\IEmailNotifier;
use App\Providers\Notifiers\ISocialNotifier;
use App\Providers\Router\Routes;
use App\Services\File\FileService;
use App\Services\User\UserService;
use App\View\View;

class NotifyService
{
    private string $redirectPath = '';
    public function __construct(
        private FileService $fileService,
        private UserService $userService,
        private IEmailNotifier $email,
        private ISocialNotifier $social
    )
    {
        $this->redirectPath = Routes::Root->value . '/' .  Routes::Notify->value;
    }
    public function index(): View
    {
        return View::make(__METHOD__, [
            'users' => $this->userService->getAll(),
            'fileIsUploaded' => $this->fileService->checkFileExists(),
            'fileName' => $this->fileService->getDefaultFileName(),
        ]);
    }
    public function notify(): void
    {
        $userId = $_POST['notified_user'] ?? null;
        $user = $this->userService->getOne($userId);
        $files = [];
        foreach($_POST as $key => $value) {
            if($value === 'on') {
                $files[] = $this->fileService->getFullFilePath($key);
            }
        }
        if(!$user) {
            throw new NotifierException('User not found', 400);
        }
        if(!$files) {
            throw new NotifierException('Files not found', 400);
        }
        $user = (object)$user;
        $emailed = (object)['result' => [], 'errors' => []];
        if($user->email && $files[0]) {
            $emailed = $this->email->notify((object)[
                'user' => $user,
                'files' => $files,
            ]);
        }
        $telegrammed = (object)['result' => [], 'errors' => []];
        if($user->telegram && $files[0]) {
            $telegrammed = $this->social->notify((object)[
                'user' => $user,
                'files' => $files,
            ]);
        }
        if($user->telegram) {
            $telegrammed = $this->social->notify((object)[
                'user' => $user,
                'files' => [],
            ]);
        }
        if(count($emailed->result) || count($telegrammed->result)) {
            $this->fileService->unlink();
        }
        session_start();
        $_SESSION['notified-data'] = $emailed->errors || $telegrammed->errors ? (object)$_POST : null;
        $_SESSION['notify-results'] = [$emailed->result, $telegrammed->result];
        $_SESSION['notify-errors'] = [$emailed->errors, $telegrammed->errors];
        session_write_close();
        header("location: $this->redirectPath");
        exit;
    }
}
