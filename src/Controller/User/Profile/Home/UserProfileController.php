<?php

declare(strict_types=1);

namespace App\Controller\User\Profile\Home;

use App\Controller\Request\Response\UserDataResponse;
use App\Form\EmailChange\EmailChangeForm;
use App\Form\PasswordChange\PasswordChangeForm;
use App\Form\UserRemove\UserRemoveForm;
use App\Form\User\Profile\ProfileForm;
use Common\Adapter\Endpoints\Endpoints;
use Common\Adapter\HttpClientConfiguration\HTTP_CLIENT_CONFIGURATION;
use Common\Domain\ControllerUrlRefererRedirect\FLASH_BAG_TYPE_SUFFIX;
use Common\Domain\Ports\FlashBag\FlashBagInterface;
use Common\Domain\Ports\Form\FormFactoryInterface;
use Common\Domain\Ports\Form\FormInterface;
use Common\Domain\Ports\HttpClient\HttpClientInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Translation\Exception\NotFoundResourceException;

#[Route(
    path: '{_locale}/user/profile/{user_name}',
    name: 'user_profile',
    methods: ['GET', 'POST'],
    requirements: [
        '_locale' => 'en|es',
    ]
)]
class UserProfileController extends AbstractController
{
    private const PROFILE_IMAGE_NOT_SET = '/assets/img/common/user-avatar-no-image.svg';

    private UserDataResponse $userData;

    public function __construct(
        private FormFactoryInterface $formFactory,
        private HttpClientInterface $httpClient,
        private Endpoints $apiEndpoint,
        private ProfileBuilder $profileBuilder,
        private FlashBagInterface $sessionFlashBag,
        private readonly string $apiUrl
    ) {
    }

    public function __invoke(Request $request): Response
    {
        $formEmailChange = $this->formFactory->create(new EmailChangeForm(), $request);
        $formPasswordChange = $this->formFactory->create(new PasswordChangeForm(), $request);
        $formProfile = $this->formFactory->create(new ProfileForm(), $request);
        $formUserRemove = $this->formFactory->create(new UserRemoveForm(), $request);
        $tokenSession = $request->cookies->get(HTTP_CLIENT_CONFIGURATION::COOKIE_SESSION_NAME);
        $userNameEncoded = $request->attributes->get('user_name');

        $this->userData = $this->getUserData($userNameEncoded, $tokenSession);

        return $this->renderUserProfileComponent(
            $request,
            $formProfile,
            $formEmailChange,
            $formPasswordChange,
            $formUserRemove,
        );
    }

    private function getUserData(string $userNameEncoded, string $tokenSession): UserDataResponse
    {
        $usersData = $this->apiEndpoint->usersGetDataByName([$userNameEncoded], $tokenSession);

        if (!empty($usersData['errors'])) {
            throw new NotFoundResourceException('Profile not found');
        }

        $usersData['data']['users'] = array_map(
            fn (array $usersData) => UserDataResponse::fromArray($usersData),
            $usersData['data']['users'] ?? []
        );

        return $usersData['data']['users'][0];
    }

    public function renderUserProfileComponent(
        Request $request,
        FormInterface $formProfile,
        FormInterface $formEmailChange,
        FormInterface $formPasswordChange,
        FormInterface $formUserRemove,
    ): Response {
        $profileHomeMessagesError = $this->sessionFlashBag->get(
            $request->attributes->get('_route').FLASH_BAG_TYPE_SUFFIX::MESSAGE_ERROR->value
        );
        $profileHomeMessagesOk = $this->sessionFlashBag->get(
            $request->attributes->get('_route').FLASH_BAG_TYPE_SUFFIX::MESSAGE_OK->value
        );

        $validForm = false;
        if (!empty($profileHomeMessagesError) || !empty($profileHomeMessagesOk)) {
            $validForm = true;
        }

        $profileComponentData = $this->profileBuilder->__invoke(
            $profileHomeMessagesError,
            $profileHomeMessagesOk,
            $this->userData,
            $formProfile,
            $formEmailChange,
            $formPasswordChange,
            $formUserRemove,
            $validForm,
            null === $this->userData->image
                ? HTTP_CLIENT_CONFIGURATION::API_DOMAIN.self::PROFILE_IMAGE_NOT_SET
                : $this->apiUrl.$this->userData->image,
        );

        return $this->render('user_profile/index.html.twig', [
            'ProfileComponent' => $profileComponentData,
        ]);
    }
}
