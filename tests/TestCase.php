<?php

namespace Tests;

use App\Models\User;
use Database\Factories\UserFactory;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;


    /**
     * Клиенты паспорта
     *
     * @var Collection
     */
    protected Collection $passportClients;

    /**
     * Созданный пользователь
     *
     * @var User
     */
    protected User $user;

    /**
     * Созданный второй пользователь
     *
     * @var User
     */
    protected User $userSecond;


    /**
     * Пароль по умолчанию
     *
     * @var string
     */
    protected string $defaultPassword = '12345678';


    public function __construct(?string $name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);

        $this->withHeader('Accept', 'application/json');
    }

    protected function setUp(bool $fresh = true): void
    {
        parent::setUp();

        # Очищаем базу
        if ($fresh) {
            Artisan::call('migrate:fresh');
            Artisan::call('passport:install --uuids --force --no-interaction');
        }

        # Получаем клиентов OAuth
        $this->passportClients = DB::table('oauth_clients')->get();

        # Создаем пользователя
        $this->user = UserFactory::new()->create([
            'name'     => 'Данил',
            'email'    => 'danilsidorenko00@gmail.com',
            'is_root'  => true,
            'password' => Hash::make($this->defaultPassword),
        ]);

        # Создаем второго пользователя, который привязан к основному
        $this->userSecond = UserFactory::new()->create([
            'name'     => 'Данил 2',
            'email'    => 'danilsidorenko01@gmail.com',
            'user_id'  => $this->user->id,
            'password' => Hash::make($this->defaultPassword),
        ]);
    }

    /**
     * Выполнять от имени пользователя
     *
     * @param User|null $user
     * @return void
     */
    protected function setAuth(?User $user = null): void
    {
        $this->actingAs(!$user ? $this->user : $user, 'api');
    }

    /**
     * Авторизовать по Bearer токену
     *
     * @param string $token
     */
    protected function setAuthBearerToken(string $token)
    {
        $this->flushHeaders();

        $this->withHeader('Accept', 'application/json');
        $this->withHeader('Authorization', 'Bearer ' . $token);
    }
}
