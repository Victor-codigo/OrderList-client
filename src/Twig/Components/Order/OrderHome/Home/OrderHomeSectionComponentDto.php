<?php

declare(strict_types=1);

namespace App\Twig\Components\Order\OrderHome\Home;

use App\Twig\Components\HomeSection\Home\HomeSectionComponentDto;
use App\Twig\Components\Modal\ModalComponentDto;
use App\Twig\Components\TwigComponentDtoInterface;
use Common\Domain\DtoBuilder\DtoBuilder;
use Common\Domain\DtoBuilder\DtoBuilderInterface;

class OrderHomeSectionComponentDto implements TwigComponentDtoInterface, DtoBuilderInterface
{
    private DtoBuilder $builder;

    public readonly string $listOrdersId;
    public readonly string $groupId;
    public readonly HomeSectionComponentDto $homeSectionComponentDto;
    public readonly ModalComponentDto $listItemsModalDto;
    public readonly ModalComponentDto $shopCreateModalDto;
    public readonly ModalComponentDto $orderInfoModalDto;

    public function __construct()
    {
        $this->builder = new DtoBuilder([
            'listOrders',
            'homeSection',
            'listItemsModal',
            'orderInfoModal',
        ]);
    }

    public function listOrders(string $listOrdersId, string $groupId): self
    {
        $this->builder->setMethodStatus('listOrders', true);

        $this->listOrdersId = $listOrdersId;
        $this->groupId = $groupId;

        return $this;
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

    public function orderInfoModal(ModalComponentDto $orderInfoModalDto): self
    {
        $this->builder->setMethodStatus('orderInfoModal', true);

        $this->orderInfoModalDto = $orderInfoModalDto;

        return $this;
    }

    public function build(): DtoBuilderInterface
    {
        $this->builder->validate();

        return $this;
    }
}
