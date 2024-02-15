<?php

declare(strict_types=1);

namespace App\Services\User;

use App\Providers\Db\IDb;
use App\Providers\Router\Routes;
use App\View\View;
use Exception;
use Throwable;

class UserService
{
    private int $userNameMinLength = 1;
    private int $userNameMaxLength = 100;
    private int $userEmailMinLength = 6;
    private int $userEmailMaxLength = 200;
    private int $userTelegramMinLength = 1;
    private int $userTelegramMaxLength = 200;
    private string $userCreated = 'User has been created';
    private string $userExists = 'User exists';
    private string $userUpdated = 'User has been updated';
    private string $couldNotUpdate = 'Could not update';
    private string $redirectPath = '';
    public function __construct(private IDb $db)
    {
        $this->redirectPath = Routes::Root->value . '/' .  Routes::User->value;
    }
    public function index(): View
    {
        return View::make(__METHOD__, $this->getAll());
    }
    public function getAll(): array
    {
        $query = "
            SELECT *
            FROM users;
        ";
        return $this->db->all($query);
    }
    public function getOne(string $userId): array
    {
        $query = "
            SELECT *
            FROM users
            WHERE
                id = :id
            ;
        ";
        return $this->db->one($query, [':id' => $userId]);
    }
    public function create(): void
    {
        $validatedData = $this->getValidatedData();
        $hasErrors = !!($validatedData->errors);
        $result = null;
        if(!$hasErrors) {
            $values =  implode(', ', $validatedData->values);
            $inserts = implode(', ', $validatedData->inserts);
            $query = "
                INSERT
                INTO users(id, name, $inserts)
                VALUES(:id, :name, $values);
            ";
            $parameters = [
                ':id' => trim(file_get_contents('/proc/sys/kernel/random/uuid')),
                ':name' => $validatedData->name,
                ...$validatedData->parameters,
            ];
            try {
                $result = !!($this->db->exec($query, $parameters));
            } catch (Throwable|\Error|Exception $error) {
                if($error->getCode() === '23505') {
                    $hasErrors = true;
                    $validatedData->errors[] = $this->userExists;
                } else {
                    throw new \Error($error->getMessage());
                }
            }
        }
        session_start();
        $_SESSION['create-results'] = $result ? $this->userCreated : null;
        $_SESSION['create-errors'] = !$result ? $validatedData->errors : null;
        $_SESSION['create-data'] = $hasErrors ? (object)[
            'name' => $validatedData->name,
            'email' => $validatedData->email,
            'telegram' => $validatedData->telegram,
        ] : null;
        session_write_close();
        header("location: $this->redirectPath");
        exit;
    }
    public function update(): void
    {
        $validatedData = $this->getValidatedData();
        $result = false;
        $hasErrors = !!($validatedData->errors) || !$validatedData->userId;
        if(!$hasErrors) {
            $values =  $validatedData->values;
            $inserts = $validatedData->inserts;
            $sets = [];
            foreach($values as $key => $value) {
                $sets[] = "$inserts[$key] = $values[$key]";
            }
            $parameters = [
                ':id' => $validatedData->userId,
                ...$validatedData->parameters,
            ];
            if($validatedData->name) {
                $sets[] = "name = :name";
                $parameters[':name'] = $validatedData->name;
            }
            $sets = implode(', ', $sets);
            $query = "
                UPDATE users
                SET $sets
                WHERE id = :id;
            ";
            $result = !!($this->db->exec($query, $parameters));
        }
        if(!$result) {
            $validatedData->errors[] = $this->couldNotUpdate;
        }
        session_start();
        $_SESSION['update-results'] = $result ? $this->userUpdated : null;
        $_SESSION['update-errors'] = !$result ? $validatedData->errors : null;
        $_SESSION['update-data'] = $hasErrors ? (object)[
            'updated_user' => $validatedData->userId,
            'user_name' => $validatedData->name,
            'user_email' => $validatedData->email,
            'user_telegram' => $validatedData->telegram,
        ] : null;
        session_write_close();
        header("location: $this->redirectPath");
        exit;
    }
    private function getValidatedData(): object
    {
        $userData = $_POST;
        $errors = [];
        $user = trim(strip_tags($userData['updated_user'] ?? ''));
        $name = trim(strip_tags($userData['name'] ?? $userData['user_name'] ?? ''));
        $email = trim(strip_tags($userData['email'] ?? $userData['user_email'] ?? ''));
        $telegram = trim(strip_tags($userData['telegram'] ?? $userData['user_telegram'] ?? ''));
        if($user && !preg_match('/^[a-f\d]{8}(-[a-f\d]{4}){4}[a-f\d]{8}$/i', $user)) {
            $errors[] = "UserId should be a valid uuid";
        }
        if(!$name || mb_strlen($name) > $this->userNameMaxLength || mb_strlen($name) < $this->userNameMinLength) {
            $errors[] = "Name should be >= $this->userNameMinLength & <= $this->userNameMaxLength";
        }
        if($email && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Email should be a valid email";
        }
        if($email && (strlen($email) > $this->userEmailMaxLength || strlen($email) < $this->userEmailMinLength)) {
            $errors[] = "Email should be >= $this->userEmailMinLength & <= $this->userEmailMaxLength";
        }
        if($telegram && (mb_strlen($telegram) > $this->userTelegramMaxLength || mb_strlen($telegram) < $this->userTelegramMinLength)) {
            $errors[] = "Telegram should be >= $this->userNameMinLength & <= $this->userNameMaxLength";
        }
        if(!$telegram && !$email) {
            $errors[] = "Provide either email or telegram id";
        }
        $parameters = [];
        if($email) {
            $parameters[':email'] = $email;
        }
        if($telegram) {
            $parameters[':telegram'] = $telegram;
        }
        $values = [];
        $inserts = [];
        foreach($parameters as $key => $value) {
            if($value) {
                 $inserts[] = str_replace(':', '', $key);
                 $values[] = $key;
            }
        }

        return (object)[
            'userId' => $user,
            'name' => $name,
            'email' => $email,
            'telegram' => $telegram,
            'errors' => $errors,
            'values' => $values,
            'inserts' => $inserts,
            'parameters' => $parameters,
        ];
    }
}
