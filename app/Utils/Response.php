<?php

namespace App\Utils;

use JetBrains\PhpStorm\Pure;

trait Response
{
    /**
     * Создать объект ответа
     *
     * @return Response\Response
     */
    #[Pure] public function response(): Response\Response
    {
        return new Response\Response();
    }
}
