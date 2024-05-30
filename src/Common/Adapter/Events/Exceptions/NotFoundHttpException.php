<?php

declare(strict_types=1);

namespace Common\Adapter\Events\Exceptions;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException as NotFoundHttpExceptionSymfony;

class NotFoundHttpException extends NotFoundHttpExceptionSymfony
{
    public static function fromMessage(string $message): static
    {
        return new static($message);
    }

    public static function fromArray(array $messages): static
    {
        $messagesToString = implode('|', $messages);

        return new static($messagesToString);
    }
}
