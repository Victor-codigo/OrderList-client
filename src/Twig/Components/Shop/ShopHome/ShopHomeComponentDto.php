<?php

declare(strict_types=1);

namespace App\Twig\Components\Shop\ShopHome;

use App\Twig\Components\TwigComponentDtoInterface;

class ShopHomeComponentDto implements TwigComponentDtoInterface
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

    private array $builder = [
        'errors' => false,
        'pagination' => false,
        'shops' => false,
        'formCsrfToken' => false,
        'formValid' => false,
        'group' => false,
    ];

    public function errors(array $shopHomeValidationOk, array $shopValidationErrorsMessage): self
    {
        $this->builder['errors'] = true;

        $this->shopHomeMessageValidationOk = $shopHomeValidationOk;
        $this->shopErrorsMessage = $shopValidationErrorsMessage;

        return $this;
    }

    public function pagination(int $page, int $pageItems, int $pagesTotal): self
    {
        $this->builder['pagination'] = true;

        $this->page = $page;
        $this->pageItems = $pageItems;
        $this->pagesTotal = $pagesTotal;

        return $this;
    }

    public function shops(array $shopsData, string $shopNoImagePath): self
    {
        $this->builder['shops'] = true;

        $this->shopsData = $shopsData;
        $this->shopNoImagePath = $shopNoImagePath;

        return $this;
    }

    public function formCsrfToken(string $shopCreateFormCsrfToken, string $shopModifyFormCsrfToken, string $shopRemoveFormCsrfToken, string $shopRemoveMultiFormCsrfToken): self
    {
        $this->builder['formCsrfToken'] = true;

        $this->shopCreateFormCsrfToken = $shopCreateFormCsrfToken;
        $this->shopModifyCsrfToken = $shopModifyFormCsrfToken;
        $this->shopRemoveFormCsrfToken = $shopRemoveFormCsrfToken;
        $this->shopRemoveMultiFormCsrfToken = $shopRemoveMultiFormCsrfToken;

        return $this;
    }

    public function form(bool $validForm, string $shopCreateFormActionUrl, string $shopModifyFormActionUrlPlaceholder, string $shopRemoveFormActionUrl): self
    {
        $this->builder['formValid'] = true;

        $this->validForm = $validForm;
        $this->shopCreateFormActionUrl = mb_strtolower($shopCreateFormActionUrl);
        $this->shopModifyFormActionUrlPlaceholder = mb_strtolower($shopModifyFormActionUrlPlaceholder);
        $this->shopRemoveFormActionUrl = mb_strtolower($shopRemoveFormActionUrl);

        return $this;
    }

    public function group(string $groupNameUrlEncoded): self
    {
        $this->builder['group'] = true;

        $this->groupNameUrlEncoded = $groupNameUrlEncoded;

        return $this;
    }

    public function build(): self
    {
        if (count(array_filter($this->builder)) < count($this->builder)) {
            $methodsMandatory = implode(', ', array_keys($this->builder));
            throw new \InvalidArgumentException("Constructors: {$methodsMandatory}. Are mandatory");
        }

        return $this;
    }
}
