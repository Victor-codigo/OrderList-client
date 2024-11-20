<?php

declare(strict_types=1);

namespace Common\Adapter\Events\Exceptions;

class DomainInternalException extends \DomainException
{
    private function __construct(string $message)
    {
        parent::__construct($message, 0, null);
    }

    public static function fromMessage(string $message): static
    {
        return new static($message);
    }
}
