<?php

declare(strict_types=1);

namespace App\Controller\User\Remember;

use App\Form\User\PasswordRemember\PASSWORD_REMEMBER_FORM_ERRORS;
use App\Form\User\PasswordRemember\PASSWORD_REMEMBER_FORM_FIELDS;
use App\Form\User\PasswordRemember\PasswordRememberForm;
use App\Twig\Components\User\PasswordRemember\PasswordRememberDto;
use Common\Adapter\Captcha\Recaptcha3ValidatorAdapter;
use Common\Adapter\HttpClientConfiguration\HTTP_CLIENT_CONFIGURATION;
use Common\Domain\Config\Config;
use Common\Domain\HttpClient\Exception\Error400Exception;
use Common\Domain\HttpClient\Exception\Error500Exception;
use Common\Domain\HttpClient\Exception\NetworkException;
use Common\Domain\PageTitle\GetPageTitleService;
use Common\Domain\Ports\Form\FormFactoryInterface;
use Common\Domain\Ports\Form\FormInterface;
use Common\Domain\Ports\HttpClient\HttpClientInterface;
use Common\Domain\Ports\HttpClient\HttpClientResponseInterface;
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
    private const PASSWORD_REMEMBER_ENDPOINT = '/api/v1/users/remember';

    public function __construct(
        private FormFactoryInterface $formFactory,
        private HttpClientInterface $httpClient,
        private GetPageTitleService $getPageTitleService,
        private Recaptcha3ValidatorAdapter $recaptcha3ValidatorAdapter
    ) {
    }

    public function __invoke(Request $request): Response
    {
        $form = $this->formFactory->create(new PasswordRememberForm($this->recaptcha3ValidatorAdapter), $request);

        if ($form->isSubmitted() && $form->isValid()) {
            return $this->formValid($form, $request->getLocale());
        }

        return $this->renderPasswordRemember($form);
    }

    private function formValid(FormInterface $form, string $locale): Response
    {
        try {
            $response = $this->requestPasswordRemember($form, $locale);
            $responseData = $response->toArray();
            $responseHttp = $this->redirectToRoute('user_remember_email_send');
        } catch (Error400Exception $e) {
            $responseData = $e->getResponse()->toArray(false);
            foreach ($responseData['errors'] as $error => $errorValue) {
                $form->addError($error, $errorValue);
            }
        } catch (Error500Exception|NetworkException) {
            $form->addError(PASSWORD_REMEMBER_FORM_ERRORS::INTERNAL_SERVER->value);
        } finally {
            return $responseHttp ?? $this->renderPasswordRemember($form);
        }
    }

    private function requestPasswordRemember(FormInterface $form, string $locale): HttpClientResponseInterface
    {
        $formData = $form->getData();

        return $this->httpClient->request(
            'POST',
            HTTP_CLIENT_CONFIGURATION::API_DOMAIN.self::PASSWORD_REMEMBER_ENDPOINT."?lang={$locale}",
            HTTP_CLIENT_CONFIGURATION::json([
                'email' => $formData[PASSWORD_REMEMBER_FORM_FIELDS::EMAIL],
                'email_password_remember_url' => HTTP_CLIENT_CONFIGURATION::CLIENT_DOMAIN."/{$locale}/user/password-remember",
            ])
        );
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
