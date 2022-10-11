<?php

namespace App\Http\Controllers\Api\v1\Cars;

use App\Http\Controllers\Controller;
use App\Http\Requests\Car\UpdateCar;
use App\Services\CarService;
use App\Utils\Response;

class UpdateCarController extends Controller
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

    public function __invoke(UpdateCar $createCar)
    {
        # Обновляем транспортное средство
        $car = $this->carService->updateCar($createCar->user_id, $createCar);

        # Выдаем результат
        return $this->response()
            ->success()
            ->setMessage(trans('api.cars.update.success'))
            ->setPayload($car)
            ->send();
    }
}
