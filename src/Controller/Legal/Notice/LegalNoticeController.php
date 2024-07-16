<?php

declare(strict_types=1);

namespace App\Controller\Legal\Notice;

use App\Twig\Components\Common\Legal\Notice\LegalNoticeComponentDto;
use Common\Domain\Config\Config;
use Common\Domain\PageTitle\GetPageTitleService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(
    path: '{_locale}/legal/notice',
    name: 'legal_notice',
    methods: ['GET'],
    requirements: [
        '_locale' => Config::CLIENT_DOMAIN_LOCALE_VALID,
    ]
)]
class LegalNoticeController extends AbstractController
{
    public function __construct(
        private GetPageTitleService $getPageTitleService,
    ) {
    }

    public function __invoke(): Response
    {
        return $this->renderTemplate();
    }

    private function renderTemplate(): Response
    {
        $legalNoticeComponentDto = new LegalNoticeComponentDto(
            Config::CLIENT_DOMAIN,
            Config::ADMIN_EMAIL
        );

        return $this->render('legal/notice/index.html.twig', [
            'legalNoticeComponentDto' => $legalNoticeComponentDto,
            'pageTitle' => $this->getPageTitleService->__invoke('LegalNoticeComponent'),
            'domainName' => Config::CLIENT_DOMAIN_NAME,
        ]);
    }
}
