<?php

declare(strict_types=1);

namespace Common\Adapter\Events\DataLoader;

use App\Controller\Request\Response\NotificationDataResponse;
use Common\Adapter\Endpoints\Endpoints;
use Common\Adapter\Events\Exceptions\RequestNotificationsException;
use Common\Domain\Config\Config;
use Common\Domain\JwtToken\JwtToken;

class NotificationDataLoader
{
    public function __construct(
        private Endpoints $endpoints
    ) {
    }

    /**
     * @throws RequestNotificationsException
     * @throws JwtTokenGetPayLoadException
     */
    public function load(string $lang, ?string $tokenSession): array
    {
        if (!JwtToken::hasSessionActive($tokenSession)) {
            return [];
        }

        $notificationsData = $this->endpoints->notificationGetData(1, Config::PAGINATION_ITEMS_MAX, $lang, $tokenSession);

        if (!empty($notificationsData['errors'])
        && (count($notificationsData['errors']) > 1 || !array_key_exists('notification_not_found', $notificationsData['errors']))) {
            throw RequestNotificationsException::fromMessage('Notifications data not found');
        }

        return array_map(
            fn (array $notificationData) => NotificationDataResponse::fromArray($notificationData),
            $notificationsData['data']['notifications']
        );

        return empty($notificationsData['data']['notifications'])
            ? []
            : NotificationDataResponse::fromArray($notificationsData['data']['notifications']);
    }
}
