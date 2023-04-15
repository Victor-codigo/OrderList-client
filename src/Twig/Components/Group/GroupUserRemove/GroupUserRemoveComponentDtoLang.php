<?php

declare(strict_types=1);

namespace App\Twig\Components\Group\GroupUserRemove;

use App\Twig\Components\Alert\AlertComponentDto;

class GroupUserRemoveComponentDtoLang
{
    public function __construct(
        public readonly string $title,
        public readonly string $messageAdvice,
        public readonly string $userRemoveButton,

        public readonly AlertComponentDto|null $validationErrors
    ) {
    }
}
