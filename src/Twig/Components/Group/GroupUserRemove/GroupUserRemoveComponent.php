<?php

namespace App\Twig\Components\Group\GroupUserRemove;

use App\Form\Group\GroupUserRemove\GROUP_USER_REMOVE_FORM_FIELDS;
use App\Twig\Components\TwigComponent;
use App\Twig\Components\TwigComponentDtoInterface;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(
    name: 'GroupUserRemoveComponent',
    template: 'Components/Group/GroupUserRemove/GroupUserRemoveComponent.html.twig'
)]
final class GroupUserRemoveComponent extends TwigComponent
{
    public GroupUserRemoveComponentDtoLang $lang;
    public GroupUserRemoveComponentDto|TwigComponentDtoInterface $data;

    public readonly string $formName;
    public readonly string $tokenCsrfFieldName;
    public readonly string $groupIdFieldName;
    public readonly string $userIdFieldName;
    public readonly string $submitFieldName;

    public static function getComponentName(): string
    {
        return 'GroupUserRemoveComponent';
    }

    public function mount(GroupUserRemoveComponentDto $data): void
    {
        $this->data = $data;

        $this->formName = GROUP_USER_REMOVE_FORM_FIELDS::FORM;
        $this->tokenCsrfFieldName = sprintf('%s[%s]', GROUP_USER_REMOVE_FORM_FIELDS::FORM, GROUP_USER_REMOVE_FORM_FIELDS::TOKEN);
        $this->groupIdFieldName = sprintf('%s[%s]', GROUP_USER_REMOVE_FORM_FIELDS::FORM, GROUP_USER_REMOVE_FORM_FIELDS::GROUP_ID);
        $this->userIdFieldName = sprintf('%s[%s]', GROUP_USER_REMOVE_FORM_FIELDS::FORM, GROUP_USER_REMOVE_FORM_FIELDS::USER_ID);
        $this->submitFieldName = sprintf('%s[%s]', GROUP_USER_REMOVE_FORM_FIELDS::FORM, GROUP_USER_REMOVE_FORM_FIELDS::SUBMIT);

        $this->loadTranslation();
    }

    private function loadTranslation(): void
    {
        $this->lang = new GroupUserRemoveComponentDtoLang(
            $this->translate('title'),
            $this->translate('message_advice.text'),
            $this->translate('remove_group_user_button.label'),
            null
        );
    }
}
