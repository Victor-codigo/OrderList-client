<?php

declare(strict_types=1);

namespace App\Twig\Components\Group\GroupHome\Home;

use App\Twig\Components\HomeSection\Home\HomeSectionComponentDto;
use App\Twig\Components\Modal\ModalComponentDto;
use App\Twig\Components\TwigComponentDtoInterface;
use Common\Domain\DtoBuilder\DtoBuilder;
use Common\Domain\DtoBuilder\DtoBuilderInterface;

class GroupHomeSectionComponentDto implements TwigComponentDtoInterface, DtoBuilderInterface
{
    private DtoBuilder $builder;

    public readonly HomeSectionComponentDto $homeSectionComponentDto;
    public readonly ModalComponentDto $groupInfoModalDto;

    public function __construct()
    {
        $this->builder = new DtoBuilder([
            'homeSection',
            'groupInfoModal',
        ]);
    }

    public function homeSection(HomeSectionComponentDto $homeSectionComponentDto): self
    {
        $this->builder->setMethodStatus('homeSection', true);

        $this->homeSectionComponentDto = $homeSectionComponentDto;

        return $this;
    }

    public function groupUsersInfoModal(ModalComponentDto $productInfoModalDto): self
    {
        $this->builder->setMethodStatus('groupInfoModal', true);

        $this->groupInfoModalDto = $productInfoModalDto;

        return $this;
    }

    public function build(): DtoBuilderInterface
    {
        $this->builder->validate();

        return $this;
    }
}
