<?php

declare(strict_types=1);

namespace App\Twig\Components\HomeSection\HomeList\List;

use App\Twig\Components\Modal\ModalComponentDto;
use App\Twig\Components\Paginator\PaginatorComponentDto;
use App\Twig\Components\TwigComponentDtoInterface;

class HomeListComponentDto implements TwigComponentDtoInterface
{
    /**
     * @param HomeListItemComponentDto[] $listItems
     */
    public function __construct(
        public readonly array $errors,
        public readonly array $listItems,
        public readonly PaginatorComponentDto $homeListPaginatorDto,
        public readonly bool $validForm,
        public readonly ModalComponentDto $homeListItemRemoveFormModalDto,
        public readonly ModalComponentDto $homeListItemModifyFormModalDto,

        public readonly string $translationDomainName,
    ) {
    }
}
