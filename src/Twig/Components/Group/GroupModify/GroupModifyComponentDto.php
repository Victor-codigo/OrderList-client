<?php

declare(strict_types=1);

namespace App\Twig\Components\Group\GroupModify;

use App\Twig\Components\TwigComponentDtoInterface;

class GroupModifyComponentDto implements TwigComponentDtoInterface
{
    public function __construct(
        public readonly array $errors,
        public readonly ?string $name,
        public readonly ?string $description,
        public readonly ?string $image,
        public readonly ?string $imageNoAvatar,
        public readonly ?string $groupModifyCsrfToken,
        public readonly string $groupModifyFormActionAttributePlaceholder,
        public readonly string $groupModifyFormIdAttribute,
        public readonly bool $validForm
    ) {
    }
}
