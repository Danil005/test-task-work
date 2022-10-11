<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;
use Symfony\Component\HttpFoundation\Response as Status;

class UsersTest extends TestCase
{
    /**
     * Тестируем создание пользователя
     *
     * @return void
     */
    public function testCreateUser()
    {
        static::setUp();

        /**
         * Тестируем создание пользователя
         */
        $name = 'Test User Name';
        $email = 'test@email.com';
        $password = '12345678';

        # Отправляем запрос на создание пользователя
        $response = $this->post(route('users.create', [
            'name'     => $name,
            'email'    => $email,
            'password' => $password,
        ]));

        $response->assertStatus(Status::HTTP_CREATED)->assertJsonFragment([
            'message' => trans('api.users.create.success')
        ]);

        $this->assertDatabaseHas(User::class, [
            'name' => $name,
            'email' => $email
        ]);

        /**
         * Тестируем ошибку при вводе электронной почты
         */
        $email = 'test';

        # Отправляем запрос на создание пользователя
        $response = $this->post(route('users.create', [
            'name'     => $name,
            'email'    => $email,
            'password' => $password,
        ]));

        $response->assertStatus(Status::HTTP_UNPROCESSABLE_ENTITY)->assertJsonFragment([
            'message' => trans('validation.email', ['attribute' => 'email'])
        ]);

        /**
         * Тестируем ошибку при указании почты, которая уже существует
         */
        $email = 'test@email.com';

        # Отправляем запрос на создание пользователя
        $response = $this->post(route('users.create', [
            'name'     => $name,
            'email'    => $email,
            'password' => $password,
        ]));

        $response->assertStatus(Status::HTTP_UNPROCESSABLE_ENTITY)->assertJsonFragment([
            'message' => trans('validation.unique', ['attribute' => 'email'])
        ]);
    }

    public function testLoginUser()
    {
        static::setUp();

        # Получаем клиент Passport
        $client = $this->passportClients->firstWhere('name', 'Laravel Password Grant Client');

        /**
         * Проверяем, что введены верные данные
         */
        $token = $this->post('oauth/token', [
            'grant_type'    => 'password',
            'client_id'     => $client->id,
            'client_secret' => $client->secret,
            'username'      => $this->user->email,
            'password'      => $this->defaultPassword,
        ]);
        $token->assertStatus(Status::HTTP_OK);

        /**
         * Проверяем, что будет ошибка, если не передан токен
         */
        $response = $this->get(route('users.me'));

        $response->assertStatus(Status::HTTP_UNAUTHORIZED);

        /**
         * Проверяем, что выдаст результат
         */
        static::setAuthBearerToken($token->json('access_token'));
        $response = $this->get(route('users.me'));

        $response->assertStatus(Status::HTTP_OK);
    }
}
