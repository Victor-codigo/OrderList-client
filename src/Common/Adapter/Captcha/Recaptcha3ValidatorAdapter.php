<?php

declare(strict_types=1);

namespace Common\Adapter\Captcha;

use Common\Domain\Ports\Captcha\CaptchaInterface;
use Karser\Recaptcha3Bundle\Validator\Constraints\Recaptcha3Validator;

class Recaptcha3ValidatorAdapter implements CaptchaInterface
{
    public function __construct(
        private Recaptcha3Validator $recaptcha3Validator
    ) {
    }

    public function valid(): bool
    {
        $lastResponse = $this->recaptcha3Validator->getLastResponse();

        if (null === $lastResponse) {
            return false;
        }

        return $lastResponse->isSuccess();
    }

    public function getErrors(): array
    {
        $lastResponse = $this->recaptcha3Validator->getLastResponse();

        if (null === $lastResponse) {
            return ['captcha' => 'Captcha not found'];
        }

        return ['captcha' => 'Error captcha'];
    }
}
