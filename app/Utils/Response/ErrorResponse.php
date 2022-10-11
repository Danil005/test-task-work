<?php

namespace App\Utils\Response;

/**
 * Класс, который делает ответ с ошибкой
 *
 * @class ErrorResponse
 */
class ErrorResponse extends CoreResponse
{
    protected bool $isSuccess = false;
}
