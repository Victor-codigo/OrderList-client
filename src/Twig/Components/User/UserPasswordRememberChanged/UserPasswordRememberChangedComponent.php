<?php

declare(strict_types=1);

namespace App\Twig\Components\User\UserPasswordRememberChanged;

use App\Twig\Components\Controls\Title\TITLE_TYPE;
use App\Twig\Components\Controls\Title\TitleComponentDto;
use App\Twig\Components\TwigComponent;
use App\Twig\Components\TwigComponentDtoInterface;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(
    name: 'UserPasswordRememberChangedComponent',
    template: 'Components/User/UserPasswordRememberChanged/UserPasswordRememberChangedComponent.html.twig'
)]
final class UserPasswordRememberChangedComponent extends TwigComponent
{
    public UserPasswordRememberChangedComponentLangDto $lang;
    public UserPasswordRememberChangedComponentDto|TwigComponentDtoInterface $data;

    public readonly TitleComponentDto $titleDto;

    public static function getComponentName(): string
    {
        return 'UserPasswordRememberChangedComponent';
    }

    public function mount(UserPasswordRememberChangedComponentDto $data): void
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
        $this->lang = new UserPasswordRememberChangedComponentLangDto(
            $this->translate('title', []),
            $this->translate('message', ['urlLoginForm' => $this->data->urlLoginForm]),
        );
    }
}
