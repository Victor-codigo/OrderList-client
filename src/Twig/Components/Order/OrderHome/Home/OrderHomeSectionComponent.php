<?php

declare(strict_types=1);

namespace App\Twig\Components\Order\OrderHome\Home;

use App\Twig\Components\Controls\ButtonLoading\ButtonLoadingComponentDto;
use App\Twig\Components\Controls\InfoModal\INFO_MODAL_TYPE;
use App\Twig\Components\Controls\InfoModal\InfoModalComponent;
use App\Twig\Components\Controls\InfoModal\InfoModalComponentDto;
use App\Twig\Components\Modal\ModalComponentDto;
use App\Twig\Components\TwigComponent;
use App\Twig\Components\TwigComponentDtoInterface;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(
    name: 'OrderHomeSectionComponent',
    template: 'Components/Order/OrderHome/Home/OrderHomeSectionComponent.html.twig'
)]
class OrderHomeSectionComponent extends TwigComponent
{
    public OrderHomeSectionComponentLangDto $lang;
    public OrderHomeSectionComponentDto|TwigComponentDtoInterface $data;
    public readonly ButtonLoadingComponentDto $buttonShareWhatsApp;
    public ModalComponentDto $guestUserRestrictionInfoModalDto;
    public ModalComponentDto $shareBrowserNotCompatibleInfoModalDto;

    public static function getComponentName(): string
    {
        return 'OrderHomeSectionComponent';
    }

    public function mount(OrderHomeSectionComponentDto $data): void
    {
        $this->data = $data;

        $this->buttonShareWhatsApp = $this->createButtonShareWhatsApp();
        $this->guestUserRestrictionInfoModalDto = $this->createInfoModalDto(
            'info_guest_user_restriction_modal',
            $this->translate('home_section_info_guest_user.share.guest_restriction')
        );
        $this->shareBrowserNotCompatibleInfoModalDto = $this->createInfoModalDto(
            'info_share_browser_not_compatible_modal',
            $this->translate('home_section_info_guest_user.share.not_compatible')
        );
        $this->loadTranslation();
    }

    private function createButtonShareWhatsApp(): ButtonLoadingComponentDto
    {
        return new ButtonLoadingComponentDto(
            'data-js-share-button',
            'button',
            '',
            '',
            $this->translate('home_section_share_in_whatsapp.title'),
            'common/share-icon.svg',
        );
    }

    private function createInfoModalDto(string $idAttribute, string $messageInfo): ModalComponentDto
    {
        $infoModalDto = new InfoModalComponentDto(
            '',
            '',
            $messageInfo,
            INFO_MODAL_TYPE::INFO
        );

        return new ModalComponentDto(
            $idAttribute,
            '',
            false,
            InfoModalComponent::getComponentName(),
            $infoModalDto,
            []
        );
    }

    private function loadTranslation(): void
    {
        $this->lang = (new OrderHomeSectionComponentLangDto())
        ->headerBoughtCounter(
            $this->translate('home_header.currentBought'),
            $this->translate('home_header.totalBought'),
        )
        ->buttonShareWhatsApp(
            $this->translate('home_section_share_in_whatsapp.label'),
            $this->translate('home_section_share_in_whatsapp.title'),
        )
        ->build();
    }
}
