<?php

declare(strict_types=1);

namespace App\Controller\User\Remember;

use App\Form\User\PasswordRemember\PASSWORD_REMEMBER_FORM_FIELDS;
use App\Form\User\PasswordRemember\PasswordRememberForm;
use App\Twig\Components\User\PasswordRemember\PasswordRememberDto;
use Common\Adapter\Captcha\Recaptcha3ValidatorAdapter;
use Common\Domain\Config\Config;
use Common\Domain\PageTitle\GetPageTitleService;
use Common\Domain\Ports\Endpoints\EndpointsInterface;
use Common\Domain\Ports\Form\FormFactoryInterface;
use Common\Domain\Ports\Form\FormInterface;
use Common\Domain\Ports\HttpClient\HttpClientInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(
    path: '{_locale}/user/remember',
    name: 'user_remember',
    methods: ['GET', 'POST'],
    requirements: [
        '_locale' => Config::CLIENT_DOMAIN_LOCALE_VALID,
    ]
)]
class UserRememberController extends AbstractController
{
    public function __construct(
        private FormFactoryInterface $formFactory,
        private HttpClientInterface $httpClient,
        private EndpointsInterface $endpoints,
        private GetPageTitleService $getPageTitleService,
        private Recaptcha3ValidatorAdapter $recaptcha3ValidatorAdapter,
    ) {
    }

    public function __invoke(Request $request): Response
    {
        $form = $this->formFactory->create(new PasswordRememberForm($this->recaptcha3ValidatorAdapter), $request);

        $passwordRemembered = false;
        if ($form->isSubmitted() && $form->isValid()) {
            $passwordRemembered = $this->formValid($form, $request->getLocale());
        }

        if ($passwordRemembered) {
            return $this->redirectToRoute('user_remember_email_send');
        }

        return $this->renderPasswordRemember($form);
    }

    private function formValid(FormInterface $form, string $locale): bool
    {
        $responseData = $this->endpoints->userRememberPassword(
            $form->getFieldData(PASSWORD_REMEMBER_FORM_FIELDS::EMAIL),
            Config::CLIENT_PROTOCOL.'://'.Config::CLIENT_DOMAIN."/{$locale}/user/password-remember",
            $locale
        );

        foreach ($responseData['errors'] as $error => $errorDescription) {
            $form->addError((string) $error, $errorDescription);
        }

        return empty($form->getErrors());
    }

    private function renderPasswordRemember(FormInterface $form): Response
    {
        $formData = $form->getData();
        $data = new PasswordRememberDto(
            $form->getErrors(),
            $formData[PASSWORD_REMEMBER_FORM_FIELDS::EMAIL],
            $form->getCsrfToken()
        );

        return $this->render('user/user_remember/index.html.twig', [
            'PasswordRememberComponent' => $data,
            'pageTitle' => $this->getPageTitleService->__invoke('PasswordRememberComponent'),
            'domainName' => Config::CLIENT_DOMAIN_NAME,
        ]);
    }
}
