<?php

declare(strict_types=1);

namespace App\Twig\Components\HomeSection\HomeList\ListItem;

use App\Twig\Components\TwigComponentDtoInterface;

class HomeListItemComponentDto implements TwigComponentDtoInterface
{
    public function __construct(
        public readonly string $componentName,
        public readonly string $id,
        public readonly string $name,
        public readonly string|null $description,
        public readonly string|null $image,
        public readonly \DateTimeImmutable $createdOn,
        public readonly string $modifyFormModalIdAttribute,
        public readonly string $deleteFormModalIdAttribute,

        public readonly string $translationDomainName,
    ) {
    }
}
