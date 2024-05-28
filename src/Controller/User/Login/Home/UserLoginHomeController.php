<?php

declare(strict_types=1);

namespace App\Controller\User\Login\Home;

use App\Form\User\Login\LoginForm;
use App\Twig\Components\User\Login\LoginComponentDto;
use Common\Adapter\Captcha\Recaptcha3ValidatorAdapter;
use Common\Adapter\Endpoints\Endpoints;
use Common\Adapter\Form\FormFactory;
use Common\Domain\Config\Config;
use Common\Domain\ControllerUrlRefererRedirect\FLASH_BAG_TYPE_SUFFIX;
use Common\Domain\PageTitle\GetPageTitleService;
use Common\Domain\Ports\FlashBag\FlashBagInterface;
use Common\Domain\Ports\Form\FormInterface;
use Common\Domain\Ports\HttpClient\HttpClientInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(
    path: '{_locale}/user/login',
    name: 'user_login_home',
    methods: ['GET', 'POST'],
    requirements: [
        '_locale' => Config::CLIENT_DOMAIN_LOCALE_VALID,
    ]
)]
class UserLoginHomeController extends AbstractController
{
    public function __construct(
        private FormFactory $formFactory,
        private HttpClientInterface $httpClient,
        private Endpoints $apiEndpoints,
        private FlashBagInterface $sessionFlashBag,
        private GetPageTitleService $getPageTitleService,
        private Recaptcha3ValidatorAdapter $recaptcha,
        private int $cookieSessionKeepAlive,
        private string $domainName,
        private string $cookieSessionName
    ) {
    }

    public function __invoke(Request $request): Response
    {
        $form = $this->formFactory->create(new LoginForm($this->recaptcha), $request);

        return $this->renderLoginComponent($form, $request);
    }

    private function renderLoginComponent(FormInterface $form, Request $request): Response
    {
        $profileHomeMessagesError = $this->sessionFlashBag->get(
            $request->attributes->get('_route').FLASH_BAG_TYPE_SUFFIX::MESSAGE_ERROR->value
        );

        $validForm = false;
        if (!empty($profileHomeMessagesError)) {
            $validForm = true;
        }

        $loginComponentData = new LoginComponentDto(
            $profileHomeMessagesError,
            '',
            '',
            false,
            $form->getCsrfToken(),
            $this->generateUrl('user_login_execute'),
            $validForm
        );

        return $this->render('user/user_login/index.html.twig', [
            'LoginComponent' => $loginComponentData,
            'pageTitle' => $this->getPageTitleService->__invoke('LoginComponent'),
        ]);
    }
}
