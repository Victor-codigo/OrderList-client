<?php

declare(strict_types=1);

namespace App\Twig\Components\Controls\FileUpload;

use App\Twig\Components\TwigComponent;
use App\Twig\Components\TwigComponentDtoInterface;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(
    name: 'FileUploadComponent',
    template: 'Components/Controls/FileUpload/FileUploadComponent.html.twig'
)]
class FileUploadComponent extends TwigComponent
{
    public FileUploadComponentDto|TwigComponentDtoInterface $data;
    public string $imageAvatarPath;

    protected static function getComponentName(): string
    {
        return 'FileUploadComponent';
    }

    public function mount(FileUploadComponentDto $data): void
    {
        $this->data = $data;
        $this->imageAvatarPath = $this->data->imagePath ?? $this->data->imageNoAvatarPath;
    }
}
