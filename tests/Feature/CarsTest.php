<?php

namespace Tests\Feature;

use App\Models\Car;
use Database\Factories\CarFactory;
use Tests\TestCase;
use Symfony\Component\HttpFoundation\Response as Status;

class CarsTest extends TestCase
{
    /**
     * Тестируем создание пользователя
     *
     * @return void
     */
    public function testCreateCar()
    {
        static::setUp();
        static::setAuth();

        /**
         * Тестируем создание нового транспортного средства
         * со стороны главного пользователя
         */
        $fabricator = 'BMW';
        $model = 'X6';

        $response = $this->post(route('cars.create', [
            'fabricator' => $fabricator,
            'model' => $model
        ]));

        $response->assertStatus(Status::HTTP_CREATED)->assertJsonFragment([
            'message' => trans('api.cars.create.success')
        ]);

        $this->assertDatabaseHas(Car::class, [
            'fabricator' => $fabricator,
            'model' => $model,
            'user_id' => $this->user->id
        ]);

        /**
         * Проверяем создание транспортного средства со стороны
         * привязанного пользователя
         */
        static::setAuth($this->userSecond);
        $fabricator = 'BMW';
        $model = 'X6';

        $response = $this->post(route('cars.create', [
            'fabricator' => $fabricator,
            'model' => $model
        ]));

        $response->assertStatus(Status::HTTP_CREATED)->assertJsonFragment([
            'message' => trans('api.cars.create.success')
        ]);

        $this->assertDatabaseHas(Car::class, [
            'fabricator' => $fabricator,
            'model' => $model,
            'user_id' => $this->userSecond->id
        ]);

        /**
         * Проверяем попытку создать еще одно транспортное
         * средство.
         */
        $fabricator = 'BMW';
        $model = 'X6';

        $response = $this->post(route('cars.create', [
            'fabricator' => $fabricator,
            'model' => $model
        ]));

        $response->assertStatus(Status::HTTP_SEE_OTHER)->assertJsonFragment([
            'message' => trans('api.cars.create.errors.already_have_car_user')
        ]);
    }

    public function testDeleteCar()
    {
        static::setUp();
        static::setAuth();

        /**
         * Тестируем удаление собственного транспорта
         * со стороны главного пользователя (мягкое удаление)
         */
        $car = CarFactory::new()->create([
            'user_id' => $this->user->id
        ]);

        $response = $this->delete(route('cars.delete', [
            'car_id' => $car->id
        ]));

        $response->assertStatus(Status::HTTP_OK)->assertJsonFragment([
            'message' => trans('api.cars.delete.success.soft')
        ]);

        $this->assertSoftDeleted(Car::class, $car->toArray());

        /**
         * Проверяем удаление собственного транспорта
         * со стороны главного пользователя (полное удаление)
         */
        $response = $this->delete(route('cars.delete', [
            'car_id' => $car->id,
            'force' => true
        ]));

        $response->assertStatus(Status::HTTP_OK)->assertJsonFragment([
            'message' => trans('api.cars.delete.success.force')
        ]);

        $this->assertDatabaseMissing(Car::class, $car->toArray());

        /**
         * Проверяем, что пользователь ROOT-пользователь может
         * удалять транспорт у своих привязанных пользователей
         */

        # Создадим первую машины для привязанного пользователя
        $car = CarFactory::new()->create([
            'user_id' => $this->userSecond->id
        ]);

        $response = $this->delete(route('cars.delete', [
            'car_id' => $car->id,
        ]));

        $response->assertStatus(Status::HTTP_OK)->assertJsonFragment([
            'message' => trans('api.cars.delete.success.soft')
        ]);

        $this->assertSoftDeleted(Car::class, $car->toArray());

        /**
         * Проверяем собственное удаление со стороны
         * привязанного пользователя (мягкое)
         */
        static::setUp();
        static::setAuth($this->userSecond);

        # Создадим первую машины для привязанного пользователя
        $car = CarFactory::new()->create([
            'user_id' => $this->userSecond->id
        ]);

        # Создаем вторую машину, которая привязана к другому пользователю
        $carRoot = CarFactory::new()->create([
            'user_id' => $this->user->id
        ]);

        $response = $this->delete(route('cars.delete', [
            'car_id' => $car->id,
        ]));

        $response->assertStatus(Status::HTTP_OK)->assertJsonFragment([
            'message' => trans('api.cars.delete.success.soft')
        ]);

        $this->assertSoftDeleted(Car::class, $car->toArray());

        /**
         * Проверяем собственное удаление со стороны
         * привязанного пользователя (полное)
         */
        $response = $this->delete(route('cars.delete', [
            'car_id' => $car->id,
            'force' => true
        ]));

        $response->assertStatus(Status::HTTP_OK)->assertJsonFragment([
            'message' => trans('api.cars.delete.success.force')
        ]);

        $this->assertDatabaseMissing(Car::class, $car->toArray());

        /**
         * Проверяем удаление чужого транспортного средства со стороны
         * привязанного пользователя
         */
        $response = $this->delete(route('cars.delete', [
            'car_id' => $carRoot->id,
        ]));

        $response->assertStatus(Status::HTTP_FORBIDDEN)->assertJsonFragment([
            'message' => trans('api.cars.delete.errors.can_delete_only_my')
        ]);
    }

