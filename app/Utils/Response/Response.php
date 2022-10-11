<?php

namespace App\Utils\Response;

use JetBrains\PhpStorm\Pure;

/**
 * Класс, который обрабатывает ответы API,
 * используется в Trait > Response.
 * В trait создает объект данного класса.
 *
 * @class Response
 */
class Response
{
    /**
     * Создать успешный запрос
     *
     * @return SuccessResponse
     */
    #[Pure] public function success(): SuccessResponse
    {
        return new SuccessResponse();
    }

    /**
     * Создать запрос с ошибкой
     *
     * @return ErrorResponse
     */
    #[Pure] public function error(): ErrorResponse
    {
        return new ErrorResponse();
    }
}
