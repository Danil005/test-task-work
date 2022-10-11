<?php

namespace App\Http\Controllers\Api\v1\Cars;

use App\Http\Controllers\Controller;
use App\Http\Requests\Car\DeleteCar;
use App\Services\CarService;
use App\Utils\Response;

class DeleteCarController extends Controller
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
    ) {}

    public function __invoke(DeleteCar $deleteCar)
    {
        if ($deleteCar?->force) {
            # Удаляем транспортное средство полностью
            $car = $this->carService->deleteCarForce($deleteCar->car_id);
        } else {
            # Удаляем транспортное средство не полностью
            $car = $this->carService->deleteCar($deleteCar->car_id);
        }


        # Выдаем результат
        return $this->response()
            ->success()
            ->setMessage($deleteCar?->force ?
                trans('api.cars.delete.success.force') :
                trans('api.cars.delete.success.soft')
            )
            ->send();
    }
}
