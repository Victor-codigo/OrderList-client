<?php

declare(strict_types=1);

namespace Common\Adapter\Events\Exceptions;

class RequestException extends \InvalidArgumentException
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
