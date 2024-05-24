<?php

declare(strict_types=1);

namespace App\Twig\Components\Home\Home;

use App\Twig\Components\TwigComponent;
use App\Twig\Components\TwigComponentDtoInterface;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(
    name: 'HomePageComponent',
    template: 'Components/Home/Home/HomePageComponent.html.twig'
)]
class HomePageComponent extends TwigComponent
{
    public HomePageComponentDto|TwigComponentDtoInterface $data;

    protected static function getComponentName(): string
    {
        return 'HomePageComponent';
    }

    public function mount(HomePageComponentDto $data): void
    {
        $this->data = $data;
    }
}
