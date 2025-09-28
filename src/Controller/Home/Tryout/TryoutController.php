<?php

declare(strict_types=1);

namespace App\Controller\Home\Tryout;

use App\Controller\Request\RequestDto;
use App\Controller\Request\RequestRefererDto;
use App\Form\User\Login\LoginForm;
use App\Twig\Components\Home\Tryout\TryoutComponentDto;
use Common\Adapter\Captcha\Recaptcha3ValidatorAdapter;
use Common\Domain\Config\Config;
use Common\Domain\Ports\Form\FormFactoryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(
    path: '{_locale}/home/try-out',
    name: 'home_tryout',
    methods: ['GET', 'POST'],
    requirements: [
        '_locale' => 'en|es',
    ]
)]
class TryoutController extends AbstractController
{
    public function __construct(
        private FormFactoryInterface $formFactory,
        private Recaptcha3ValidatorAdapter $recaptcha,
    ) {
    }

    public function __invoke(RequestDto $requestDto): Response
    {
        $response = $this->redirectToHome($requestDto->requestReferer);

        if (null !== $response) {
            return $response;
        }

        $loginForm = $this->formFactory->create(new LoginForm($this->recaptcha), $requestDto->request);

        return $this->renderTemplate($requestDto, $loginForm->getCsrfToken());
    }

    private function redirectToHome(?RequestRefererDto $requestRefererDto): ?Response
    {
        if (null === $requestRefererDto) {
            return null;
        }

        if ('home_tryout' !== $requestRefererDto->routeName) {
            return null;
        }

        return $this->redirectToRoute('home');
    }

    private function renderTemplate(RequestDto $requestDto, string $tokenSession): Response
    {
        $tryoutComponentDto = new TryoutComponentDto(
            Config::CLIENT_DOMAIN_NAME,
            $requestDto->request->getHost(),
            $requestDto->locale,
            $tokenSession,
            $this->generateUrl('user_login_execute')
        );

        return $this->render('home/tryout/index.html.twig', [
            'tryoutComponentDto' => $tryoutComponentDto,
        ]);
    }
}
