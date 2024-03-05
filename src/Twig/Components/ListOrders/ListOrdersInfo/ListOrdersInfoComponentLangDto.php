<?php

declare(strict_types=1);

namespace App\Twig\Components\ListOrders\ListOrdersInfo;

use App\Twig\Components\HomeSection\ItemInfo\ItemInfoComponentLangDto;

class ListOrdersInfoComponentLangDto extends ItemInfoComponentLangDto
{
    public readonly string $dateToBuy;

    public function __construct()
    {
        parent::__construct();

        $this->builder->addBuilderMethod('dateToBuy');
        $this->builder->addBuilderMethod('description');
    }

    public function dateToBuy(string $dateToBuyText): self
    {
        $this->builder->setMethodStatus('dateToBuy', true);

        $this->dateToBuy = $dateToBuyText;

        return $this;
    }
}
