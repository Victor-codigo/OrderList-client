<?php

namespace App\Controller;

use App\Form\EmailChange\EMAIL_CHANGE_FORM_FIELDS;
use App\Form\EmailChange\EmailChangeForm;
use App\Form\PasswordChange\PASSWORD_CHANGE_FORM_FIELDS;
use App\Form\PasswordChange\PasswordChangeForm;
use App\Form\Profile\PROFILE_FORM_ERRORS;
use App\Form\Profile\PROFILE_FORM_FIELDS;
use App\Form\Profile\ProfileForm;
use App\Form\UserRemove\UserRemoveForm;
use App\Twig\Components\Modal\ModalComponentDto;
use App\Twig\Components\User\EmailChange\EmailChangeComponentDto;
use App\Twig\Components\User\PasswordChange\PasswordChangeComponentDto;
use App\Twig\Components\User\Profile\ProfileComponentDto;
use App\Twig\Components\User\UserRemove\UserRemoveComponentDto;
use Common\Adapter\HttpClientConfiguration\HTTP_CLIENT_CONFIGURATION;
use Common\Domain\HttpClient\Exception\Error400Exception;
use Common\Domain\HttpClient\Exception\Error500Exception;
use Common\Domain\HttpClient\Exception\NetworkException;
use Common\Domain\Ports\Form\FormFactoryInterface;
use Common\Domain\Ports\Form\FormInterface;
use Common\Domain\Ports\HttpCllent\HttpClientInterface;
use Common\Domain\Ports\HttpCllent\HttpClientResponseInteface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(
    path: '{_locale}/user/profile/{id}',
    name: 'user_profile',
    methods: ['GET', 'POST'],
    requirements: [
        '_locale' => 'en|es',
    ]
)]
class UserProfileController extends AbstractController
{
    private const PROFILE_ENDPOINT = '/api/v1/users/modify';
    private const PROFILE_EMAIL_CHANGE_ENDPONIT = '/api/v1/users/email';
    private const PROFILE_PASSWORD_CHANGE_ENDPOINT = '/api/v1/users/password';
    private const PROFILE_GET_USER_ENDPOINT = '/api/v1/users';
    private const PROFILE_USER_REMOVE_ENDPOINT = '/api/v1/users/remove';
    private const PROFILE_IMAGE_NOT_SET = '/assets/img/common/user-avatar-no-image.svg';

    private array $userData;

    public function __construct(
        private FormFactoryInterface $formFactory,
        private HttpClientInterface $httpClient,
        private string $apiUrl
    ) {
    }

    public function __invoke(Request $request): Response
    {
        $formEmailChange = $this->formFactory->create(new EmailChangeForm(), $request);
        $formPasswordChange = $this->formFactory->create(new PasswordChangeForm(), $request);
        $formProfile = $this->formFactory->create(new ProfileForm(), $request);
        $formUserRemove = $this->formFactory->create(new UserRemoveForm(), $request);
        $tokenSession = $request->cookies->get('TOKENSESSION');
        $userId = $request->attributes->get('id');
        $errorList = [];
        $submited = false;

        if ($formEmailChange->isSubmitted() && $formEmailChange->isValid()) {
            $errorList = $this->formManagement($this->requestEmailChange(...), $formEmailChange, $tokenSession);
            $submited = true;
        } elseif ($formPasswordChange->isSubmitted() && $formPasswordChange->isValid()) {
            $errorList = $this->formManagement($this->requestPasswordChange(...), $formPasswordChange, $userId, $tokenSession);
            $submited = true;
        } elseif ($formProfile->isSubmitted() && $formProfile->isValid()) {
            $errorList = $this->formManagement($this->requestProfile(...), $formProfile, $tokenSession);
            $submited = true;
        } elseif ($formUserRemove->isSubmitted() && $formUserRemove->isValid()) {
            $errorList = $this->formManagement($this->requestUserRemove(...), $userId, $tokenSession);
            $submited = true;
        }

        $this->userData = $this->getUserData($userId, $tokenSession);
        $this->addFormErrors($formProfile, $errorList);

        return $this->renderUserProfileComponent(
            $formProfile,
            $formEmailChange,
            $formPasswordChange,
            $formUserRemove,
            $submited,
            $errorList
        );
    }

    private function formManagement(callable $requestCallback, ...$requestCallbackArguments): array
    {
        try {
            $response = $requestCallback(...$requestCallbackArguments);
            $responseData = $response->toArray();

            return [];
        } catch (Error400Exception $e) {
            $responseData = $e->getResponse()->toArray(false);

            return isset($responseData['errors']) ? $responseData['errors'] : [];
        } catch (Error500Exception|NetworkException) {
            return [PROFILE_FORM_ERRORS::INTERNAL_SERVER];
        }
    }

    private function getUserData(string $userId, string $tokenSession): array
    {
        $response = $this->requestUserData($userId, $tokenSession);
        $responseData = $response->toArray();

        return $responseData['data'][0];
    }

    private function requestUserData(string $userId, string $tokenSession): HttpClientResponseInteface
    {
        return $this->httpClient->request(
            'GET',
            HTTP_CLIENT_CONFIGURATION::API_DOMAIN.self::PROFILE_GET_USER_ENDPOINT."/{$userId}?".HTTP_CLIENT_CONFIGURATION::XDEBUG_VAR,
            HTTP_CLIENT_CONFIGURATION::json(null, $tokenSession)
        );
    }

