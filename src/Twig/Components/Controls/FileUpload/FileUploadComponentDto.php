<?php

declare(strict_types=1);

namespace App\Twig\Components\Controls\FileUpload;

use App\Twig\Components\TwigComponentDtoInterface;

class FileUploadComponentDto implements TwigComponentDtoInterface
{
    public function __construct(
        public readonly string $controlName,
        public readonly string $controlDeleteImageName,
        public readonly string $imageNoAvatarPath,
        public readonly string|null $imagePath,
        public readonly string|null $controlId = null,
        public readonly string|null $imageAlt = null,
        public readonly string|null $componentId = null,
        ) {
    }
}
