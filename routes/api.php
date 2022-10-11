<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/


# Устанавливаем стандартный Namespace API, а также префикс: /api/v1/{category}/{category}.{method}
Route::namespace('App\Http\Controllers\Api\v1')->prefix('v1')->group(function () {

    # Пользователи
    Route::namespace('Users')->prefix('users')->group(function () {
        # Создать пользователя
        Route::post('users.create', 'CreateUserController')->name('users.create');

        # Необходима авторизация
        Route::middleware('auth:api')->group(function () {
            # Получаем информацию о себе
            Route::get('users.me', 'MeController')->name('users.me');
        });
    });

    Route::middleware('auth:api')->group(function () {
        # Транспортные средства
        Route::namespace('Cars')->prefix('cars')->group(function () {
            # Создать транспортное средство
            Route::post('cars.create', 'CreateCarController')->name('cars.create');
            # Удалить транспортное средство
            Route::delete('cars.delete', 'DeleteCarController')->name('cars.delete');
            # Обновить транспортное средство
            Route::put('cars.update', 'UpdateCarController')->name('cars.update');
            # Получаем транспортное средство
            Route::get('cars.get', 'GetCarController')->name('cars.get');
        });
    });

});
