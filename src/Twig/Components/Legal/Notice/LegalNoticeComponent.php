<?php

declare(strict_types=1);

namespace App\Twig\Components\Legal\Notice;

use App\Twig\Components\Controls\Title\TITLE_TYPE;
use App\Twig\Components\Controls\Title\TitleComponentDto;
use App\Twig\Components\TwigComponent;
use App\Twig\Components\TwigComponentDtoInterface;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(
    name: 'LegalNoticeComponent',
    template: 'Components/Legal/Notice/LegalComponent.html.twig'
)]
final class LegalNoticeComponent extends TwigComponent
{
    public LegalNoticeComponentLangDto $lang;
    public LegalNoticeComponentDto|TwigComponentDtoInterface $data;

    public readonly TitleComponentDto $titleDto;

    public static function getComponentName(): string
    {
        return 'LegalNoticeComponent';
    }

    public function mount(LegalNoticeComponentDto $data): void
    {
        $this->loadTranslation();
        $this->titleDto = $this->createTitleComponent();
    }

    private function createTitleComponent(): TitleComponentDto
    {
        return new TitleComponentDto($this->lang->title, TITLE_TYPE::PAGE_MAIN);
    }

    private function loadTranslation(): void
    {
        $this->lang = (new LegalNoticeComponentLangDto())
        ->title(
            $this->translate('title'),
        )
        ->build();
    }
}
