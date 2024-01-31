<?php

declare(strict_types=1);

namespace App\Twig\Components\Product\ProductHome\Home;

use App\Twig\Components\HomeSection\Home\HomeSectionComponentDto;
use App\Twig\Components\Modal\ModalComponentDto;
use App\Twig\Components\TwigComponentDtoInterface;
use Common\Domain\DtoBuilder\DtoBuilder;
use Common\Domain\DtoBuilder\DtoBuilderInterface;

class ProductHomeSectionComponentDto implements TwigComponentDtoInterface, DtoBuilderInterface
{
    private DtoBuilder $builder;

    public readonly HomeSectionComponentDto $homeSectionComponentDto;
    public readonly ModalComponentDto $listItemsModalDto;
    public readonly ModalComponentDto $shopCreateModalDto;

    public function __construct()
    {
        $this->builder = new DtoBuilder([
            'homeSection',
            'listItemsModal',
            'shopCreateModal',
        ]);
    }

    public function homeSection(HomeSectionComponentDto $homeSectionComponentDto): self
    {
        $this->builder->setMethodStatus('homeSection', true);

        $this->homeSectionComponentDto = $homeSectionComponentDto;

        return $this;
    }

    public function listItemsModal(ModalComponentDto $listItemsModalDto): self
    {
        $this->builder->setMethodStatus('listItemsModal', true);

        $this->listItemsModalDto = $listItemsModalDto;

        return $this;
    }

    public function shopCreateModal(ModalComponentDto $shopCreateModalDto): self
    {
        $this->builder->setMethodStatus('shopCreateModal', true);

        $this->shopCreateModalDto = $shopCreateModalDto;

        return $this;
    }

    public function build(): DtoBuilderInterface
    {
        $this->builder->validate();

        return $this;
    }
}
