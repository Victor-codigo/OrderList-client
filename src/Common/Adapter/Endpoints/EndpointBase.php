<?php

declare(strict_types=1);

namespace Common\Adapter\Endpoints;

use Common\Adapter\Events\Exceptions\RequestException;
use Common\Adapter\Events\Exceptions\RequestUnauthorizedException;
use Common\Domain\HttpClient\Exception\Error400Exception;
use Common\Domain\HttpClient\Exception\Error500Exception;
use Common\Domain\HttpClient\Exception\NetworkException;
use Common\Domain\Ports\HttpClient\HttpClientResponseInterface;
use Symfony\Component\HttpFoundation\Response;

abstract class EndpointBase
{
    /**
     * @param array<string, string> $parametersAndValues
     */
    protected function createQueryParameters(array $parametersAndValues): string
    {
        $queryParametersValid = array_filter($parametersAndValues, fn (string|null $value) => null !== $value);
        $queryParametersCreated = array_map(
            fn (string $value, string $key) => "{$key}={$value}",
            array_values($queryParametersValid), array_keys($queryParametersValid)
        );

        return implode('&', $queryParametersCreated);
    }

    /**
     * @param callable $onResponseErrorReturnCallback     function(array $responseDataOk): array
     * @param callable $onResponseOkReturnCallback        function(array $responseDataError): array
     * @param callable $onResponseNoContentReturnCallBack function(array $responseDataNoContent): array
     *
     * @return array<{
     *      data: array<string, mixed>,
     *      errors: array<string, mixed>
     * }>
     *
     * @throws RequestUnauthorizedException
     * @throws RequestException
     */
    protected function apiResponseManage(HttpClientResponseInterface $response, callable $onResponseErrorReturnCallback = null, callable $onResponseOkReturnCallback = null, callable $onResponseNoContentReturnCallBack = null): array
    {
        try {
            if (Response::HTTP_NO_CONTENT === $response->getStatusCode()) {
                return $this->noContentResponseHandler($response, $onResponseNoContentReturnCallBack);
            }

            $responseDataOk = $response->toArray();

            if (null === $onResponseOkReturnCallback) {
                return $responseDataOk;
            }

            return $onResponseOkReturnCallback($responseDataOk);
        } catch (Error400Exception|Error500Exception|NetworkException) {
            $responseDataError = $response->toArray(false);

            if (array_key_exists('data', $responseDataError)
            && array_key_exists('errors', $responseDataError)) {
                if (null === $onResponseErrorReturnCallback) {
                    return $responseDataError;
                }

                return $onResponseErrorReturnCallback($responseDataError);
            }

            if (array_key_exists('code', $responseDataError)
            && Response::HTTP_UNAUTHORIZED === $responseDataError['code']) {
                throw RequestUnauthorizedException::fromMessage('Your session has expired. Please login again.');
            }

            throw RequestException::fromMessage('An error has occurred in the request. Unknown response');
        }
    }

    /**
     * @param callable $onResponseNoContentReturnCallBack function(array $responseDataNoContent): array
     */
    private function noContentResponseHandler(HttpClientResponseInterface $response, callable $onResponseNoContentReturnCallBack): array
    {
        if (null === $onResponseNoContentReturnCallBack) {
            return [];
        }

        return $onResponseNoContentReturnCallBack([]);
    }
}
