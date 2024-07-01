<?php

declare(strict_types=1);

namespace App\Twig\Components\GroupUsers\GroupUsersHome\Home;

use App\Twig\Components\HomeSection\Home\HomeSectionComponentDto;
use App\Twig\Components\Modal\ModalComponentDto;
use App\Twig\Components\TwigComponentDtoInterface;
use Common\Domain\DtoBuilder\DtoBuilder;
use Common\Domain\DtoBuilder\DtoBuilderInterface;

class GroupUsersHomeSectionComponentDto implements TwigComponentDtoInterface, DtoBuilderInterface
{
    private DtoBuilder $builder;

    public readonly HomeSectionComponentDto $homeSectionComponentDto;
    public readonly ModalComponentDto $groupUsersInfoModalDto;

    public readonly string $groupId;

    public function __construct()
    {
        $this->builder = new DtoBuilder([
            'groupData',
            'homeSection',
            'groupUsersInfoModal',
        ]);
    }

    public function groupData(string $groupId): self
    {
        $this->builder->setMethodStatus('groupData', true);

        $this->groupId = $groupId;

        return $this;
    }

    public function homeSection(HomeSectionComponentDto $homeSectionComponentDto): self
    {
        $this->builder->setMethodStatus('homeSection', true);

        $this->homeSectionComponentDto = $homeSectionComponentDto;

        return $this;
    }

    public function groupUsersInfoModal(ModalComponentDto $groupUsersInfoModalDto): self
    {
        $this->builder->setMethodStatus('groupUsersInfoModal', true);

        $this->groupUsersInfoModalDto = $groupUsersInfoModalDto;

        return $this;
    }

    public function build(): DtoBuilderInterface
    {
        $this->builder->validate();

        return $this;
    }
}
