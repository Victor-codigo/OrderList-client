<?php

declare(strict_types=1);

namespace App\Twig\Components\Order\OrderHome\Home;

use App\Twig\Components\TwigComponent;
use App\Twig\Components\TwigComponentDtoInterface;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(
    name: 'OrderHomeSectionComponent',
    template: 'Components/Order/OrderHome/Home/OrderHomeSectionComponent.html.twig'
)]
class OrderHomeSectionComponent extends TwigComponent
{
    public OrderHomeSectionComponentLangDto $lang;
    public OrderHomeSectionComponentDto|TwigComponentDtoInterface $data;

    public static function getComponentName(): string
    {
        return 'OrderHomeSectionComponent';
    }

    public function mount(OrderHomeSectionComponentDto $data): void
    {
        $this->data = $data;

        $this->loadTranslation();
    }

    private function loadTranslation(): void
    {
        $this->lang = new OrderHomeSectionComponentLangDto(
            $this->translate('home_header.currentBought'),
            $this->translate('home_header.totalBought'),
        );
    }
}
