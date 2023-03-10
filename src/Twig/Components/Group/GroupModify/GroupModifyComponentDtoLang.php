<?php

declare(strict_types=1);

namespace App\Twig\Components\Group\GroupModify;

use App\Twig\Components\Alert\AlertComponentDto;

class GroupModifyComponentDtoLang
{
    public function __construct(
        public readonly string $title,
        // --
        public readonly string $nameLabel,
        public readonly string $namePlaceholder,
        public readonly string $nameMsgInvalid,
        // --
        public readonly string $descriptionLabel,
        public readonly string $descriptionPlaceholder,
        public readonly string $descriptionMsgInvalid,
        // --
        public readonly string $imageAlt,
        // --
        public readonly string $groupCreateButton,
        // --
        public readonly AlertComponentDto $validationErrors
    ) {
    }
}
