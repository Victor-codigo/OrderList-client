<?php

declare(strict_types=1);

namespace App\Controller\User\Profile\Home;

use App\Controller\Request\RequestDto;
use App\Controller\Request\Response\UserDataResponse;
use App\Form\User\EmailChange\EmailChangeForm;
use App\Form\User\PasswordChange\PasswordChangeForm;
use App\Form\User\Profile\ProfileForm;
use App\Form\User\UserRemove\UserRemoveForm;
use Common\Adapter\Endpoints\Endpoints;
use Common\Adapter\Events\Exceptions\NotFoundHttpException;
use Common\Domain\Config\Config;
use Common\Domain\ControllerUrlRefererRedirect\FLASH_BAG_TYPE_SUFFIX;
use Common\Domain\PageTitle\GetPageTitleService;
use Common\Domain\Ports\FlashBag\FlashBagInterface;
use Common\Domain\Ports\Form\FormFactoryInterface;
use Common\Domain\Ports\Form\FormInterface;
use Common\Domain\Ports\HttpClient\HttpClientInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(
    path: '{_locale}/user/profile/{user_name}',
    name: 'user_profile',
    methods: ['GET', 'POST'],
    requirements: [
        '_locale' => Config::CLIENT_DOMAIN_LOCALE_VALID,
    ]
)]
class UserProfileController extends AbstractController
{
    private UserDataResponse $userData;

    public function __construct(
        private FormFactoryInterface $formFactory,
        private HttpClientInterface $httpClient,
        private Endpoints $apiEndpoint,
        private ProfileBuilder $profileBuilder,
        private FlashBagInterface $sessionFlashBag,
        private GetPageTitleService $getPageTitleService,
    ) {
    }

    public function __invoke(RequestDto $requestDto): Response
    {
        $formEmailChange = $this->formFactory->create(new EmailChangeForm(), $requestDto->request);
        $formPasswordChange = $this->formFactory->create(new PasswordChangeForm(), $requestDto->request);
        $formProfile = $this->formFactory->create(new ProfileForm(), $requestDto->request);
        $formUserRemove = $this->formFactory->create(new UserRemoveForm(), $requestDto->request);

        $this->userData = $this->getUserData($requestDto->userNameUrlEncoded, $requestDto->getTokenSessionOrFail());

        return $this->renderUserProfileComponent(
            $requestDto->request,
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
            throw new NotFoundHttpException('Profile not found');
        }

        if (empty($usersData['data']['users'])) {
            throw new NotFoundHttpException('Profile not found');
        }

        try {
            $usersData['data']['users'] = array_map(
                fn (array $usersData) => UserDataResponse::fromArray($usersData),
                $usersData['data']['users']
            );
        } catch (\Throwable $e) {
            throw new NotFoundHttpException('Profile not found');
        }

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
            $this->userData->image
        );

        return $this->render('user/user_profile/index.html.twig', [
            'ProfileComponent' => $profileComponentData,
            'pageTitle' => $this->getPageTitleService->__invoke('ProfileComponent'),
            'domainName' => Config::CLIENT_DOMAIN_NAME,
        ]);
    }
}
