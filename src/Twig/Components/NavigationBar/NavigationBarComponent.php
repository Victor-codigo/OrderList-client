<?php

declare(strict_types=1);

namespace App\Twig\Components\NavigationBar;

use App\Twig\Components\NavigationBar\NavigationBarDto;
use App\Twig\Components\TwigComponent;
use App\Twig\Components\TwigComponentDtoInterface;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(
    name: 'NavigationBarComponent',
    template: 'Components/NavigationBar/NavigationBarComponent.html.twig'
)]
class NavigationBarComponent extends TwigComponent
{
    public NavigationBarLangDto $lang;
    public NavigationBarDto|TwigComponentDtoInterface $data;

    public readonly string $cssType;
    public readonly string $cssTextColor;

    protected static function getComponentName(): string
    {
        return 'NavigationBarComponent';
    }

    public function mount(NavigationBarDto $data): void
    {
        $this->data = $data;
        $this->loadTranslation();
    }

    private function loadTranslation()
    {
        $this->lang = (new NavigationBarLangDto())
            ->title('OrderList')
            ->addSection('Home')
            ->addSection('Login')
            ->build();
    }
}
