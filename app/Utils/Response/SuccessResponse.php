<?php

namespace App\Utils\Response;

/**
 * Класс, который делает ответ успешный
 *
 * @class SuccessResponse
 */
class SuccessResponse extends CoreResponse
{
    protected bool $isSuccess = true;
}
