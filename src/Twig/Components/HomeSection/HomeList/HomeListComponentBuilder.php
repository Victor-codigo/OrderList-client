<?php

declare(strict_types=1);

namespace App\Twig\Components\HomeSection\HomeList;

use App\Controller\Request\Response\ShopDataResponse;
use App\Twig\Components\HomeSection\HomeList\ListItem\HomeListItemComponent;
use App\Twig\Components\HomeSection\HomeList\ListItem\HomeListItemComponentDto;
use App\Twig\Components\HomeSection\HomeList\List\HomeListComponentDto;
use App\Twig\Components\Modal\ModalComponentDto;
use App\Twig\Components\Paginator\PaginatorComponentDto;
use Common\Domain\DtoBuilder\DtoBuilder;

class HomeListComponentBuilder
{
    private DtoBuilder $builder;

    private readonly array $errors;
    /**
     * @param ShopDataResponse[] $listItems
     */
    private readonly array $listItems;
    private readonly int $page;
    private readonly int $pageItems;
    private readonly int $pagesTotal;
    private readonly bool $validation;
    private readonly string $homeListItemNoImagePath;

    private readonly string $translationListDomainName;
    private readonly string $translationListItemDomainName;

    private readonly ModalComponentDto $homeListItemModifyFormModalDto;
    private readonly ModalComponentDto $homeListItemRemoveFormModalDto;

    public function __construct()
    {
        $this->builder = new DtoBuilder([
            'validation',
            'pagination',
            'listItems',
            'listItemModifyForm',
            'listItemRemoveForm',
            'translationDomainNames',
        ]);
    }

    public function validation(array $errors, bool $validation): self
    {
        $this->builder->setMethodStatus('validation', true);

        $this->errors = $errors;
        $this->validation = $validation;

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

    public function listItems(array $listItems, string $homeListItemNoImagePath): self
    {
        $this->builder->setMethodStatus('listItems', true);

        $this->listItems = $listItems;
        $this->homeListItemNoImagePath = $homeListItemNoImagePath;

        return $this;
    }

    public function listItemModifyForm(ModalComponentDto $homeListItemModifyFormModalDto): self
    {
        $this->builder->setMethodStatus('listItemModifyForm', true);

        $this->homeListItemModifyFormModalDto = $homeListItemModifyFormModalDto;

        return $this;
    }

    public function listItemRemoveForm(ModalComponentDto $homeListItemRemoveFormModalDto): self
    {
        $this->builder->setMethodStatus('listItemRemoveForm', true);

        $this->homeListItemRemoveFormModalDto = $homeListItemRemoveFormModalDto;

        return $this;
    }

    public function translationDomainNames(string $listDomainName, string $listItemDomainName): self
    {
        $this->builder->setMethodStatus('translationDomainNames', true);

        $this->translationListDomainName = $listDomainName;
        $this->translationListItemDomainName = $listItemDomainName;

        return $this;
    }

    /**
     * @throws InvalidArgumentException
     */
    public function build(): HomeListComponentDto
    {
        $this->builder->validate();

        $paginator = $this->createPaginatorComponentDto($this->page, $this->pageItems, $this->pagesTotal);
        $homesListItems = $this->createHomeListItemComponentDto($this->listItems);

        return $this->createHomeListComponentDto(
            $paginator,
            $homesListItems,
        );
    }

    /**
     * @param HomeListItemComponentDto[] $homesListsItems
     */
    private function createHomeListComponentDto(PaginatorComponentDto $paginatorDto, array $homesListsItems): HomeListComponentDto
    {
        return new HomeListComponentDto(
            $this->errors,
            $homesListsItems,
            $paginatorDto,
            $this->validation,
            $this->homeListItemRemoveFormModalDto,
            $this->homeListItemModifyFormModalDto,
            $this->translationListDomainName
        );
    }

    /**
     * @param HomeDataResponse[] $listItems
     */
    private function createHomeListItemComponentDto(array $listItems): array
    {
        return array_map(
            fn (ShopDataResponse $homeData) => new HomeListItemComponentDto(
                HomeListItemComponent::getComponentName(),
                $homeData->id,
                $homeData->name,
                $homeData->description,
                null === $homeData->image ? $this->homeListItemNoImagePath : $homeData->image,
                $homeData->createdOn,
                $this->homeListItemModifyFormModalDto->idAttribute,
                $this->homeListItemRemoveFormModalDto->idAttribute,
                $this->translationListItemDomainName
            ),
            $listItems
        );
    }

    private function createPaginatorComponentDto(int $page, int $pageItems, int $pagesTotal): PaginatorComponentDto
    {
        return new PaginatorComponentDto($page, $pagesTotal, "page-{pageNum}-{$pageItems}");
    }
}
