<?php

declare(strict_types=1);

namespace App\Twig\Components\Order\OrderHome\ListItem;

use App\Twig\Components\HomeSection\HomeList\ListItem\HomeListItemComponent;
use App\Twig\Components\HomeSection\HomeList\ListItem\HomeListItemComponentDto;
use App\Twig\Components\HomeSection\HomeList\ListItem\HomeListItemComponentLangDto;
use App\Twig\Components\TwigComponentDtoInterface;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(
    name: 'OrderListItemComponent',
    template: 'Components/Order/OrderHome/ListItem/OrderListItemComponent.html.twig'
)]
final class OrderListItemComponent extends HomeListItemComponent
{
    public HomeListItemComponentLangDto $lang;
    public OrderListItemComponentDto|TwigComponentDtoInterface $data;

    public readonly string $orderDataJson;

    public static function getComponentName(): string
    {
        return 'OrderListItemComponent';
    }

    public function mount(HomeListItemComponentDto $data): void
    {
        $this->data = $data;
        $this->loadTranslation();
        $this->orderDataJson = $this->parseItemDataToJson($data);
    }

    protected function loadTranslation(): void
    {
        $this->setTranslationDomainName($this->data->translationDomainName);
        $this->lang = new OrderListItemComponentLangDto(
            $this->translate('order_modify_button.alt'),
            $this->translate('order_modify_button.title'),
            $this->translate('order_remove_button.alt'),
            $this->translate('order_remove_button.title'),
            $this->translate('order_info_button.alt'),
            $this->translate('order_info_button.title'),
            $this->translate('order_image.alt'),
            $this->translate('order_image.title'),
        );
    }

    private function parseItemDataToJson(OrderListItemComponentDto $orderData): string
    {
        $orderDataToParse = [
            'id' => $orderData->id,
            'name' => $orderData->name,
            'description' => $orderData->description,
            'image' => $orderData->image,
            'createdOn' => $orderData->createdOn->format('Y-m-d'),
        ];

        return json_encode($orderDataToParse, JSON_THROW_ON_ERROR);
    }
}
