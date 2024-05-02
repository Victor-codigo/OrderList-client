<?php

declare(strict_types=1);

namespace App\Twig\Components\Group\GroupUserRemove;

use App\Twig\Components\HomeSection\ItemRemove\ItemRemoveComponentDto;

class GroupUserRemoveComponentDto extends ItemRemoveComponentDto
{
    public function __construct(
        public readonly string $componentName,
        public readonly array $errors,
        public readonly string $groupId,
        public readonly string $csrfToken,
        public readonly string $formActionUrl,
        public readonly bool $removeMulti,
    ) {
    }
}
