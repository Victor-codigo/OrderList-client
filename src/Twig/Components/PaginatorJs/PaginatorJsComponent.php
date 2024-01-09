<?php

declare(strict_types=1);

namespace App\Twig\Components\PaginatorJs;

use App\Twig\Components\TwigComponent;
use App\Twig\Components\TwigComponentDtoInterface;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(
    name: 'PaginatorJsComponent',
    template: 'Components/PaginatorJs/PaginatorJsComponent.html.twig'
)]
class PaginatorJsComponent extends TwigComponent
{
    public PaginatorJsComponentLangDto $lang;
    public PaginatorJsComponentDto|TwigComponentDtoInterface $data;

    protected static function getComponentName(): string
    {
        return 'PaginatorJsComponent';
    }

    public function mount(PaginatorJsComponentDto $data): void
    {
        $this->data = $data;

        $this->loadTranslation();
    }

    private function loadTranslation(): void
    {
        $this->lang = new PaginatorJsComponentLangDto(
            $this->translate('page.previous'),
            $this->translate('page.next')
        );
    }
}
