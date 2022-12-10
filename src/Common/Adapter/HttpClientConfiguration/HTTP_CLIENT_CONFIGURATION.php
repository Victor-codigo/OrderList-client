<?php

declare(strict_types=1);

namespace Common\Adapter\HttpClientConfiguration;

class HTTP_CLIENT_CONFIGURATION
{
    public const API_DOMAIN = 'http://orderlist.api';
    public const CLIENT_DOMAIN = 'http://orderlist.client';
    public const XDEBUG_VAR = 'XDEBUG_SESSION=VSCODE';

    public static function json(array $data): array
    {
        return [
            'proxy' => 'http://proxy:80',
            'verify_peer' => false,
            'verify_host' => false,
            'json' => $data
        ];
    }
}
