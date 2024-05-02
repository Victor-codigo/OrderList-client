<?php

namespace App\Twig\Components\Group\GroupUserRemove;

use App\Form\Group\GroupUserRemoveMulti\GROUP_USER_REMOVE_MULTI_FORM_ERRORS;
use App\Form\Group\GroupUserRemoveMulti\GROUP_USER_REMOVE_MULTI_FORM_FIELDS;
use App\Form\Group\GroupUserRemove\GROUP_USER_REMOVE_FORM_FIELDS;
use App\Twig\Components\HomeSection\ItemRemove\ItemRemoveComponent;
use App\Twig\Components\HomeSection\ItemRemove\ItemRemoveComponentDto;
use App\Twig\Components\TwigComponentDtoInterface;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(
    name: 'GroupUserRemoveComponent',
    template: 'Components/Group/GroupUserRemove/GroupUserRemoveComponent.html.twig'
)]
final class GroupUserRemoveComponent extends ItemRemoveComponent
{
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

    public function mount(ItemRemoveComponentDto $data): void
    {
        $this->data = $data;

        [$formName, $submitFieldName, $itemsIdFieldName, $tokenCsrfFieldName, $this->groupIdFieldName] = $this->data->removeMulti
            ? $this->createRemoveMultiData()
            : $this->createRemoveData();

        $this->initialize(self::getComponentName(), $formName, $submitFieldName, $itemsIdFieldName, $tokenCsrfFieldName);
        $this->loadTranslation();
        $this->titleDto = $this->createTitleDto();
    }

    private function createRemoveMultiData(): array
    {
        return [
            GROUP_USER_REMOVE_MULTI_FORM_FIELDS::FORM,
            sprintf('%s[%s]', GROUP_USER_REMOVE_MULTI_FORM_FIELDS::FORM, GROUP_USER_REMOVE_MULTI_FORM_FIELDS::SUBMIT),
            sprintf('%s[%s]', GROUP_USER_REMOVE_MULTI_FORM_FIELDS::FORM, GROUP_USER_REMOVE_MULTI_FORM_FIELDS::USERS_ID),
            sprintf('%s[%s]', GROUP_USER_REMOVE_MULTI_FORM_FIELDS::FORM, GROUP_USER_REMOVE_MULTI_FORM_FIELDS::TOKEN),
            sprintf('%s[%s]', GROUP_USER_REMOVE_MULTI_FORM_FIELDS::FORM, GROUP_USER_REMOVE_MULTI_FORM_FIELDS::GROUP_ID),
        ];
    }

    private function createRemoveData(): array
    {
        return [
            GROUP_USER_REMOVE_FORM_FIELDS::FORM,
            sprintf('%s[%s]', GROUP_USER_REMOVE_FORM_FIELDS::FORM, GROUP_USER_REMOVE_FORM_FIELDS::SUBMIT),
            sprintf('%s[%s]', GROUP_USER_REMOVE_FORM_FIELDS::FORM, GROUP_USER_REMOVE_FORM_FIELDS::USERS_ID),
            sprintf('%s[%s]', GROUP_USER_REMOVE_FORM_FIELDS::FORM, GROUP_USER_REMOVE_FORM_FIELDS::TOKEN),
            sprintf('%s[%s]', GROUP_USER_REMOVE_FORM_FIELDS::FORM, GROUP_USER_REMOVE_FORM_FIELDS::GROUP_ID),
        ];
    }

    protected function loadTranslation(): void
    {
        $this->lang = (new GroupUserRemoveComponentDtoLang())
            ->title(
                $this->translate('title'),
            )
            ->itemRemoveButton(
                $this->translate('remove_group_user_button.label'),
            )
            ->messageAdvice(
                $this->translate('message_advice.text'),
            )
            ->validationErrors(
                $this->createAlertValidationComponentDto()
            )
            ->build();
    }

    /**
     * @param string[] $errors
     *
     * @return string[]
     */
    public function loadErrorsTranslation(array $errors): array
    {
        $errorsLang = [];
        foreach ($errors as $field => $error) {
            $errorsLang[] = match ($field) {
                GROUP_USER_REMOVE_MULTI_FORM_ERRORS::GROUP_WITHOUT_ADMINS->value => $this->translate('validation.error.group_without_admin'),
                GROUP_USER_REMOVE_MULTI_FORM_ERRORS::GROUP_EMPTY->value => $this->translate('validation.error.group_empty'),
                GROUP_USER_REMOVE_MULTI_FORM_ERRORS::GROUP_ID->value,
                GROUP_USER_REMOVE_MULTI_FORM_ERRORS::GROUP_USERS_NOT_FOUND->value,
                GROUP_USER_REMOVE_MULTI_FORM_ERRORS::PERMISSIONS->value,
                GROUP_USER_REMOVE_MULTI_FORM_ERRORS::INTERNAL_SERVER->value => $this->translate('validation.error.internal_server'),
                default => $this->translate('validation.error.internal_server')
            };
        }

        return $errorsLang;
    }

    public function loadValidationOkTranslation(): string
    {
        return $this->translate('validation.ok');
    }
}
