<?php

declare(strict_types=1);

namespace Common\Domain\Response;

class ResponseDto
{
    public RESPONSE_STATUS $status;
    public string $message;
    public array $data;
    public array $headers;
    public array $errors;

    public function getStatus(): RESPONSE_STATUS
    {
        return $this->status;
    }

    public function setStatus(RESPONSE_STATUS $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function setMessage(string $message): static
    {
        $this->message = $message;

        return $this;
    }

    public function getData(): array
    {
        return $this->data;
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }

    public function setData(array $data): static
    {
        $this->data = $data;

        return $this;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    public function setErrors(array $errors): static
    {
        $this->errors = $errors;

        return $this;
    }

    public function __construct(array $data = [], array $errors = [], string $message = '', RESPONSE_STATUS $status = RESPONSE_STATUS::OK, array $headers = [])
    {
        $this->status = $status;
        $this->message = $message;
        $this->data = $data;
        $this->headers = $headers;
        $this->errors = $errors;
    }

    /**
     * @throws \InvalidArgumentException
     */
    public static function fromArray(array $responseData): self
    {
        if (!array_key_exists('status', $responseData)
        || !array_key_exists('message', $responseData)
        || !array_key_exists('data', $responseData)
        || !array_key_exists('headers', $responseData)
        || !array_key_exists('errors', $responseData)) {
            throw new \InvalidArgumentException('Not all group parameters are provided');
        }

        return new self(
            $responseData['data'],
            $responseData['errors'],
            $responseData['message'],
            RESPONSE_STATUS::tryFrom($responseData['status']) ?? RESPONSE_STATUS::ERROR,
            $responseData['headers'],
        );
    }

    public function toArray(): array
    {
        return [
            'status' => $this->status->value,
            'message' => $this->message,
            'data' => $this->data,
            'headers' => $this->headers,
            'errors' => $this->errors,
        ];
    }

    public function to(callable $callbackTransformTo, bool $multidimensional = true): mixed
    {
        if (!$multidimensional) {
            return $callbackTransformTo($this->data);
        }

        return array_map(
            fn (mixed $data) => $callbackTransformTo($data),
            $this->data
        );
    }

    public function validate(): bool
    {
        if (!empty($this->errors)) {
            return false;
        }

        return true;
    }
}
