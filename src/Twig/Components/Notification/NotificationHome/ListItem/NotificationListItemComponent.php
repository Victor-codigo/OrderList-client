<?php

declare(strict_types=1);

namespace App\Twig\Components\Notification\NotificationHome\ListItem;

use App\Twig\Components\HomeSection\HomeList\ListItem\HomeListItemComponent;
use App\Twig\Components\HomeSection\HomeList\ListItem\HomeListItemComponentDto;
use App\Twig\Components\HomeSection\HomeList\ListItem\HomeListItemComponentLangDto;
use App\Twig\Components\TwigComponentDtoInterface;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(
    name: 'NotificationListItemComponent',
    template: 'Components/Notification/NotificationHome/ListItem/NotificationListItemComponent.html.twig'
)]
final class NotificationListItemComponent extends HomeListItemComponent
{
    public HomeListItemComponentLangDto $lang;
    public NotificationListItemComponentDto|TwigComponentDtoInterface $data;

    public readonly string $notificationDataJson;

    public static function getComponentName(): string
    {
        return 'NotificationListItemComponent';
    }

    public function mount(HomeListItemComponentDto $data): void
    {
        $this->data = $data;
        $this->loadTranslation();
        $this->notificationDataJson = $this->parseItemDataToJson($data);
    }

    protected function loadTranslation(): void
    {
        $this->setTranslationDomainName($this->data->translationDomainName);
        $this->lang = new NotificationListItemComponentLangDto(
            $this->translate('notification_remove_button.title'),
            $this->translate('notification_info_button.title'),
            $this->translate('notification_image.alt'),
            $this->translate('notification_image.title'),
        );
    }

    private function parseItemDataToJson(NotificationListItemComponentDto $notificationData): string
    {
        $notificationDataToParse = [
            'id' => $notificationData->id,
            'message' => $notificationData->message,
            'image' => $notificationData->image,
            'viewed' => $notificationData->viewed,
            'createdOn' => $notificationData->createdOn->format('Y-m-d H:i:s'),
        ];

        return json_encode($notificationDataToParse, JSON_THROW_ON_ERROR);
    }
}
