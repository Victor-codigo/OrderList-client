<?php

declare(strict_types=1);

namespace Common\Adapter\Template\Globals;

use App\Twig\Components\Common\Footer\FooterComponentDto;
use Common\Adapter\Template\Globals\Dto\MetaDataDto;
use Common\Domain\Config\Config;
use Symfony\Contracts\Translation\TranslatorInterface;

readonly class TemplateGlobalParamsService
{
    private TranslatorInterface $translator;
    public FooterComponentDto $footerComponentDto;
    public MetaDataDto $metaData;
    public string $domainName;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
        $this->footerComponentDto = $this->createFooterComponentDto();
        $this->metaData = $this->createMetadataDto();
        $this->domainName = Config::CLIENT_DOMAIN_NAME;
    }

    private function createFooterComponentDto(): FooterComponentDto
    {
        return new FooterComponentDto(
            Config::CLIENT_DOMAIN_NAME
        );
    }

    private function createMetaDataDto(): MetaDataDto
    {
        return new MetaDataDto(
            $this->translator->trans('metadata.description', [], 'HomePageComponent')
        );
    }
}
