<?php

declare(strict_types=1);

namespace App\Twig\Components\Notification\NotificationRemove;

use App\Form\Notification\NotificationRemove\NOTIFICATION_REMOVE_FORM_FIELDS;
use App\Twig\Components\HomeSection\ItemRemove\ItemRemoveComponent;
use App\Twig\Components\HomeSection\ItemRemove\ItemRemoveComponentDto;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(
    name: 'NotificationRemoveComponent',
    template: 'Components/HomeSection/ItemRemove/ItemRemoveComponent.html.twig'
)]
class NotificationRemoveComponent extends ItemRemoveComponent
{
    public static function getComponentName(): string
    {
        return 'NotificationRemoveComponent';
    }

    public function mount(ItemRemoveComponentDto $data): void
    {
        $this->data = $data;

        [$formName, $submitFieldName, $itemsIdFieldName, $tokenCsrfFieldName] = $this->createRemoveData();

        $this->initialize(self::getComponentName(), $formName, $submitFieldName, $itemsIdFieldName, $tokenCsrfFieldName);
        $this->loadTranslation();
        $this->titleDto = $this->createTitleDto();
    }

    private function createRemoveData(): array
    {
        return [
             NOTIFICATION_REMOVE_FORM_FIELDS::FORM,
             sprintf('%s[%s]', NOTIFICATION_REMOVE_FORM_FIELDS::FORM, NOTIFICATION_REMOVE_FORM_FIELDS::SUBMIT),
             sprintf('%s[%s]', NOTIFICATION_REMOVE_FORM_FIELDS::FORM, NOTIFICATION_REMOVE_FORM_FIELDS::NOTIFICATIONS_ID),
             sprintf('%s[%s]', NOTIFICATION_REMOVE_FORM_FIELDS::FORM, NOTIFICATION_REMOVE_FORM_FIELDS::TOKEN),
        ];
    }

    protected function loadTranslation(): void
    {
        $this->setTranslationDomainName($this->getComponentName());
        $this->lang = (new NotificationRemoveComponentLangDto())
            ->title(
                $this->translate('title')
            )
            ->messageAdvice(
                $this->translate($this->data->removeMulti ? 'message_advice.text_multi' : 'message_advice.text')
            )
            ->itemRemoveButton(
                $this->translate('remove_button.label')
            )
            ->validationErrors(
                $this->createAlertValidationComponentDto()
            )
        ->build();
    }

    public function loadErrorsTranslation(array $errors): array
    {
        $errorsLang = [];
        foreach ($errors as $field => $error) {
            $errorsLang[] = match ($field) {
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
