<?php

declare(strict_types=1);

namespace App\Controller\User\SignUp\Home;

use App\Controller\Request\RequestDto;
use App\Form\User\Signup\SIGNUP_FORM_FIELDS;
use App\Form\User\Signup\SignupForm;
use App\Twig\Components\User\Signup\SignupComponentDto;
use Common\Adapter\Captcha\Recaptcha3ValidatorAdapter;
use Common\Domain\Config\Config;
use Common\Domain\ControllerUrlRefererRedirect\FLASH_BAG_TYPE_SUFFIX;
use Common\Domain\PageTitle\GetPageTitleService;
use Common\Domain\Ports\Endpoints\EndpointsInterface;
use Common\Domain\Ports\FlashBag\FlashBagInterface;
use Common\Domain\Ports\Form\FormFactoryInterface;
use Common\Domain\Ports\Form\FormInterface;
use Common\Domain\Ports\HttpClient\HttpClientInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(
    path: '{_locale}/user/signup',
    name: 'user_register',
    methods: ['GET', 'POST'],
    requirements: [
        '_locale' => Config::CLIENT_DOMAIN_LOCALE_VALID,
    ]
)]
class UserSignupHomeController extends AbstractController
{
    public function __construct(
        private FormFactoryInterface $formFactory,
        private EndpointsInterface $endpoint,
        private HttpClientInterface $httpClient,
        private GetPageTitleService $getPageTitleService,
        private FlashBagInterface $sessionFlashBag,
        private Recaptcha3ValidatorAdapter $recaptcha,
    ) {
    }

    public function __invoke(RequestDto $requestDto): Response
    {
        $form = $this->formFactory->create(new SignupForm($this->recaptcha), $requestDto->request);

        $signupMessage = $this->getSignupMessages($requestDto->request);

        return $this->renderSignupComponent($form, $signupMessage);
    }

    /**
     * @return array{ok: array<string>, error: array<string>, form: array, signedUp: bool}
     */
    private function getSignupMessages(Request $request): array
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
     * @param array{ok: array<string>, error: array<string>, form: array, signedUp: bool} $signupMessages
     */
    private function renderSignupComponent(FormInterface $form, array $signupMessages): Response
    {
        $validForm = false;
        if (!empty($signupMessages['ok']) || !empty($signupMessages['error'])) {
            $validForm = true;
        }

        $data = new SignupComponentDto(
            $signupMessages['error'],
            $signupMessages['form'][SIGNUP_FORM_FIELDS::EMAIL] ?? '',
            $signupMessages['form'][SIGNUP_FORM_FIELDS::PASSWORD] ?? '',
            $signupMessages['form'][SIGNUP_FORM_FIELDS::PASSWORD_REPEATED] ?? '',
            $signupMessages['form'][SIGNUP_FORM_FIELDS::NICK] ?? '',
            $form->getCsrfToken(),
            $this->generateUrl('user_register_execute'),
            $validForm
        );

        return $this->render('user/user_signup/index.html.twig', [
            'SignupComponent' => $data,
            'pageTitle' => $this->getPageTitleService->__invoke('SignupComponent'),
        ]);
    }
}
