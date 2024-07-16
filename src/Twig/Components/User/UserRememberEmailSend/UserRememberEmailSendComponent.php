<?php

declare(strict_types=1);

namespace App\Twig\Components\User\UserRememberEmailSend;

use App\Twig\Components\Controls\Title\TITLE_TYPE;
use App\Twig\Components\Controls\Title\TitleComponentDto;
use App\Twig\Components\TwigComponent;
use App\Twig\Components\TwigComponentDtoInterface;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(
    name: 'UserRememberEmailSendComponent',
    template: 'Components/User/UserRememberEmailSend/UserRememberEmailSendComponent.html.twig'
)]
final class UserRememberEmailSendComponent extends TwigComponent
{
    public UserRememberEmailSendComponentLangDto $lang;
    public UserRememberEmailSendComponentDto|TwigComponentDtoInterface $data;

    public readonly TitleComponentDto $titleDto;

    public static function getComponentName(): string
    {
        return 'UserRememberEmailSendComponent';
    }

    public function mount(UserRememberEmailSendComponentDto $data): void
    {
        $this->data = $data;

        $this->loadTranslation();

        $this->titleDto = $this->createTitleDto();
    }

    private function createTitleDto(): TitleComponentDto
    {
        return new TitleComponentDto($this->lang->title, TITLE_TYPE::PAGE_MAIN, null);
    }

    private function loadTranslation(): void
    {
        $this->lang = new UserRememberEmailSendComponentLangDto(
            $this->translate('title', []),
            $this->translate('message', ['urlRememberPasswordForm' => $this->data->urlRememberPasswordForm]),
        );
    }
}
