<?php

declare(strict_types=1);

namespace App\Twig\Components\Shop\ShopHome;

use App\Twig\Components\TwigComponentDtoInterface;
use Common\Domain\DtoBuilder\DtoBuilder;
use Common\Domain\DtoBuilder\DtoBuilderInterface;

class ShopHomeComponentDto implements TwigComponentDtoInterface, DtoBuilderInterface
{
    public readonly array $shopHomeMessageValidationOk;

    /**
     * @param array<int, string> $shopErrorsMessage
     */
    public readonly array $shopErrorsMessage;
    public readonly array $shopsData;
    public readonly int $page;
    public readonly int $pageItems;
    public readonly int $pagesTotal;
    public readonly string $groupNameUrlEncoded;
    public readonly string $shopNoImagePath;
    public readonly string $shopCreateFormCsrfToken;
    public readonly string $shopModifyCsrfToken;
    public readonly string $shopRemoveFormCsrfToken;
    public readonly string $shopRemoveMultiFormCsrfToken;
    public readonly bool $validForm;
    public readonly string $shopCreateFormActionUrl;
    public readonly string $shopModifyFormActionUrlPlaceholder;
    public readonly string $shopRemoveFormActionUrl;

    public readonly string|null $searchBarFilterType;
    public readonly string|null $searchBarFilterValue;
    public readonly string $searchBarCsrfToken;
    public readonly string $searchBarFormActionUrl;
    public readonly string $searchAutoCompleteUrl;
    public readonly string $groupId;

    private DtoBuilder $builder;

    public function __construct()
    {
        $this->builder = new DtoBuilder([
            'searchBar',
            'errors',
            'pagination',
            'shops',
            'formCsrfToken',
            'formValid',
            'group',
        ]);
    }

    public function searchBar(
        string $groupId,
        string|null $searchBarFilterType,
        string|null $searchBarFilterValue,
        string $searchBarCsrfToken,
        string $searchAutoCompleteUrl,
        string $searchBarFormActionUrl,
    ): self {
        $this->builder->setMethodStatus('searchBar', true);

        $this->searchBarFilterType = $searchBarFilterType;
        $this->searchBarFilterValue = $searchBarFilterValue;
        $this->searchBarCsrfToken = $searchBarCsrfToken;
        $this->searchAutoCompleteUrl = $searchAutoCompleteUrl;
        $this->searchBarFormActionUrl = $searchBarFormActionUrl;
        $this->groupId = $groupId;

        return $this;
    }

    public function errors(array $shopHomeValidationOk, array $shopValidationErrorsMessage): self
    {
        $this->builder->setMethodStatus('errors', true);

        $this->shopHomeMessageValidationOk = $shopHomeValidationOk;
        $this->shopErrorsMessage = $shopValidationErrorsMessage;

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

    public function shops(array $shopsData, string $shopNoImagePath): self
    {
        $this->builder->setMethodStatus('shops', true);

        $this->shopsData = $shopsData;
        $this->shopNoImagePath = $shopNoImagePath;

        return $this;
    }

    public function formCsrfToken(string $shopCreateFormCsrfToken, string $shopModifyFormCsrfToken, string $shopRemoveFormCsrfToken, string $shopRemoveMultiFormCsrfToken): self
    {
        $this->builder->setMethodStatus('formCsrfToken', true);

        $this->shopCreateFormCsrfToken = $shopCreateFormCsrfToken;
        $this->shopModifyCsrfToken = $shopModifyFormCsrfToken;
        $this->shopRemoveFormCsrfToken = $shopRemoveFormCsrfToken;
        $this->shopRemoveMultiFormCsrfToken = $shopRemoveMultiFormCsrfToken;

        return $this;
    }

    public function form(bool $validForm, string $shopCreateFormActionUrl, string $shopModifyFormActionUrlPlaceholder, string $shopRemoveFormActionUrl): self
    {
        $this->builder->setMethodStatus('formValid', true);

        $this->validForm = $validForm;
        $this->shopCreateFormActionUrl = mb_strtolower($shopCreateFormActionUrl);
        $this->shopModifyFormActionUrlPlaceholder = mb_strtolower($shopModifyFormActionUrlPlaceholder);
        $this->shopRemoveFormActionUrl = mb_strtolower($shopRemoveFormActionUrl);

        return $this;
    }

    public function group(string $groupNameUrlEncoded): self
    {
        $this->builder->setMethodStatus('group', true);

        $this->groupNameUrlEncoded = $groupNameUrlEncoded;

        return $this;
    }

    public function build(): self
    {
        $this->builder->validate();

        return $this;
    }
}
