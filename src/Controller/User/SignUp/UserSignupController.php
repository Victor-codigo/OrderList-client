<?php

declare(strict_types=1);

namespace App\Controller\User\SignUp;

use App\Form\User\Signup\SIGNUP_FORM_ERRORS;
use App\Form\User\Signup\SIGNUP_FORM_FIELDS;
use App\Form\User\Signup\SignupForm;
use App\Twig\Components\User\Signup\SignupComponentDto;
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
    path: '{_locale}/user/signup',
    name: 'user_register',
    methods: ['GET', 'POST'],
    requirements: [
        '_locale' => Config::CLIENT_DOMAIN_LOCALE_VALID,
    ]
)]
class UserSignupController extends AbstractController
{
    private const SIGNUP_ENDPOINT = '/api/v1/users';

    public function __construct(
        private FormFactoryInterface $formFactory,
        private HttpClientInterface $httpClient,
        private GetPageTitleService $getPageTitleService
    ) {
    }

    public function __invoke(Request $request): Response
    {
        $form = $this->formFactory->create(new SignupForm(), $request);

        if ($form->isSubmitted() && $form->isCsrfValid()) {
            return $this->formValid($form, $request->getLocale());
        }

        return $this->renderSignupComponent($form);
    }

    private function formValid(FormInterface $form, string $locale): Response
    {
        try {
            $response = $this->requestSignup($form->getData(), $locale);
            $responseData = (object) $response->toArray();
            $responseHttp = $this->redirectToRoute('user_register_complete');
        } catch (Error400Exception|Error500Exception $e) {
            $responseData = $e->getResponse()->toArray(false);
            foreach ($responseData['errors'] as $error => $errorValue) {
                $form->addError($error, $errorValue);
            }
        } catch (NetworkException) {
            $form->addError(SIGNUP_FORM_ERRORS::INTERNAL_SERVER->value);
        } finally {
            return $responseHttp ?? $this->renderSignupComponent($form);
        }
    }

    /**
     * @throws UnsupportedOptionException
     */
    private function requestSignup(array $formData, string $locale): HttpClientResponseInterface
    {
        return $this->httpClient->request(
            'POST',
            HTTP_CLIENT_CONFIGURATION::API_DOMAIN.self::SIGNUP_ENDPOINT.'?lang='.$locale,
            HTTP_CLIENT_CONFIGURATION::json([
                'name' => $formData[SIGNUP_FORM_FIELDS::NICK],
                'email' => $formData[SIGNUP_FORM_FIELDS::EMAIL],
                'password' => $formData[SIGNUP_FORM_FIELDS::PASSWORD],
                'email_confirmation_url' => 'http://orderlist.client/'.$locale.'/user/signup/confirm',
            ])
        );
    }

    private function renderSignupComponent(FormInterface $form): Response
    {
        $formData = $form->getData();
        $data = new SignupComponentDto(
            $form->getErrors(),
            $formData[SIGNUP_FORM_FIELDS::EMAIL],
            $formData[SIGNUP_FORM_FIELDS::PASSWORD],
            $formData[SIGNUP_FORM_FIELDS::PASSWORD_REPEATED],
            $formData[SIGNUP_FORM_FIELDS::NICK],
            $form->getCsrfToken()
        );

        return $this->render('user/user_signup/index.html.twig', [
            'SignupComponent' => $data,
            'pageTitle' => $this->getPageTitleService->__invoke('SignupComponent'),
        ]);
    }
}
