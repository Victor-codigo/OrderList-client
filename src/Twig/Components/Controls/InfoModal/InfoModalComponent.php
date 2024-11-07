<?php

declare(strict_types=1);

namespace App\Twig\Components\Controls\InfoModal;

use App\Twig\Components\Controls\Title\TITLE_TYPE;
use App\Twig\Components\Controls\Title\TitleComponentDto;
use App\Twig\Components\TwigComponent;
use App\Twig\Components\TwigComponentDtoInterface;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(
    name: 'InfoModalComponent',
    template: 'Components/Controls/InfoModal/InfoModalComponent.html.twig'
)]
class InfoModalComponent extends TwigComponent
{
    public readonly InfoModalComponentLangDto $lang;
    public InfoModalComponentDto|TwigComponentDtoInterface $data;

    public readonly TitleComponentDto $titleDto;

    public static function getComponentName(): string
    {
        return 'InfoModalComponent';
    }

    public function mount(InfoModalComponentDto $data): void
    {
        $this->data = $data;
        $this->loadTranslation();

        $this->titleDto = $this->createTitle();
    }

    private function createTitle(): TitleComponentDto
    {
        return new TitleComponentDto($this->data->title, TITLE_TYPE::POP_UP, null);
    }

    protected function loadTranslation(): void
    {
        $this->lang = new InfoModalComponentLangDto(
            $this->translate('close_button.label'),
            $this->translate('close_button.title')
        );
    }

    public function getType(): string
    {
        return $this->data->type->value;
    }
}
