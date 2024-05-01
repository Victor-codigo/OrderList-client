<?php

declare(strict_types=1);

namespace App\Twig\Components\Group\GroupUserAdd;

use App\Twig\Components\AlertValidation\AlertValidationComponentDto;

class GroupUserAddComponentDtoLang
{
    public function __construct(
        public readonly string $title,
        // --
        public readonly string $nameLabel,
        public readonly string $namePlaceholder,
        public readonly string $nameMsgInvalid,
        // --
        public readonly string $groupUserAddButton,
        // --
        public readonly ?AlertValidationComponentDto $validationErrors
    ) {
    }
}
