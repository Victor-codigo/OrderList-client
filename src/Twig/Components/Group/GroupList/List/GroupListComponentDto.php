<?php

declare(strict_types=1);

namespace App\Twig\Components\Group\GroupList\List;

use App\Twig\Components\Group\GroupList\ListItem\GroupListItemComponentDto;
use App\Twig\Components\Modal\ModalComponentDto;
use App\Twig\Components\Paginator\PaginatorComponentDto;
use App\Twig\Components\TwigComponentDtoInterface;

class GroupListComponentDto implements TwigComponentDtoInterface
{
    /**
     * @param GroupListItemComponentDto[] $groupList
     */
    public function __construct(
        public readonly array $errors,
        public readonly array $groupList,
        public readonly PaginatorComponentDto $paginatorDto,
        public readonly ModalComponentDto $groupRemoveModalDto,
        public readonly bool $validForm,
    ) {
    }
}
