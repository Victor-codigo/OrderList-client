<?php

declare(strict_types=1);

namespace App\Twig\Components\GroupUsers\GroupUsersInfo;

use App\Twig\Components\HomeSection\ItemInfo\ItemInfoComponentLangDto;

class GroupUserInfoComponentLangDto extends ItemInfoComponentLangDto
{
    public readonly string $adminLabel;

    public function __construct()
    {
        parent::__construct();

        $this->builder->addBuilderMethod('admin');
    }

    public function admin(string $adminLabel): static
    {
        $this->builder->setMethodStatus('admin', true);

        $this->adminLabel = $adminLabel;

        return $this;
    }
}
