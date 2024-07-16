<?php

declare(strict_types=1);

namespace App\Twig\Components\HomeSection\ItemRemove;

use App\Twig\Components\AlertValidation\AlertValidationComponentDto;
use App\Twig\Components\Controls\Title\TITLE_TYPE;
use App\Twig\Components\Controls\Title\TitleComponentDto;
use App\Twig\Components\TwigComponent;
use App\Twig\Components\TwigComponentDtoInterface;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(
    name: 'ItemRemoveComponent',
    template: 'Components/HomeSection/ItemRemove/ItemRemoveComponent.html.twig'
)]
abstract class ItemRemoveComponent extends TwigComponent
{
    abstract protected function loadTranslation(): void;

    abstract public function loadErrorsTranslation(array $errors): array;

    abstract public function loadValidationOkTranslation(): string;

    abstract public function mount(ItemRemoveComponentDto $data): void;

    abstract public static function getComponentName(): string;

    public ItemRemoveComponentLangDto $lang;
    public ItemRemoveComponentDto|TwigComponentDtoInterface $data;
    public TitleComponentDto $titleDto;

    public readonly string $componentName;
    public readonly string $formName;
    public readonly string $submitFieldName;
    public readonly string $itemsIdFieldName;
    public readonly string $tokenCsrfFieldName;

    protected function initialize(string $componentName, string $formName, string $submitFieldName, string $itemsIdFieldName, string $tokenCsrfFieldName): void
    {
        $this->componentName = $componentName;
        $this->formName = $formName;
        $this->submitFieldName = $submitFieldName;
        $this->itemsIdFieldName = $itemsIdFieldName;
        $this->tokenCsrfFieldName = $tokenCsrfFieldName;
    }

    protected function createTitleDto(): TitleComponentDto
    {
        return new TitleComponentDto($this->lang->title, TITLE_TYPE::POP_UP,null);
    }

    protected function createAlertValidationComponentDto(): AlertValidationComponentDto
    {
        return new AlertValidationComponentDto(
            [$this->loadValidationOkTranslation()],
            $this->loadErrorsTranslation($this->data->errors)
        );
    }
}
