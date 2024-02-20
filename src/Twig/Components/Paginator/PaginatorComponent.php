<?php

declare(strict_types=1);

namespace App\Twig\Components\Paginator;

use App\Twig\Components\TwigComponent;
use App\Twig\Components\TwigComponentDtoInterface;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(
    name: 'PaginatorComponent',
    template: 'Components/Paginator/PaginatorComponent.html.twig'
)]
class PaginatorComponent extends TwigComponent
{
    private const PAGE_RANGE = 2;
    public const URL_PLACEHOLDER = '{pageNum}';

    public PaginatorComponentLangDto $lang;
    public PaginatorComponentDto|TwigComponentDtoInterface $data;

    public readonly array $pageList;
    public readonly string $pagePreviousUrl;
    public readonly string $pageNextUrl;

    protected static function getComponentName(): string
    {
        return 'PaginatorComponent';
    }

    public function mount(PaginatorComponentDto $data): void
    {
        $this->data = $data;

        $this->loadTranslation();
        $this->pagePreviousUrl = $this->getPageUrl($this->data->pageCurrent - 1, $this->data->pageUrl);
        $this->pageNextUrl = $this->getPageUrl($this->data->pageCurrent + 1, $this->data->pageUrl);
        $this->pageList = $this->getPages($this->data->pageCurrent, $this->data->pagesTotal, $this->data->pageUrl);
    }

    private function loadTranslation(): void
    {
        $this->lang = new PaginatorComponentLangDto(
            $this->translate('page.previous'),
            $this->translate('page.next')
        );
    }

    private function getPages(int $pageCurrent, int $pagesTotal, string $pageUrl): array
    {
        $pageCurrent = $pageCurrent > $pagesTotal ? $pagesTotal : $pageCurrent;
        $pageMin = $pageCurrent - self::PAGE_RANGE < 1 ? 1 : $pageCurrent - self::PAGE_RANGE;
        $pageMax = $pageCurrent + self::PAGE_RANGE > $pagesTotal ? $pagesTotal : $pageCurrent + self::PAGE_RANGE;
        $pages = [];

        if ($pageMin > 1) {
            $pages[] = [
                'url' => $this->getPageUrl(1, $pageUrl),
                'text' => '1',
                'active' => 1 === $pageCurrent,
            ];
        }

        if ($pageMin > 2) {
            $pages[] = [
                'url' => null,
                'text' => '...',
                'active' => false,
            ];
        }

        for ($i = $pageMin; $i <= $pageMax; ++$i) {
            $pages[] = [
                'url' => $this->getPageUrl($i, $pageUrl),
                'text' => $i,
                'active' => $i === $pageCurrent,
            ];
        }

        if ($pageMax < $pagesTotal - 1) {
            $pages[] = [
                'url' => null,
                'text' => '...',
                'active' => false,
            ];
        }

        if ($pageMax < $pagesTotal) {
            $pages[] = [
                'url' => $this->getPageUrl($pagesTotal, $pageUrl),
                'text' => $pagesTotal,
                'active' => $pageCurrent === $pagesTotal,
            ];
        }

        return $pages;
    }

    private function getPageUrl(int $page, string $pageUrl): string
    {
        return str_replace(self::URL_PLACEHOLDER, (string) $page, $pageUrl);
    }
}
