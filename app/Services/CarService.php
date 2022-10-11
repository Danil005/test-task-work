<?php

namespace App\Services;

use App\Http\Requests\Car\UpdateCar;
use App\Models\Car;
use App\Utils\Response;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response as Status;

class CarService
{
    use Response;

    /**
     * Создаем транспортное средство
     *
     * @param int|null $userId
     * @param string $fabricator
     * @param string $model
     *
     * @return Car|Model
     */
    public function createCar(?int $userId, string $fabricator, string $model): Model|Car
    {
        # Если пользователя не передали, то присваиваем человеку, который выполнял запрос
        if (!$userId) {
            $userId = Auth::user()->id;
        }

        # Проверяем, что у пользователя еще нет транспортного средства
        # Иначе выдаем ошибку (У вас) ИЛИ (У пользователя)
        if (Car::where('user_id', $userId)->exists()) {
            $this->response()->error()->setMessage(
                $userId ? trans('api.cars.create.errors.already_have_car_user') :
                    trans('api.cars.create.errors.already_have_car')
            )->setHttpCode(Status::HTTP_SEE_OTHER)->send();
        }

        # Добавляем машину
        return Car::create([
            'user_id'    => $userId,
            'fabricator' => $fabricator,
            'model'      => $model,
        ]);
    }

    /**
     * Изменить информацию об автомобиле
     *
     * @param int $userId
     * @param UpdateCar $car
     *
     * @return array
     */
    public function updateCar(int $userId, UpdateCar $car): array
    {
        $user = Auth::user();

        # Проверяем, может ли пользователь изменить информацию чужой машины
        if (!$user->is_root) {
            if ($userId != $user?->id) {
                $this->response()->error()
                    ->setMessage(trans('api.cars.update.errors.can_change_only_my'))
                    ->setHttpCode(Status::HTTP_FORBIDDEN)
                    ->send();
            }
        }

        # Если пользователя не передали, то присваиваем человеку, который выполнял запрос
        if (!$userId) {
            $userId = $user->id;
        }

        # Создаем массив для обновления
        $update = [];

        # Если указан производитель
        if ($car->has('fabricator')) {
            # Добавляем в изменения заранее удаляя в конце и в начале пробелы
            $update['fabricator'] = trim($car->fabricator);
        }

        # Если указана модель
        if ($car->has('model')) {
            # Добавляем в изменения заранее удаляя в конце и в начале пробелы
            $update['model'] = trim($car->model);
        }

        # Редактируем данные
        Car::where('user_id', $userId)->update($update);

        # Возвращаем данные, которые были изменены
        return $update;
    }

    private function prepareDeleteCar(?int $carId): Builder|Car
    {
        # Получаем пользователя
        $user = Auth::user();

        # Проверяем, может ли пользователь удалять чужие машины
        if (!$user->is_root) {
            # Получаем мое транспортное средство
            $myCar = $user->car()->withTrashed()->first();

            if ($carId != $myCar?->id) {
                $this->response()->error()
                    ->setHttpCode(Status::HTTP_FORBIDDEN)
                    ->setMessage(trans('api.cars.delete.errors.can_delete_only_my'))
                    ->send();
            }
        }

        # Получить модель автомобиля
        $car = Car::withTrashed()->where('id', $carId);

        # Если это не ROOT пользователь, то получаем только наш автомобиль
        if (!$user->is_root) {
            $car = $car->where('user_id', $user->id);
        }

        return $car;
    }

    /**
     * Удалить автомобиль (удаляется не полностью)
     *
     * @param int|null $carId
     * @return bool
     */
    public function deleteCar(?int $carId): bool
    {
        # Получаем модель со всеми проверками и удаляем не полностью
        return $this->prepareDeleteCar($carId)->delete();
    }

    /**
     * Удалить автомобиль (полностью)
     *
     * @param int|null $carId
     * @return bool
     */
    public function deleteCarForce(?int $carId): bool
    {
        # Получаем модель со всеми проверками и удаляем полностью
        return $this->prepareDeleteCar($carId)->forceDelete();
    }

    /**
     * Получить транспортное средство
     *
     * @param int|null $carId
     * @return array
     */
    public function getCar(?int $carId): array
    {
        # Получаем пользователя
        $user = Auth::user();
        # Получаем мое транспортное средство
        $myCar = $user->car()->first();

        # Проверяем, может ли пользователь удалять чужие машины
        if (!$user->is_root) {
            if ($carId && $carId != $myCar?->id) {
                $this->response()->error()
                    ->setHttpCode(Status::HTTP_FORBIDDEN)
                    ->setMessage(trans('api.cars.get.errors.not_my_car'))
                    ->send();
            }
        }

        return $myCar->toArray();
    }
}
