<?php

declare(strict_types=1);

namespace App\Controller\Legal\Privacy;

use App\Controller\Request\RequestDto;
use App\Twig\Components\Common\Legal\Privacy\PrivacyComponentDto;
use Common\Domain\Config\Config;
use Common\Domain\PageTitle\GetPageTitleService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(
    path: '{_locale}/legal/privacy',
    name: 'privacy_notice',
    methods: ['GET'],
    requirements: [
        '_locale' => Config::CLIENT_DOMAIN_LOCALE_VALID,
    ]
)]
class PrivacyController extends AbstractController
{
    public function __construct(
        private GetPageTitleService $getPageTitleService,
    ) {
    }

    public function __invoke(RequestDto $requestDto): Response
    {
        return $this->renderTemplate($requestDto);
    }

    private function renderTemplate(RequestDto $requestDto): Response
    {
        $privacyComponentDto = new PrivacyComponentDto(
            $requestDto->request->getHost(),
            Config::ADMIN_EMAIL
        );

        return $this->render('legal/privacy/index.html.twig', [
            'privacyComponentDto' => $privacyComponentDto,
            'pageTitle' => $this->getPageTitleService->__invoke('PrivacyComponent'),
            'domainName' => Config::CLIENT_DOMAIN_NAME,
        ]);
    }
}
