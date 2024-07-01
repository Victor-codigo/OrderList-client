<?php

declare(strict_types=1);

namespace App\Twig\Components\User\RegistrationComplete;

use App\Twig\Components\Controls\Title\TITLE_TYPE;
use App\Twig\Components\Controls\Title\TitleComponentDto;
use App\Twig\Components\TwigComponent;
use App\Twig\Components\TwigComponentDtoInterface;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(
    name: 'RegistrationCompleteComponent',
    template: 'Components/User/RegistrationComplete/RegistrationCompleteComponent.html.twig'
)]
final class RegistrationCompleteComponent extends TwigComponent
{
    public RegistrationCompleteComponentLangDto $lang;
    public RegistrationCompleteComponentDto|TwigComponentDtoInterface $data;

    public readonly TitleComponentDto $titleDto;

    public static function getComponentName(): string
    {
        return 'RegistrationCompleteComponent';
    }

    public function mount(RegistrationCompleteComponentDto $data): void
    {
        $this->data = $data;

        $this->loadTranslation();

        $this->titleDto = $this->createTitleDto();
    }

    private function createTitleDto(): TitleComponentDto
    {
        return new TitleComponentDto($this->lang->title, TITLE_TYPE::PAGE_MAIN);
    }

    private function loadTranslation(): void
    {
        $this->lang = new RegistrationCompleteComponentLangDto(
            $this->translate('title', []),
            $this->translate('msg', ['appName' => $this->data->domainName]),
        );
    }
}
