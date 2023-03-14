<?php

declare(strict_types=1);

namespace App\Twig\Components\Group\GroupModify;

use App\Twig\Components\TwigComponentDtoInterface;

class GroupModifyComponentDto implements TwigComponentDtoInterface
{
    public function __construct(
        public readonly array $errors,
        public readonly string|null $groupId,
        public readonly string|null $name,
        public readonly string|null $description,
        public readonly string|null $image,
        public readonly string|null $imageNoAvatar,
        public readonly string|null $groupModifyCsrfToken,
        public readonly string|null $groupRemoveCsrfToken,
        public readonly bool $validForm
    ) {
    }
}
