<?php

declare(strict_types=1);

namespace App\Twig\Components\Shop\ShopHome\Home;

use App\Twig\Components\HomeSection\Home\HomeSectionComponentDto;
use App\Twig\Components\Modal\ModalComponentDto;
use App\Twig\Components\TwigComponentDtoInterface;
use Common\Domain\DtoBuilder\DtoBuilder;
use Common\Domain\DtoBuilder\DtoBuilderInterface;

class ShopHomeSectionComponentDto implements TwigComponentDtoInterface, DtoBuilderInterface
{
    private DtoBuilder $builder;

    public readonly HomeSectionComponentDto $homeSectionComponentDto;
    public readonly ModalComponentDto $listItemsModalDto;
    public readonly ModalComponentDto $productCreateModalDto;
    public readonly ModalComponentDto $shopInfoModalDto;

    public function __construct()
    {
        $this->builder = new DtoBuilder([
            'homeSection',
            'listItemsModal',
            'productCreateModal',
            'shopInfoModal',
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

    public function productCreateModal(ModalComponentDto $productCreateModalDto): self
    {
        $this->builder->setMethodStatus('productCreateModal', true);

        $this->productCreateModalDto = $productCreateModalDto;

        return $this;
    }

    public function shopInfoModal(ModalComponentDto $shopInfoModalDto): self
    {
        $this->builder->setMethodStatus('shopInfoModal', true);

        $this->shopInfoModalDto = $shopInfoModalDto;

        return $this;
    }

    public function build(): DtoBuilderInterface
    {
        $this->builder->validate();

        return $this;
    }
}
