<?php

namespace App\Http\Controllers\Api\v1\Users;

use App\Http\Controllers\Controller;
use App\Services\UserService;
use App\Utils\Response;

class MeController extends Controller
{
    # Подключаем ответы
    use Response;

    /**
     * Constructor
     *
     * @param UserService $userService | Сервис пользователей
     */
    public function __construct(
        private UserService $userService,
    ) {}

    public function __invoke()
    {
        # Получаем информацию о пользователе
        $user = $this->userService->getMe();

        # Выдаем результат
        return $this->response()
            ->success()
            ->setMessage(trans('api.users.me.success'))
            ->setPayload($user)
            ->send();
    }
}
