<?php

declare(strict_types=1);

namespace App\Twig\Components\ListOrders\ListOrdersHome\Home;

use App\Twig\Components\HomeSection\Home\HomeSectionComponentDto;
use App\Twig\Components\Modal\ModalComponentDto;
use App\Twig\Components\TwigComponentDtoInterface;
use Common\Domain\DtoBuilder\DtoBuilder;
use Common\Domain\DtoBuilder\DtoBuilderInterface;

class ListOrdersHomeSectionComponentDto implements TwigComponentDtoInterface, DtoBuilderInterface
{
    private DtoBuilder $builder;

    public readonly HomeSectionComponentDto $homeSectionComponentDto;
    public readonly ModalComponentDto $listItemsModalDto;
    public readonly ModalComponentDto $productCreateModalDto;
    public readonly ModalComponentDto $listOrdersInfoModalDto;

    public function __construct()
    {
        $this->builder = new DtoBuilder([
            'homeSection',
            'listOrdersInfoModal',
        ]);
    }

    public function homeSection(HomeSectionComponentDto $homeSectionComponentDto): self
    {
        $this->builder->setMethodStatus('homeSection', true);

        $this->homeSectionComponentDto = $homeSectionComponentDto;

        return $this;
    }

    public function listOrdersInfoModal(ModalComponentDto $listOrdersInfoModalDto): self
    {
        $this->builder->setMethodStatus('listOrdersInfoModal', true);

        $this->listOrdersInfoModalDto = $listOrdersInfoModalDto;

        return $this;
    }

    public function build(): DtoBuilderInterface
    {
        $this->builder->validate();

        return $this;
    }
}
