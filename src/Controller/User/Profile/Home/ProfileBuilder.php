<?php

declare(strict_types=1);

namespace App\Controller\User\Profile\Home;

use App\Controller\Request\Response\UserDataResponse;
use App\Twig\Components\Modal\ModalComponentDto;
use App\Twig\Components\User\EmailChange\EmailChangeComponentDto;
use App\Twig\Components\User\PasswordChange\PasswordChangeComponentDto;
use App\Twig\Components\User\Profile\ProfileComponentDto;
use App\Twig\Components\User\UserRemove\UserRemoveComponentDto;
use Common\Domain\Ports\Form\FormInterface;
use Symfony\Component\Routing\RouterInterface;

class ProfileBuilder
{
    public function __construct(
        private RouterInterface $router
    ) {
    }

    /**
     * @param string[] $messagesError
     */
    public function __invoke(
        array $messagesError,
        array $messagesOk,
        UserDataResponse $userData,
        FormInterface $formProfile,
        FormInterface $formEmailChange,
        FormInterface $formPasswordChange,
        FormInterface $formUserRemove,
        bool $validForm,
        ?string $userImagePublicPath
    ): ProfileComponentDto {
        return new ProfileComponentDto(
            $messagesError,
            $messagesOk,
            $formProfile->getCsrfToken(),
            $userData->email,
            $userData->name,
            $userImagePublicPath,
            $this->getEmailModalData($formEmailChange),
            $this->getPasswordModalData($formPasswordChange, $userData->id),
            $this->getUserRemoveModalData($formUserRemove, $userData->id),
            $this->router->generate('profile_change'),
            $validForm
        );
    }

    private function getUserRemoveModalData(FormInterface $formUserRemove, string $userId): ModalComponentDto
    {
        $userRemoveModalContentData = new UserRemoveComponentDto(
            $formUserRemove->getErrors(),
            $userId,
            $formUserRemove->getCsrfToken(),
            $this->router->generate('profile_remove'),
            false
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
            $formEmailChange->getCsrfToken(),
            $this->router->generate('profile_email_change'),
            false
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

    private function getPasswordModalData(FormInterface $formPasswordChange, string $userId): ModalComponentDto
    {
        $passwordModalContentData = new PasswordChangeComponentDto(
            [],
            $userId,
            '',
            '',
            '',
            $formPasswordChange->getCsrfToken(),
            true,
            $this->router->generate('profile_password_change'),
            false
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
