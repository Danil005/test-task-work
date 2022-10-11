<?php

namespace App\Utils\Response;

use Illuminate\Http\JsonResponse;

use Symfony\Component\HttpFoundation\Response as Status;
use Illuminate\Support\Facades\Response as ResponseFacade;

/**
 * Класс обработки ответов
 *
 * @class CoreResponse
 */
class CoreResponse
{
    /**
     * Значение успешности запроса
     *
     * @var bool
     */
    protected bool $isSuccess = false;

    /**
     * Сообщение ответа запроса
     *
     * @var string
     */
    protected string $message = '';

    /**
     * Код ответа
     *
     * @var int
     */
    protected int $httpCode = 200;

    /**
     * Дополнительная нагрузка
     *
     * @var mixed
     */
    protected mixed $payload = [];

    /**
     * Установить сообщение
     *
     * @param string $message
     * @return $this
     */
    public function setMessage(string $message): static
    {
        $this->message = $message;

        return $this;
    }

    /**
     * Установить код ответа
     *
     * @param int $code
     * @return $this
     */
    public function setHttpCode(int $code = Status::HTTP_OK): static
    {
        # Если запрос успешный
        if($this->isSuccess) {
            # Устанавливаем ответ 200
            $this->httpCode = $code;
        } else {
            # Устанавливаем ответ 400, если запрос не успешный
            $this->httpCode = $code ?? Status::HTTP_BAD_REQUEST;
        }

        return $this;
    }

    /**
     * Установить дополнительную нагрузку
     *
     * @param mixed $payload
     * @return $this
     */
    public function setPayload(mixed $payload): static
    {
        $this->payload = $payload;

        return $this;
    }

    /**
     * Выдать результат
     *
     * @param bool $throw    # Оборвать дальнейшую обработку кода
     * @return JsonResponse
     */
    public function send(bool $throw = true): JsonResponse
    {
        # Создаем ответ в формате JSON
        $response = ResponseFacade::json([
            'success' => $this->isSuccess,
            'message' => $this->message,
            'payload' => $this->payload,
        ], $this->httpCode);

        # Если указано выбрасывание, то останавливаем дальнейшую обработку
        # и выдаем ответ
        if($throw) {
            $response->throwResponse();
        }

        # Выдаем ответ
        return $response;
    }
}
