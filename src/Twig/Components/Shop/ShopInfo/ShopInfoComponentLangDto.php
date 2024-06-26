<?php

declare(strict_types=1);

namespace App\Twig\Components\Shop\ShopInfo;

use App\Twig\Components\HomeSection\ItemInfo\ItemInfoComponentLangDto;

class ShopInfoComponentLangDto extends ItemInfoComponentLangDto
{
    public readonly string $address;

    public function __construct()
    {
        parent::__construct();

        $this->builder->addBuilderMethod('address');
    }

    public function address(string $addressText): static
    {
        $this->builder->setMethodStatus('address', true);

        $this->address = $addressText;

        return $this;
    }
}
