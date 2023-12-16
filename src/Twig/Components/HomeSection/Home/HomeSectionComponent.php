<?php

namespace App\Twig\Components\HomeSection\Home;

use App\Twig\Components\AlertValidation\AlertValidationComponentDto;
use App\Twig\Components\Controls\Title\TitleComponentDto;
use App\Twig\Components\HomeSection\HomeList\HomeListComponentBuilder;
use App\Twig\Components\HomeSection\HomeList\List\HomeListComponentDto;
use App\Twig\Components\Modal\ModalComponentDto;
use App\Twig\Components\SearchBar\SearchBarComponentDto;
use App\Twig\Components\TwigComponent;
use App\Twig\Components\TwigComponentDtoInterface;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(
    name: 'HomeSectionComponent',
    template: 'Components/HomeSection/Home/HomeSectionComponent.html.twig'
)]
final class HomeSectionComponent extends TwigComponent
{
    public HomeSectionComponentLangDto $lang;
    public HomeSectionComponentDto|TwigComponentDtoInterface $data;

    public readonly TitleComponentDto $titleDto;
    public readonly SearchBarComponentDto $searchBarFormDto;
    public readonly ModalComponentDto $createFormModalDto;
    public readonly ModalComponentDto $removeMultiFormModalDto;
    public readonly HomeListComponentDto $listComponentDto;
    public readonly AlertValidationComponentDto $alertValidationComponentDto;

    public static function getComponentName(): string
    {
        return 'HomeSectionComponent';
    }

    public function mount(HomeSectionComponentDto $data): void
    {
        $this->data = $data;
        $this->createFormModalDto = $this->data->createFormModalDto;
        $this->removeMultiFormModalDto = $this->data->removeMultiFormModalDto;
        $this->loadTranslation();
        $this->titleDto = $this->createTitleDto();
        $this->searchBarFormDto = $this->data->searchComponentDto;
        $this->listComponentDto = $this->createListComponentDto();
        $this->alertValidationComponentDto = $this->createAlertValidationComponentDto();
    }

    private function createTitleDto(): TitleComponentDto
    {
        return new TitleComponentDto($this->lang->title);
    }

    private function createListComponentDto(): HomeListComponentDto
    {
        return (new HomeListComponentBuilder())
            ->pagination(
                $this->data->page,
                $this->data->pageItems,
                $this->data->pagesTotal
            )
            ->listItemModifyForm(
                $this->data->modifyFormModalDto
            )
            ->listItemRemoveForm(
                $this->data->removeFormModalDto
            )
            ->listItems(
                $this->data->listItemsData,
                $this->data->listItemNoImagePath
            )
            ->validation(
                [],
                false
            )
            ->translationDomainNames(
                $this->data->translationHomeListDomainName,
                $this->data->translationHomeListItemDomainName
            )
            ->build();
    }

    private function createAlertValidationComponentDto(): AlertValidationComponentDto
    {
        return new AlertValidationComponentDto(
            $this->data->homeSectionMessageValidationOk,
            $this->data->homeSectionErrorsMessage
        );
    }

    private function loadTranslation(): void
    {
        $this->setTranslationDomainName($this->data->translationHomeDomainName);
        $this->lang = (new HomeSectionComponentLangDto())
            ->title(
                $this->translate('title')
            )
            ->buttonAdd(
                $this->translate('home_section_add.label'),
                $this->translate('home_section_add.title'),
            )
            ->buttonRemoveMultiple(
                $this->translate('home_section_remove_multiple.label'),
                $this->translate('home_section_remove_multiple.title'),
            )
            ->errors(
                $this->data->validForm ? $this->createAlertValidationComponentDto() : null
            )
            ->build();
    }

    public function loadValidationOkTranslation(): string
    {
        return $this->translate('validation.ok');
    }
}