    public function testUpdateCar()
    {
        static::setUp();
        static::setAuth();

        /**
         * Проверяем, что мы можем редактировать свои
         * транспортные средства
         */
        CarFactory::new()->create([
            'user_id' => $this->user->id
        ]);

        $model = 'i7';

        $response = $this->put(route('cars.update', [
            'user_id' => $this->user->id,
            'model' => $model
        ]));

        $response->assertStatus(Status::HTTP_OK)->assertJsonFragment([
            'message' => trans('api.cars.update.success')
        ]);

        $this->assertDatabaseHas(Car::class, [
            'model' => $model
        ]);

        /**
         * Проверяем, что можем изменить информацию транспортного средства
         * у своего привязанного пользователя
         */
        CarFactory::new()->create([
            'user_id' => $this->userSecond->id
        ]);

        $model = 'i7';

        $response = $this->put(route('cars.update', [
            'user_id' => $this->userSecond->id,
            'model' => $model
        ]));

        $response->assertStatus(Status::HTTP_OK)->assertJsonFragment([
            'message' => trans('api.cars.update.success')
        ]);

        $this->assertDatabaseHas(Car::class, [
            'model' => $model
        ]);

        /**
         * Проверяем, что привязанный пользователь может изменить
         * информацию о своем собственном транспорте
         */
        static::setUp();
        static::setAuth($this->userSecond);

        CarFactory::new()->create([
            'user_id' => $this->userSecond->id
        ]);

        $model = 'i7';

        $response = $this->put(route('cars.update', [
            'user_id' => $this->userSecond->id,
            'model' => $model
        ]));

        $response->assertStatus(Status::HTTP_OK)->assertJsonFragment([
            'message' => trans('api.cars.update.success')
        ]);

        $this->assertDatabaseHas(Car::class, [
            'model' => $model
        ]);

        /**
         * Проверяем, что привязанный пользователь не может изменить другим
         * информацию о транспортном средстве
         */

        CarFactory::new()->create([
            'user_id' => $this->user->id
        ]);

        $model = 'i7';

        $response = $this->put(route('cars.update', [
            'user_id' => $this->user->id,
            'model' => $model
        ]));

        $response->assertStatus(Status::HTTP_FORBIDDEN)->assertJsonFragment([
            'message' => trans('api.cars.update.errors.can_change_only_my')
        ]);

        $this->assertDatabaseMissing(Car::class, [
            'model' => $model,
            'user_id' => $this->user->id
        ]);
    }

    public function testGetCar()
    {
        static::setUp();
        static::setAuth();

        /**
         * Проверяем, что мы можем получить транспортное средство
         */
        $car = CarFactory::new()->create([
            'user_id' => $this->user->id
        ]);

        $carSecondUser = CarFactory::new()->create([
            'user_id' => $this->userSecond->id
        ]);

        $response = $this->get(route('cars.get', [
            'car_id' => $car->id,
        ]));

        $response->assertStatus(Status::HTTP_OK)->assertJsonFragment([
            'message' => trans('api.cars.get.success')
        ]);

        /**
         * Проверяем, что мы можем получить чужое транспортное средство
         * будучи ROOT-пользователем
         */

        $response = $this->get(route('cars.get', [
            'car_id' => $carSecondUser->id,
        ]));

        $response->assertStatus(Status::HTTP_OK)->assertJsonFragment([
            'message' => trans('api.cars.get.success')
        ]);

        /**
         * Проверяем, что привязанный пользователь может получить данные
         * о своем транспортном средстве
         */
        static::setAuth($this->userSecond);

        $response = $this->get(route('cars.get', [
            'car_id' => $carSecondUser->id,
        ]));

        $response->assertStatus(Status::HTTP_OK)->assertJsonFragment([
            'message' => trans('api.cars.get.success')
        ]);

        /**
         * Проверяем, что привязанный пользователь НЕ может получить данные
         * о ЧУЖОМ транспортном средстве
         */

        $response = $this->get(route('cars.get', [
            'car_id' => $car->id,
        ]));

        $response->assertStatus(Status::HTTP_FORBIDDEN)->assertJsonFragment([
            'message' => trans('api.cars.get.errors.not_my_car')
        ]);

        /**
         * Проверяем, что мы можем получить информацию о своем
         * транспортном средстве без передачи каких-либо аргументов
         */
        $response = $this->get(route('cars.get'));


        $response->assertStatus(Status::HTTP_OK)->assertJsonFragment([
            'message' => trans('api.cars.get.success')
        ]);
    }
}
