<?php

declare(strict_types=1);

namespace App\Twig\Components\Group\GroupUsersList\List;

use App\Twig\Components\Group\GroupUsersList\ListItem\GroupUsersListItemComponentDto;
use App\Twig\Components\Modal\ModalComponentDto;
use App\Twig\Components\Paginator\PaginatorComponentDto;
use App\Twig\Components\TwigComponentDtoInterface;

class GroupUsersListComponentDto implements TwigComponentDtoInterface
{
    /**
     * @param GroupUsersListItemComponentDto[] $groupList
     */
    public function __construct(
        public readonly array $errors,
        public readonly array $groupUsersList,
        public readonly string $groupName,
        public readonly PaginatorComponentDto $paginatorDto,
        public readonly ModalComponentDto $groupUserRemoveModalDto,
        public readonly bool $validForm,
    ) {
    }
}
