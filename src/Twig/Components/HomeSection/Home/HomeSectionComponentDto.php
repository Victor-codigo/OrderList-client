<?php

declare(strict_types=1);

namespace App\Twig\Components\HomeSection\Home;

use App\Twig\Components\Modal\ModalComponentDto;
use App\Twig\Components\SearchBar\SearchBarComponentDto;
use App\Twig\Components\TwigComponentDtoInterface;
use Common\Domain\DtoBuilder\DtoBuilder;
use Common\Domain\DtoBuilder\DtoBuilderInterface;

class HomeSectionComponentDto implements TwigComponentDtoInterface, DtoBuilderInterface
{
    /**
     * @param string[]
     */
    public readonly array $homeSectionErrorsMessage;
    /**
     * @param string[]
     */
    public readonly array $homeSectionMessageValidationOk;

    public readonly array $listItemsData;
    public readonly string $listItemNoImagePath;

    public readonly string $translationHomeDomainName;
    public readonly string $translationHomeListDomainName;
    public readonly string $translationHomeListItemDomainName;

    public readonly int $page;
    public readonly int $pageItems;
    public readonly int $pagesTotal;

    public readonly bool $validForm;

    public readonly SearchBarComponentDto $searchComponentDto;
    public readonly RemoveMultiFormDto $removeMultiFormDto;
    public readonly ModalComponentDto $createFormModalDto;
    public readonly ModalComponentDto $modifyFormModalDto;
    public readonly ModalComponentDto $removeMultiFormModalDto;
    public readonly ModalComponentDto $removeFormModalDto;

    private DtoBuilder $builder;

    public function __construct()
    {
        $this->builder = new DtoBuilder([
            'searchBar',
            'createFormModal',
            'modifyFormModal',
            'removeFormModal',
            'removeMultiFormModal',
            'createRemoveMultiForm',
            'errors',
            'pagination',
            'listItems',
            'validation',
            'group',
            'translationDomainNames',
        ]);
    }

    public function searchBar(SearchBarComponentDto $searchBarComponentDto): self
    {
        $this->builder->setMethodStatus('searchBar', true);

        $this->searchComponentDto = $searchBarComponentDto;

        return $this;
    }

    public function removeMultiFormModal(ModalComponentDto $removeMultiFormModalDto): self
    {
        $this->builder->setMethodStatus('removeMultiFormModal', true);

        $this->removeMultiFormModalDto = $removeMultiFormModalDto;

        return $this;
    }

    public function removeFormModal(ModalComponentDto $removeFormModalDto): self
    {
        $this->builder->setMethodStatus('removeFormModal', true);

        $this->removeFormModalDto = $removeFormModalDto;

        return $this;
    }

    public function createFormModal(ModalComponentDto $createFormModalDto): self
    {
        $this->builder->setMethodStatus('createFormModal', true);

        $this->createFormModalDto = $createFormModalDto;

        return $this;
    }

    public function modifyFormModal(ModalComponentDto $modifyFormModalDto): self
    {
        $this->builder->setMethodStatus('modifyFormModal', true);

        $this->modifyFormModalDto = $modifyFormModalDto;

        return $this;
    }

    public function createRemoveMultiForm(RemoveMultiFormDto $removeMultiFormDto): self
    {
        $this->builder->setMethodStatus('createRemoveMultiForm', true);

        $this->removeMultiFormDto = $removeMultiFormDto;

        return $this;
    }

    public function errors(array $homeSectionValidationOk, array $homeValidationErrorsMessage): self
    {
        $this->builder->setMethodStatus('errors', true);

        $this->homeSectionMessageValidationOk = $homeSectionValidationOk;
        $this->homeSectionErrorsMessage = $homeValidationErrorsMessage;

        return $this;
    }

    public function pagination(int $page, int $pageItems, int $pagesTotal): self
    {
        $this->builder->setMethodStatus('pagination', true);

        $this->page = $page;
        $this->pageItems = $pageItems;
        $this->pagesTotal = $pagesTotal;

        return $this;
    }

    public function listItems(array $listItemsData, string $listItemNoImagePath): self
    {
        $this->builder->setMethodStatus('listItems', true);

        $this->listItemsData = $listItemsData;
        $this->listItemNoImagePath = $listItemNoImagePath;

        return $this;
    }

    public function validation(bool $validForm): self
    {
        $this->builder->setMethodStatus('validation', true);

        $this->validForm = $validForm;

        return $this;
    }

    public function translationDomainNames(string $homeDomainName, string $homeListDomainName, string $homeListItemDomanName): self
    {
        $this->builder->setMethodStatus('translationDomainNames', true);

        $this->translationHomeDomainName = $homeDomainName;
        $this->translationHomeListDomainName = $homeListDomainName;
        $this->translationHomeListItemDomainName = $homeListItemDomanName;

        return $this;
    }

    public function build(): self
    {
        $this->builder->validate();

        return $this;
    }
}
