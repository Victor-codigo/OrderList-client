<?php

declare(strict_types=1);

namespace Common\Domain\Ports\Captcha;

interface CaptchaInterface
{
    public function valid(): bool;

    public function getErrors(): array;
}
