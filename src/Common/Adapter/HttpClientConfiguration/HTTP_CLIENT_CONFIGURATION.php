<?php

declare(strict_types=1);

namespace Common\Adapter\HttpClientConfiguration;

use Common\Domain\Config\Config;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Mime\Part\DataPart;
use Symfony\Component\Mime\Part\Multipart\FormDataPart;

final class HTTP_CLIENT_CONFIGURATION
{
    public const string API_DOMAIN = Config::API_DOMAIN;
    public const string XDEBUG_VAR = 'XDEBUG_SESSION=VSCODE';
    public const string COOKIE_SESSION_NAME = Config::COOKIE_TOKEN_SESSION_NAME;

    public static function json(?array $data = null, ?string $tokenSession = null): array
    {
        $json = Config::getConfigurationHttp();

        null === $data ?: $json['json'] = $data;
        null === $tokenSession ?: $json['auth_bearer'] = $tokenSession;

        return $json;
    }

    public static function form(array $data, array $files = [], ?string $tokenSession = null): array
    {
        $form = Config::getConfigurationHttp();
        null === $tokenSession ?: $form['auth_bearer'] = $tokenSession;

        $files = array_filter($files);
        $files = array_map(self::loadFiles(...), $files);
        $data = array_map(fn (mixed $value) => (string) $value, $data);
        $data = array_merge($data, $files);
        $formData = new FormDataPart($data);

        $form['body'] = $formData->toIterable();
        $form['headers'] = $formData->getPreparedHeaders()->toArray();

        return $form;
    }

    private static function loadFiles(?UploadedFile $file): ?DataPart
    {
        if (null === $file) {
            return null;
        }

        return DataPart::fromPath($file->getPathname());
    }
}
