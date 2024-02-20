<?php

declare(strict_types=1);

namespace App\Twig\Components\HomeSection\SearchBar;

use App\Form\SearchBar\SEARCHBAR_FORM_FIELDS;
use App\Twig\Components\TwigComponent;
use App\Twig\Components\TwigComponentDtoInterface;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(
    name: 'SearchBarComponent',
    template: 'Components/HomeSection/SearchBar/SearchBarComponent.html.twig'
)]
class SearchBarComponent extends TwigComponent
{
    public SearchBarComponentLangDto $lang;
    public SearchBarComponentDto|TwigComponentDtoInterface $data;

    public readonly string $formName;
    public readonly string $searchTokenCsrfFieldName;

    public readonly string $searchValueFieldName;
    public readonly string $nameFilterFieldName;
    public readonly string $sectionFilterFieldName;
    public readonly string $searchButtonFiledName;

    protected static function getComponentName(): string
    {
        return 'SearchBarComponent';
    }

    public function mount(SearchBarComponentDto $data): void
    {
        $this->data = $data;
        $this->formName = SEARCHBAR_FORM_FIELDS::FORM;
        $this->searchTokenCsrfFieldName = sprintf('%s[%s]', SEARCHBAR_FORM_FIELDS::FORM, SEARCHBAR_FORM_FIELDS::TOKEN);
        $this->searchValueFieldName = sprintf('%s[%s]', SEARCHBAR_FORM_FIELDS::FORM, SEARCHBAR_FORM_FIELDS::SEARCH_VALUE);
        $this->nameFilterFieldName = sprintf('%s[%s]', SEARCHBAR_FORM_FIELDS::FORM, SEARCHBAR_FORM_FIELDS::NAME_FILTER);
        $this->sectionFilterFieldName = sprintf('%s[%s]', SEARCHBAR_FORM_FIELDS::FORM, SEARCHBAR_FORM_FIELDS::SECTION_FILTER);
        $this->searchButtonFiledName = sprintf('%s[%s]', SEARCHBAR_FORM_FIELDS::FORM, SEARCHBAR_FORM_FIELDS::BUTTON);

        $this->loadTranslation();
    }

    private function loadTranslation(): void
    {
        $this->lang = (new SearchBarComponentLangDto())
            ->input(
                $this->translate('inputSearch.label'),
                $this->translate('inputSearch.placeholder'),
                $this->translate('button.label'),
            )
            ->nameFilters([
                NAME_FILTERS::STARTS_WITH->value => $this->translate('name_filters.startsWith'),
                NAME_FILTERS::ENDS_WITH->value => $this->translate('name_filters.endsWith'),
                NAME_FILTERS::CONTAINS->value => $this->translate('name_filters.contains'),
                NAME_FILTERS::EQUALS->value => $this->translate('name_filters.equals'),
            ])
            ->sectionFilters([
                SECTION_FILTERS::ORDER->value => $this->translate('section_filters.order'),
                SECTION_FILTERS::PRODUCT->value => $this->translate('section_filters.product'),
                SECTION_FILTERS::SHOP->value => $this->translate('section_filters.shop'),
            ])
            ->build();
    }

    public function sectionFilters(): array
    {
        $sectionFiltersKeys = array_keys($this->lang->sectionFilters);
        $sectionFilters = array_map(
            fn (string $sectionFilter, string $key) => in_array(SECTION_FILTERS::tryFrom($key), $this->data->sectionFilters) ? $sectionFilter : null,
            array_values($this->lang->sectionFilters), $sectionFiltersKeys
        );

        $sectionFilters = array_combine($sectionFiltersKeys, $sectionFilters);

        return array_filter($sectionFilters);
    }
}
