<?php

declare(strict_types=1);

namespace App\Twig\Components\User\RegistrationEmailConfirmation;

use App\Twig\Components\Controls\Title\TITLE_TYPE;
use App\Twig\Components\Controls\Title\TitleComponentDto;
use App\Twig\Components\TwigComponent;
use App\Twig\Components\TwigComponentDtoInterface;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(
    name: 'RegistrationEmailConfirmationComponent',
    template: 'Components/User/RegistrationEmailConfirmation/RegistrarionEmailConfirmationComponent.html.twig'
)]
final class RegistrationEmailConfirmationComponent extends TwigComponent
{
    public RegistrationEmailConfirmationComponentDto|TwigComponentDtoInterface $data;

    public readonly TitleComponentDto $titleDto;

    public static function getComponentName(): string
    {
        return 'RegistrationEmailConfirmationComponent';
    }

    public function mount(RegistrationEmailConfirmationComponentDto $data): void
    {
        $this->data = $data;

        $this->titleDto = $this->createTitleDto();
    }

    private function createTitleDto(): TitleComponentDto
    {
        return new TitleComponentDto($this->data->title, TITLE_TYPE::PAGE_MAIN);
    }
}
