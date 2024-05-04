<?php

declare(strict_types=1);

namespace App\Twig\Components\Group\GroupUsersHome\ListItem;

use App\Twig\Components\HomeSection\HomeList\ListItem\HomeListItemComponentDto;

class GroupUsersListItemComponentDto extends HomeListItemComponentDto
{
    /**
     * @param array<{
     *      id: string,
     *      name: string,
     *      description: string,
     *      image: string,
     *      price; float|null
     * }> $shops
     */
    public function __construct(
        public readonly string $componentName,
        public readonly string $id,
        public readonly string $name,
        public readonly string $deleteFormModalIdAttribute,
        public readonly string $groupUsersInfoModalIdAttribute,
        public readonly string $translationDomainName,

        public readonly ?string $image,
        public readonly bool $noImage,
        public readonly bool $admin,
        public readonly bool $userSessionAdmin,
    ) {
    }
}