    private function addFormErrors(FormInterface $form, array $errorList): void
    {
        foreach ($errorList as $error => $errorValue) {
            $form->addError($error, $errorValue);
        }
    }

    private function requestEmailChange(FormInterface $form, string $tokenSession): HttpClientResponseInteface
    {
        $formData = $form->getData();

        return $this->httpClient->request(
            'PATCH',
            HTTP_CLIENT_CONFIGURATION::API_DOMAIN.self::PROFILE_EMAIL_CHANGE_ENDPONIT.'?'.HTTP_CLIENT_CONFIGURATION::XDEBUG_VAR,
            HTTP_CLIENT_CONFIGURATION::json(
                [
                    'email' => $formData[EMAIL_CHANGE_FORM_FIELDS::EMAIL],
                    'password' => $formData[EMAIL_CHANGE_FORM_FIELDS::PASSWORD],
                ],
                $tokenSession
            )
        );
    }

    private function requestPasswordChange(FormInterface $form, string $userId, string $tokenSession): HttpClientResponseInteface
    {
        $formData = $form->getData();

        return $this->httpClient->request(
            'PATCH',
            HTTP_CLIENT_CONFIGURATION::API_DOMAIN.self::PROFILE_PASSWORD_CHANGE_ENDPOINT.'?'.HTTP_CLIENT_CONFIGURATION::XDEBUG_VAR,
            HTTP_CLIENT_CONFIGURATION::json([
                'id' => $userId,
                'passwordOld' => $formData[PASSWORD_CHANGE_FORM_FIELDS::PASSWORD_OLD],
                'passwordNew' => $formData[PASSWORD_CHANGE_FORM_FIELDS::PASSWORD_NEW],
                'passwordNewRepeat' => $formData[PASSWORD_CHANGE_FORM_FIELDS::PASSWORD_NEW_REPEAT],
            ],
                $tokenSession
            )
        );
    }

    private function requestProfile(FormInterface $form, string $tokenSession): HttpClientResponseInteface
    {
        $formData = $form->getData();

        return $this->httpClient->request(
            'POST',
            HTTP_CLIENT_CONFIGURATION::API_DOMAIN.self::PROFILE_ENDPOINT.'?'.HTTP_CLIENT_CONFIGURATION::XDEBUG_VAR,
            HTTP_CLIENT_CONFIGURATION::form([
                    'name' => $formData[PROFILE_FORM_FIELDS::NICK],
                    'image_remove' => $formData[PROFILE_FORM_FIELDS::IMAGE_REMOVE],
                    '_method' => 'PUT',
                ],
                [
                    'image' => $formData[PROFILE_FORM_FIELDS::IMAGE],
                ],
                $tokenSession
            )
        );
    }

    private function requestUserRemove(string $userId, string $tokenSession): HttpClientResponseInteface
    {
        return $this->httpClient->request(
            'DELETE',
            HTTP_CLIENT_CONFIGURATION::API_DOMAIN.self::PROFILE_USER_REMOVE_ENDPOINT."/{$userId}?".HTTP_CLIENT_CONFIGURATION::XDEBUG_VAR,
            HTTP_CLIENT_CONFIGURATION::json([], $tokenSession)
        );
    }

    public function renderUserProfileComponent(
        FormInterface $formProfile,
        FormInterface $formEmailChange,
        FormInterface $formPasswordChange,
        FormInterface $formUserRemove,
        bool $submited,
        array $errorList
    ): Response {
        $profileComponentData = new ProfileComponentDto(
            $errorList,
            $formProfile->getCsrfToken(),
            $this->userData['email'],
            $this->userData['name'],
            null === $this->userData['image']
                ? HTTP_CLIENT_CONFIGURATION::API_DOMAIN.self::PROFILE_IMAGE_NOT_SET
                : $this->apiUrl.$this->userData['image'],
            $this->getEmailModalData($formEmailChange),
            $this->getPasswordModalData($formPasswordChange),
            $this->getUserRemoveModalData($formUserRemove),
            $submited
        );

        return $this->render('user_profile/index.html.twig', [
            'ProfileComponent' => $profileComponentData,
        ]);
    }

    private function getUserRemoveModalData(FormInterface $formUserRemove): ModalComponentDto
    {
        $userRemoveModalContentData = new UserRemoveComponentDto(
            $formUserRemove->getErrors(),
            $this->userData['id'],
            $formUserRemove->getCsrfToken()
        );

        return new ModalComponentDto(
            'user_remove_modal',
            '',
            false,
            'UserRemoveComponent',
            $userRemoveModalContentData,
            []
        );
    }

    private function getEmailModalData(FormInterface $formEmailChange): ModalComponentDto
    {
        $emailModalContentData = new EmailChangeComponentDto(
            [],
            '',
            '',
            $formEmailChange->getCsrfToken()
        );

        return new ModalComponentDto(
            'email_modal',
            '',
            false,
            'EmailChangeComponent',
            $emailModalContentData,
            [],
        );
    }

    private function getPasswordModalData(FormInterface $formPasswordChange): ModalComponentDto
    {
        $passwordModalContentData = new PasswordChangeComponentDto(
            [],
            '',
            '',
            '',
            $formPasswordChange->getCsrfToken(),
            true
        );

        return new ModalComponentDto(
            'password_modal',
            '',
            false,
            'PasswordChangeComponent',
            $passwordModalContentData,
            [],
        );
    }
}
