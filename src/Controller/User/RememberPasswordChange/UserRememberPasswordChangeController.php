<?php

declare(strict_types=1);

namespace App\Controller\User\RememberPasswordChange;

use App\Form\User\PasswordChange\PASSWORD_CHANGE_FORM_FIELDS;
use App\Form\User\PasswordChange\PasswordChangeForm;
use App\Twig\Components\User\PasswordChange\PasswordChangeComponentDto;
use Common\Adapter\Form\FormFactory;
use Common\Domain\Config\Config;
use Common\Domain\PageTitle\GetPageTitleService;
use Common\Domain\Ports\Endpoints\EndpointsInterface;
use Common\Domain\Ports\Form\FormInterface;
use Common\Domain\Ports\HttpClient\HttpClientInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(
    path: '{_locale}/user/password-remember/{token}',
    name: 'user_remember_password_change',
    methods: ['GET', 'POST'],
    requirements: [
        '_locale' => Config::CLIENT_DOMAIN_LOCALE_VALID,
    ]
)]
class UserRememberPasswordChangeController extends AbstractController
{
    public function __construct(
        private HttpClientInterface $httpClient,
        private FormFactory $formFactory,
        private EndpointsInterface $endpoints,
        private GetPageTitleService $getPageTitleService
    ) {
    }

    public function __invoke(Request $request): Response
    {
        $sessionToken = $request->attributes->get('token');
        $form = $this->formFactory->create(new PasswordChangeForm(), $request);

        $validForm = false;
        if ($form->isSubmitted() && $form->isValid()) {
            $validForm = true;
            $this->formValid($form, $sessionToken);
        }

        return $this->renderPasswordChange($form, $validForm);
    }

    private function formValid(FormInterface $form, string $sessionToken): void
    {
        $responseData = $this->endpoints->userRememberPasswordChange(
            $form->getFieldData(PASSWORD_CHANGE_FORM_FIELDS::PASSWORD_NEW, ''),
            $form->getFieldData(PASSWORD_CHANGE_FORM_FIELDS::PASSWORD_NEW_REPEAT, ''),
            $sessionToken
        );

        foreach ($responseData['errors'] as $error => $errorDescription) {
            $form->addError((string) $error, $errorDescription);
        }
    }

    private function renderPasswordChange(FormInterface $form, bool $validForm): Response
    {
        $formData = $form->getData();
        $data = new PasswordChangeComponentDto(
            $form->getErrors(),
            $formData[PASSWORD_CHANGE_FORM_FIELDS::USER_ID] ?? '',
            $formData[PASSWORD_CHANGE_FORM_FIELDS::PASSWORD_OLD],
            $formData[PASSWORD_CHANGE_FORM_FIELDS::PASSWORD_NEW],
            $formData[PASSWORD_CHANGE_FORM_FIELDS::PASSWORD_NEW_REPEAT],
            $form->getCsrfToken(),
            false,
            '',
            $validForm,
        );

        return $this->render('user/user_remember_password_change/index.html.twig', [
            'PasswordChangeComponent' => $data,
            'pageTitle' => $this->getPageTitleService->__invoke('PasswordChangeComponent'),
            'domainName' => Config::CLIENT_DOMAIN_NAME,
        ]);
    }
}
