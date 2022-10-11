<?php

namespace App\Http\Controllers\Api\v1\Users;

use App\Http\Controllers\Controller;
use App\Http\Requests\Users\CreateUser;
use App\Services\UserService;
use App\Utils\Response;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response as Status;

class CreateUserController extends Controller
{
    # Подключаем ответы
    use Response;

    /**
     * Construct
     *
     * @param UserService $userService # Сервис для управления пользователям
     */
    public function __construct(
        private UserService $userService
    ) {}

    /**
     * Исполняем контроллер
     */
    public function __invoke(CreateUser $createUser)
    {
        if(Auth::check()) {
            # Создаем пользователя
            $user = $this->userService->createRootUser(
                $createUser->name,
                $createUser->email,
                $createUser->password
            );

            # Выдаем результат
            $this->response()
                ->success()
                ->setMessage(trans('api.users.create.success'))
                ->setPayload($user)
                ->setHttpCode(Status::HTTP_CREATED)
                ->send();
        } else {
            $owner = Auth::user();

            # Создаем обычного пользователя, который будет привязан к администратору
            $user = $this->userService->createUser(
                $createUser->name,
                $createUser->email,
                $createUser->password,
                $owner
            );

            # Выдаем результат
            $this->response()
                ->success()
                ->setMessage(trans('api.users.create.success'))
                ->setPayload($user)
                ->setHttpCode(Status::HTTP_CREATED)
                ->send();
        }
    }
}
