<?php

declare(strict_types=1);

namespace App\Controller\User\Login\Home;

use App\Controller\Request\RequestDto;
use App\Form\User\Login\LOGIN_FORM_FIELDS;
use App\Form\User\Login\LoginForm;
use App\Twig\Components\User\Login\LoginComponentDto;
use Common\Adapter\Captcha\Recaptcha3ValidatorAdapter;
use Common\Adapter\Form\FormFactory;
use Common\Domain\Config\Config;
use Common\Domain\ControllerUrlRefererRedirect\FLASH_BAG_TYPE_SUFFIX;
use Common\Domain\PageTitle\GetPageTitleService;
use Common\Domain\Ports\FlashBag\FlashBagInterface;
use Common\Domain\Ports\Form\FormInterface;
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
        private FlashBagInterface $sessionFlashBag,
        private GetPageTitleService $getPageTitleService,
        private Recaptcha3ValidatorAdapter $recaptcha,
        private string $domainName,
    ) {
    }

    public function __invoke(RequestDto $requestDto): Response
    {
        $form = $this->formFactory->create(new LoginForm($this->recaptcha), $requestDto->request);

        $loginMessages = $this->getLoginMessages($requestDto->request);

        return $this->renderLoginComponent($form, $loginMessages);
    }

    /**
     * @return array{ok: array<string>, error: array<string>, form: array, signedUp: bool}
     */
    private function getLoginMessages(Request $request): array
    {
        $signupMessagesError = $this->sessionFlashBag->get(
            $request->attributes->get('_route').FLASH_BAG_TYPE_SUFFIX::MESSAGE_ERROR->value
        );
        $signupMessagesOk = $this->sessionFlashBag->get(
            $request->attributes->get('_route').FLASH_BAG_TYPE_SUFFIX::MESSAGE_OK->value
        );
        $signupData = $this->sessionFlashBag->get(
            $request->attributes->get('_route').FLASH_BAG_TYPE_SUFFIX::DATA->value
        );

        $data = isset($signupData[0]) ? $signupData[0] : [];

        return [
            'ok' => $signupMessagesOk,
            'error' => $signupMessagesError,
            'form' => $data['form'] ?? [],
        ];
    }

    /**
     * @param array{ok: array<string>, error: array<string>, form: array, signedUp: bool} $loginMessages
     */
    private function renderLoginComponent(FormInterface $form, array $loginMessages): Response
    {
        $validForm = false;
        if (!empty($loginMessages['ok']) || !empty($loginMessages['error'])) {
            $validForm = true;
        }

        $loginComponentData = new LoginComponentDto(
            $loginMessages['error'],
            $loginMessages['form'][LOGIN_FORM_FIELDS::EMAIL] ?? '',
            $loginMessages['form'][LOGIN_FORM_FIELDS::PASSWORD] ?? '',
            false,
            $form->getCsrfToken(),
            $this->generateUrl('user_login_execute'),
            $validForm
        );

        return $this->render('user/user_login/index.html.twig', [
            'LoginComponent' => $loginComponentData,
            'pageTitle' => $this->getPageTitleService->__invoke('LoginComponent'),
            'domainName' => Config::CLIENT_DOMAIN_NAME,
        ]);
    }
}
