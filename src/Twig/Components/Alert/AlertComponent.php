<?php

declare(strict_types=1);

namespace App\Twig\Components\Alert;

use App\Twig\Components\TwigComponent;
use App\Twig\Components\TwigComponentDtoInterface;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(
    name: 'AlertComponent',
    template: 'Components/Alert/AlertComponent.html.twig'
)]
class AlertComponent extends TwigComponent
{
    public AlertComponentDto|TwigComponentDtoInterface $data;

    public string $cssType;

    protected static function getComponentName(): string
    {
        return 'AlertComponent';
    }

    public function mount(AlertComponentDto $data): void
    {
        $this->data = $data;
        $this->setAlertType($this->data->type);
    }

    private function setAlertType(ALERT_TYPE $alertType): void
    {
        $this->cssType = match ($alertType) {
            ALERT_TYPE::DANGER => 'alert-danger',
            ALERT_TYPE::INFO => 'alert-light',
            ALERT_TYPE::SUCCESS => 'alert-success'
        };
    }
}
