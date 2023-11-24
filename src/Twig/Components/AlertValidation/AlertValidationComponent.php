<?php

declare(strict_types=1);

namespace App\Twig\Components\AlertValidation;

use App\Twig\Components\Alert\ALERT_TYPE;
use App\Twig\Components\Alert\AlertComponentDto;
use App\Twig\Components\TwigComponent;
use App\Twig\Components\TwigComponentDtoInterface;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(
    name: 'AlertValidationComponent',
    template: 'Components/AlertValidation/AlertValidationComponent.html.twig'
)]
class AlertValidationComponent extends TwigComponent
{
    public AlertValidationComponentDto|TwigComponentDtoInterface $data;

    public string $cssType;
    public string $cssTextColor;
    public readonly AlertComponentDto $alertDto;

    protected static function getComponentName(): string
    {
        return 'AlertValidationComponent';
    }

    public function mount(AlertValidationComponentDto $data): void
    {
        $this->data = $data;

        $this->alertDto = $this->createAlertValidation($data->messageValidationOk, $data->messageErrors);
    }

    private function createAlertValidation(array $messageValidationOk, array $messageErrors): AlertComponentDto
    {
        $validationOk = empty($messageErrors) ? true : false;

        return $this->createAlertComponentDto(
            $validationOk ? ALERT_TYPE::SUCCESS : ALERT_TYPE::DANGER,
            '',
            '',
            $validationOk ? array_unique($messageValidationOk) : array_unique($messageErrors)
        );
    }

    private function createAlertComponentDto(ALERT_TYPE $alertType, string $title, string $subtitle, array|string $messages): AlertComponentDto
    {
        return new AlertComponentDto(
            $alertType,
            $title,
            $subtitle,
            $messages,
            true
        );
    }
}
