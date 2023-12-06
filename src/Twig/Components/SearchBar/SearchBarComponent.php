<?php

declare(strict_types=1);

namespace App\Twig\Components\SearchBar;

use App\Form\SearchBar\SEARCHBAR_FORM_FIELDS;
use App\Twig\Components\TwigComponent;
use App\Twig\Components\TwigComponentDtoInterface;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(
    name: 'SearchBarComponent',
    template: 'Components/SearchBar/SearchBarComponent.html.twig'
)]
class SearchBarComponent extends TwigComponent
{
    public SearchBarComponentLangDto $lang;
    public SearchBarComponentDto|TwigComponentDtoInterface $data;

    public readonly string $formName;
    public readonly string $searchFieldName;
    public readonly string $searchFilterFieldName;

    protected static function getComponentName(): string
    {
        return 'SearchBarComponent';
    }

    public function mount(SearchBarComponentDto $data): void
    {
        $this->data = $data;
        $this->formName = SEARCHBAR_FORM_FIELDS::FORM;
        $this->searchFieldName = sprintf('%s[%s]', SEARCHBAR_FORM_FIELDS::FORM, SEARCHBAR_FORM_FIELDS::SEARCH_VALUE);
        $this->searchFilterFieldName = sprintf('%s[%s]', SEARCHBAR_FORM_FIELDS::FORM, SEARCHBAR_FORM_FIELDS::SEARCH_FILTER);

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
            ->filters([
                FILTERS::STARTS_WITH->value => $this->translate('filters.startsWith'),
                FILTERS::ENDS_WITH->value => $this->translate('filters.endsWith'),
                FILTERS::CONTAINS->value => $this->translate('filters.contains'),
                FILTERS::EQUALS->value => $this->translate('filters.equals'),
            ])
            ->build();
    }
}
