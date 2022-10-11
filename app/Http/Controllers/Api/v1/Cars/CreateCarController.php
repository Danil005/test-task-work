<?php

namespace App\Http\Controllers\Api\v1\Cars;

use App\Http\Controllers\Controller;
use App\Http\Requests\Car\CreateCar;
use App\Services\CarService;
use App\Utils\Response;
use Symfony\Component\HttpFoundation\Response as Status;


class CreateCarController extends Controller
{
    # Подключаем ответы
    use Response;

    /**
     * Controller
     *
     * @param CarService $carService
     */
    public function __construct(
        private CarService $carService,
    )
    {}

    public function __invoke(CreateCar $createCar)
    {
        # Создаем транспортное средство
        $car = $this->carService->createCar($createCar->user_id, $createCar->fabricator, $createCar->model);

        # Выдаем результат
        return $this->response()
            ->success()
            ->setMessage(trans('api.cars.create.success'))
            ->setPayload($car)
            ->setHttpCode(Status::HTTP_CREATED)
            ->send();
    }
}
