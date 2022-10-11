<?php

namespace App\Http\Controllers\Api\v1\Cars;

use App\Http\Controllers\Controller;
use App\Http\Requests\Car\GetCar;
use App\Services\CarService;
use App\Utils\Response;

class GetCarController extends Controller
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

    public function __invoke(GetCar $createCar)
    {
        # Получаем транспортное средство
        $car = $this->carService->getCar($createCar->car_id);

        # Выдаем результат
        return $this->response()
            ->success()
            ->setMessage(trans('api.cars.get.success'))
            ->setPayload($car)
            ->send();
    }
}
