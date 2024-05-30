<?php

declare(strict_types=1);

namespace Common\Adapter\Events\Exceptions;

use Symfony\Component\HttpFoundation\File\Exception\AccessDeniedException as AccessDeniedExceptionSymfony;

class AccessDeniedException extends AccessDeniedExceptionSymfony
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
