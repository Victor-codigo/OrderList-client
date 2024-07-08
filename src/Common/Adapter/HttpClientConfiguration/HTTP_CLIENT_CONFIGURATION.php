<?php

declare(strict_types=1);

namespace Common\Adapter\HttpClientConfiguration;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Mime\Part\DataPart;
use Symfony\Component\Mime\Part\Multipart\FormDataPart;

final class HTTP_CLIENT_CONFIGURATION
{
    public const API_DOMAIN = 'http://orderlist.api';
    public const CLIENT_DOMAIN = 'http://orderlist.client';
    public const XDEBUG_VAR = 'XDEBUG_SESSION=VSCODE';
    public const TOKEN_SESSION_VAR_NAME = 'TOKENSESSION';

    public static function json(array $data = null, string $tokenSession = null): array
    {
        $json = self::getConfiguration();

        null === $data ?: $json['json'] = $data;
        null === $tokenSession ?: $json['auth_bearer'] = $tokenSession;

        return $json;
    }

    public static function form(array $data, array $files = [], string $tokenSession = null): array
    {
        $form = self::getConfiguration();
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

    private static function loadFiles(UploadedFile|null $file): DataPart|null
    {
        if (null === $file) {
            return null;
        }

        return DataPart::fromPath($file->getPathname());
    }

    private static function getConfiguration(): array
    {
        return [
            'proxy' => 'http://proxy:80',
            'verify_peer' => false,
            'verify_host' => false,
        ];
    }
}
