<?php

declare(strict_types=1);

namespace Common\Domain\PageTitle;

use Common\Domain\Config\Config;
use Symfony\Contracts\Translation\TranslatorInterface;

class GetPageTitleService
{
    public function __construct(
        private TranslatorInterface $translator
    ) {
    }

    public function __invoke(string $componentName): string
    {
        $pageTitle = $this->translator->trans('title', [], $componentName);

        return Config::CLIENT_DOMAIN_NAME." | {$pageTitle}";
    }

    public function setTitleWithDomainName(string $title): string
    {
        return Config::CLIENT_DOMAIN_NAME." | {$title}";
    }
}
