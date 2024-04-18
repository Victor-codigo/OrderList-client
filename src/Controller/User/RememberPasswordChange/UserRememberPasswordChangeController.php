<?php

declare(strict_types=1);

namespace App\Controller\User\RememberPasswordChange;

use App\Form\PasswordChange\PASSWORD_CHANGE_FORM_ERRORS;
use App\Form\PasswordChange\PASSWORD_CHANGE_FORM_FIELDS;
use App\Form\PasswordChange\PasswordChangeForm;
use App\Twig\Components\User\PasswordChange\PasswordChangeComponentDto;
use Common\Adapter\Form\FormFactory;
use Common\Adapter\HttpClientConfiguration\HTTP_CLIENT_CONFIGURATION;
use Common\Domain\HttpClient\Exception\Error400Exception;
use Common\Domain\HttpClient\Exception\Error500Exception;
use Common\Domain\HttpClient\Exception\NetworkException;
use Common\Domain\Ports\Form\FormInterface;
use Common\Domain\Ports\HttpClient\HttpClientInterface;
use Common\Domain\Ports\HttpClient\HttpClientResponseInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(
    path: '{_locale}/user/password-remember/{token}',
    name: 'user_remember_password_change',
    methods: ['GET', 'POST'],
    requirements: [
        '_locale' => 'en|es',
    ]
)]
class UserRememberPasswordChangeController extends AbstractController
{
    private const PASSWORD_CHANGE_ENDPOINT = '/api/v1/users/password-remember';

    public function __construct(
        private HttpClientInterface $httpClient,
        private FormFactory $formFactory
    ) {
    }

    public function __invoke(Request $request): Response
    {
        $sessionToken = $request->attributes->get('token');
        $form = $this->formFactory->create(new PasswordChangeForm(), $request);

        if ($form->isSubmitted() && $form->isValid()) {
            return $this->formValid($form, $sessionToken);
        }

        return $this->renderPasswordChange($form);
    }

    private function formValid(FormInterface $form, string $sessionToken): Response
    {
        try {
            $response = $this->requestPasswordChange($form, $sessionToken);
            $responseData = $response->getContent();
            $responseHttp = $this->redirectToRoute('user_password_remember_changed');
        } catch (Error400Exception $e) {
            $responseData = $e->getResponse()->toArray(false);
            foreach ($responseData['errors'] as $error => $errorValue) {
                $form->addError($error, $errorValue);
            }
        } catch (Error500Exception|NetworkException $e) {
            $form->addError(PASSWORD_CHANGE_FORM_ERRORS::INTERNAL_SERVER->value);
        } finally {
            return $responseHttp ?? $this->renderPasswordChange($form);
        }
    }

    private function requestPasswordChange(FormInterface $form, string $sessionToken): HttpClientResponseInterface
    {
        $formData = $form->getData();

        return $this->httpClient->request(
            'PATCH',
            HTTP_CLIENT_CONFIGURATION::API_DOMAIN.self::PASSWORD_CHANGE_ENDPOINT.'?'.HTTP_CLIENT_CONFIGURATION::XDEBUG_VAR,
            HTTP_CLIENT_CONFIGURATION::json([
                'token' => $sessionToken,
                'passwordNew' => $formData[PASSWORD_CHANGE_FORM_FIELDS::PASSWORD_NEW],
                'passwordNewRepeat' => $formData[PASSWORD_CHANGE_FORM_FIELDS::PASSWORD_NEW_REPEAT],
            ])
        );
    }

    private function renderPasswordChange(FormInterface $form): Response
    {
        $formData = $form->getData();
        $data = new PasswordChangeComponentDto(
            $form->getErrors(),
            $formData[PASSWORD_CHANGE_FORM_FIELDS::PASSWORD_OLD],
            $formData[PASSWORD_CHANGE_FORM_FIELDS::PASSWORD_NEW],
            $formData[PASSWORD_CHANGE_FORM_FIELDS::PASSWORD_NEW_REPEAT],
            $form->getCsrfToken(),
            false
        );

        return $this->render('user_remember_password_change/index.html.twig', [
            'PasswordChangeComponent' => $data,
        ]);
    }
}
