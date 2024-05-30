<?php

declare(strict_types=1);

namespace App\Twig\Components\GroupUsers\GroupUsersHome\Home;

use App\Twig\Components\Controls\InfoModal\INFO_MODAL_TYPE;
use App\Twig\Components\Controls\InfoModal\InfoModalComponent;
use App\Twig\Components\Controls\InfoModal\InfoModalComponentDto;
use App\Twig\Components\Modal\ModalComponentDto;
use App\Twig\Components\TwigComponent;
use App\Twig\Components\TwigComponentDtoInterface;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(
    name: 'GroupUsersHomeSectionComponent',
    template: 'Components/GroupUsers/GroupUsersHome/Home/GroupUsersHomeSectionComponent.html.twig'
)]
class GroupUsersHomeSectionComponent extends TwigComponent
{
    private const GROUP_CREATE_MODAL_ID = 'info_modal';

    public GroupUsersHomeComponentLangDto $lang;
    public GroupUsersHomeSectionComponentDto|TwigComponentDtoInterface $data;

    public ModalComponentDto $infoModalDto;

    public static function getComponentName(): string
    {
        return 'GroupUsersHomeSectionComponent';
    }

    public function mount(GroupUsersHomeSectionComponentDto $data): void
    {
        $this->data = $data;

        $this->loadTranslation();

        $this->infoModalDto = $this->createInfoModalDto();
    }

    private function createInfoModalDto(): ModalComponentDto
    {
        $infoModalDto = new InfoModalComponentDto(
            self::GROUP_CREATE_MODAL_ID,
            $this->lang->infoModalTitle,
            $this->lang->infoModalText,
            INFO_MODAL_TYPE::WARNING
        );

        return new ModalComponentDto(
            self::GROUP_CREATE_MODAL_ID,
            '',
            false,
            InfoModalComponent::getComponentName(),
            $infoModalDto,
            []
        );
    }

    protected function loadTranslation(): void
    {
        $this->setTranslationDomainName($this->getComponentName());
        $this->lang = (new GroupUsersHomeComponentLangDto())
            ->infoModal(
                $this->translate('info_modal.title'),
                $this->translate('info_modal.text'),
                $this->translate('info_modal.close_button'),
            )
            ->build();
    }
}
