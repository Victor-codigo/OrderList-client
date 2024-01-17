<?php

declare(strict_types=1);

namespace App\Twig\Components\Controls\ContentLoaderJs;

use App\Twig\Components\TwigComponent;
use App\Twig\Components\TwigComponentDtoInterface;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(
    name: 'ContentLoaderJsComponent',
    template: 'Components/Controls/ContentLoaderJs/ContentLoaderJsComponent.html.twig'
)]
class ContentLoaderJsComponent extends TwigComponent
{
    public ContentLoaderJsComponentDto|TwigComponentDtoInterface $data;

    public readonly string $queryParametersJson;

    protected static function getComponentName(): string
    {
        return 'ContentLoaderJsComponent';
    }

    public function mount(ContentLoaderJsComponentDto $data): void
    {
        $this->data = $data;

        $this->queryParametersJson = json_encode($this->data->queryParameters);
    }
}
