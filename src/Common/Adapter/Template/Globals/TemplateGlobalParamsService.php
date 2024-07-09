<?php

declare(strict_types=1);

namespace Common\Adapter\Template\Globals;

use App\Twig\Components\Common\Footer\FooterComponentDto;
use Common\Domain\Config\Config;

class TemplateGlobalParamsService
{
    public readonly FooterComponentDto $footerComponentDto;

    public function __construct()
    {
        $this->footerComponentDto = $this->createFooterComponentDto();
    }

    private function createFooterComponentDto(): FooterComponentDto
    {
        return new FooterComponentDto(
            Config::CLIENT_DOMAIN_NAME
        );
    }
}
