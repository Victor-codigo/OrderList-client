<?php

declare(strict_types=1);

namespace App\Twig\Components\Common\Legal\Privacy;

use App\Twig\Components\Controls\Title\TITLE_TYPE;
use App\Twig\Components\Controls\Title\TitleComponentDto;
use App\Twig\Components\TwigComponent;
use App\Twig\Components\TwigComponentDtoInterface;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(
    name: 'PrivacyComponent',
    template: 'Components/Legal/Privacy/PrivacyComponent.html.twig'
)]
final class PrivacyComponent extends TwigComponent
{
    public PrivacyComponentLangDto $lang;
    public PrivacyComponentDto|TwigComponentDtoInterface $data;

    public readonly TitleComponentDto $titleDto;

    public static function getComponentName(): string
    {
        return 'PrivacyComponent';
    }

    public function mount(PrivacyComponentDto $data): void
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
        $this->lang = (new PrivacyComponentLangDto())
        ->title(
            $this->translate('title'),
        )
        ->name(
            $this->translate('name.label'),
            $this->translate('name.placeholder'),
            $this->translate('name.msg_invalid'),
        )
        ->description(
            $this->translate('description.label'),
            $this->translate('description.placeholder'),
            $this->translate('description.msg_invalid'),
        )
        ->createButton(
            $this->translate('button_group_create.label'),
        )
        ->build();
    }
}
