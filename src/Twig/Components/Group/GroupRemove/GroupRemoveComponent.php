<?php

namespace App\Twig\Components\Group\GroupRemove;

use App\Form\Group\GroupRemove\GROUP_REMOVE_FORM_FIELDS;
use App\Twig\Components\TwigComponent;
use App\Twig\Components\TwigComponentDtoInterface;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(
    name: 'GroupRemoveComponent',
    template: 'Components/Group/GroupRemove/GroupRemoveComponent.html.twig'
)]
final class GroupRemoveComponent extends TwigComponent
{
    public GroupRemoveComponentDtoLang $lang;
    public GroupRemoveComponentDto|TwigComponentDtoInterface $data;

    public readonly string $formName;
    public readonly string $tokenCsrfFiledName;
    public readonly string $groupIdFieldName;
    public readonly string $submitFieldName;

    public static function getComponentName(): string
    {
        return 'GroupRemoveComponent';
    }

    public function mount(GroupRemoveComponentDto $data): void
    {
        $this->data = $data;

        $this->formName = GROUP_REMOVE_FORM_FIELDS::FORM;
        $this->tokenCsrfFiledName = sprintf('%s[%s]', GROUP_REMOVE_FORM_FIELDS::FORM, GROUP_REMOVE_FORM_FIELDS::TOKEN);
        $this->groupIdFieldName = sprintf('%s[%s]', GROUP_REMOVE_FORM_FIELDS::FORM, GROUP_REMOVE_FORM_FIELDS::GROUP_ID);
        $this->submitFieldName = sprintf('%s[%s]', GROUP_REMOVE_FORM_FIELDS::FORM, GROUP_REMOVE_FORM_FIELDS::SUBMIT);

        $this->loadTranslation();
    }

    private function loadTranslation(): void
    {
        $this->lang = new GroupRemoveComponentDtoLang(
            $this->translate('title'),
            $this->translate('message_advice.text'),
            $this->translate('remove_group_button.label'),
            null
        );
    }
}
