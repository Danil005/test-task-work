<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserService
{
    /**
     * Подготовить запрос на создание пользователя
     *
     * @param string $name
     * @param string $email
     * @param string $password
     * @param bool $isRoot
     * @param User|null $owner
     *
     * @return User
     */
    private function prepareUser(
        string $name,
        string $email,
        string $password,
        bool $isRoot = false,
        ?User $owner = null,
    ): User
    {
        # Очищаем пробелы в конце и в начала
        $name = trim($name);
        $email = trim($email);

        # Создаем хеш-пароль
        $password = Hash::make($password);

        # Добавляем пользователя в базу данных
        return User::create([
            'name' => $name,
            'email' => $email,
            'password' => $password,
            'is_root' => $isRoot,
            'user_id' => $owner?->id
        ]);
    }

    /**
     * Создаем главного пользователя
     *
     * @param string $name
     * @param string $email
     * @param string $password
     *
     * @return User
     */
    public function createRootUser(string $name, string $email, string $password): User
    {
        # Подготовить и занести данные о пользователи ROOT
        return $this->prepareUser($name, $email, $password, true);
    }

    /**
     * Создать простого пользователя
     *
     * @param string $name
     * @param string $email
     * @param string $password
     * @param ?User $owner | Сотрудник к которому привязан пользователь
     * @return User
     */
    public function createUser(string $name, string $email, string $password, ?User $owner): User
    {
        # Подготовить и занести данные о пользователи (Обычный)
        return $this->prepareUser($name, $email, $password, owner: $owner);
    }

    /**
     * Возвращает пользователя, который авторизован
     *
     * @return User|Authenticatable|null
     */
    public function getMe(): User|Authenticatable|null
    {
        return Auth::user();
    }
}
