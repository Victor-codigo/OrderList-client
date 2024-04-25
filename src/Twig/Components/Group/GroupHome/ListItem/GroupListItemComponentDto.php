<?php

declare(strict_types=1);

namespace App\Twig\Components\Group\GroupHome\ListItem;

use App\Twig\Components\HomeSection\HomeList\ListItem\HomeListItemComponentDto;

class GroupListItemComponentDto extends HomeListItemComponentDto
{
    public function __construct(
        public readonly string $componentName,
        public readonly string $id,
        public readonly string $name,
        public readonly string $modifyFormModalIdAttribute,
        public readonly string $deleteFormModalIdAttribute,
        public readonly string $groupInfoModalIdAttribute,
        public readonly string $translationDomainName,

        public readonly ?string $description,
        public readonly ?string $image,
        public readonly bool $noImage,
        public readonly \DateTimeImmutable $createdOn,
    ) {
    }
}
