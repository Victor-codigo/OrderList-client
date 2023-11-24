<?php

declare(strict_types=1);

namespace Common\Domain\CodedUrlParameter;

use Symfony\Component\HttpFoundation\ParameterBag;

trait CodedUrlParameter
{
    private function decodeUrlParameter(ParameterBag $request, string $parameterName): string|null
    {
        if (!$request->has($parameterName)) {
            return null;
        }

        $parameterValue = $request->get($parameterName);

        return str_replace([' ', '-'], ['', ' '], $parameterValue);
    }

    private function encodeUrlParameter(string $parameterValue): string
    {
        return str_replace(' ', '-', $parameterValue);
    }
}
