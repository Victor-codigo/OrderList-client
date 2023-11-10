<?php

declare(strict_types=1);

namespace Common\Adapter\Endpoints;

abstract class EndpointBase
{
    protected function createQueryParameters(array $parameters, array $values): string
    {
        $queryParameters = array_combine($parameters, $values);
        $queryParametersValid = array_filter($queryParameters, fn (string|null $value) => null !== $value);
        $queryParametersCreated = array_map(
            fn (string $value, string $key) => "{$key}={$value}",
            array_values($queryParametersValid), array_keys($queryParametersValid)
        );

        return implode('&', $queryParametersCreated);
    }
}
