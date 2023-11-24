<?php

declare(strict_types=1);

namespace Common\Domain\ControllerUrlRefererRedirect;

use App\Controller\Request\RequestRefererDto;
use Common\Adapter\Events\Exceptions\RequestRefererException;
use Common\Domain\Config\Config;
use Common\Domain\Ports\FlashBag\FlashBagInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;

class ControllerUrlRefererRedirect
{
    public function __construct(
        private FlashBagInterface $sessionFlashBag,
        private RouterInterface $router
    ) {
    }

    public function createRedirectToRoute(string $routeName, array $routeParams, array $formValidationMessagesOk, array $formValidationMessagesError): RedirectResponse
    {
        if (empty($formValidationMessagesError)) {
            array_map(
                fn (string $messageOk) => $this->sessionFlashBag->add($routeName.Config::FLASH_BAG_FORM_NAME_SUFFIX_MESSAGE_OK, $messageOk),
                $formValidationMessagesOk
            );
        }

        array_map(
            fn (string $messageError) => $this->sessionFlashBag->add($routeName.Config::FLASH_BAG_FORM_NAME_SUFFIX_MESSAGE_ERROR, $messageError),
            $formValidationMessagesError
        );

        return new RedirectResponse(
            $this->router->generate($routeName, $routeParams),
            Response::HTTP_MOVED_PERMANENTLY
        );
    }

    public function validateReferer(RequestRefererDto $requestReferer): void
    {
        if (null === $requestReferer) {
            throw RequestRefererException::fromMessage('Request referer not valid');
        }
    }
}
