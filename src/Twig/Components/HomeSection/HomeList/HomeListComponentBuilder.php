<?php

declare(strict_types=1);

namespace App\Twig\Components\HomeSection\HomeList;

use App\Twig\Components\HomeSection\HomeList\List\HomeListComponentDto;
use App\Twig\Components\HomeSection\HomeList\ListItem\HomeListItemComponentDto;
use App\Twig\Components\Modal\ModalComponentDto;
use App\Twig\Components\Paginator\PaginatorComponentDto;
use Common\Domain\DtoBuilder\DtoBuilder;

class HomeListComponentBuilder
{
    private DtoBuilder $builder;

    private readonly array $errors;
    private readonly string $listItemComponentName;
    /**
     * @param object[] $listItems
     */
    private readonly array $listItems;
    private readonly int $page;
    private readonly int $pageItems;
    private readonly int $pagesTotal;
    private readonly bool $validation;

    private readonly string $translationListDomainName;

    private readonly ?ModalComponentDto $homeListItemModifyFormModalDto;
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

    /**
     * @param HomeListItemComponentDto[] $listItems
     */
    public function listItems(string $listItemComponentName, array $listItems): self
    {
        $this->builder->setMethodStatus('listItems', true);

        $this->listItemComponentName = $listItemComponentName;
        $this->listItems = $listItems;

        return $this;
    }

    public function listItemModifyForm(?ModalComponentDto $homeListItemModifyFormModalDto): self
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

    public function translationDomainNames(string $listDomainName): self
    {
        $this->builder->setMethodStatus('translationDomainNames', true);

        $this->translationListDomainName = $listDomainName;

        return $this;
    }

    /**
     * @throws InvalidArgumentException
     */
    public function build(): HomeListComponentDto
    {
        $this->builder->validate();

        $paginator = $this->createPaginatorComponentDto($this->page, $this->pageItems, $this->pagesTotal);

        return $this->createHomeListComponentDto($paginator);
    }

    private function createHomeListComponentDto(PaginatorComponentDto $paginatorDto): HomeListComponentDto
    {
        return new HomeListComponentDto(
            $this->errors,
            $this->listItemComponentName,
            $this->listItems,
            $paginatorDto,
            $this->validation,
            $this->homeListItemRemoveFormModalDto,
            $this->homeListItemModifyFormModalDto,
            $this->translationListDomainName
        );
    }

    private function createPaginatorComponentDto(int $page, int $pageItems, int $pagesTotal): PaginatorComponentDto
    {
        return new PaginatorComponentDto($page, $pagesTotal, "page-{pageNum}-{$pageItems}");
    }
}
