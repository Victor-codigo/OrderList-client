<?php

declare(strict_types=1);

namespace Common\Domain\ControllerUrlRefererRedirect;

use App\Controller\Request\RequestRefererDto;
use Common\Adapter\Events\Exceptions\RequestRefererException;
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

    public function createRedirectToRoute(string $routeName, array $routeParams, array $formValidationMessagesOk, array $formValidationMessagesError, array $data): RedirectResponse
    {
        if (empty($formValidationMessagesError)) {
            array_map(
                fn (string $messageOk) => $this->sessionFlashBag->add($routeName.FLASH_BAG_TYPE_SUFFIX::MESSAGE_OK->value, $messageOk),
                $formValidationMessagesOk
            );
        }

        array_map(
            fn (string $messageError) => $this->sessionFlashBag->add($routeName.FLASH_BAG_TYPE_SUFFIX::MESSAGE_ERROR->value, $messageError),
            $formValidationMessagesError
        );

        $this->sessionFlashBag->add($routeName.FLASH_BAG_TYPE_SUFFIX::DATA->value, $data);

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

    public function getFlashBag(string $routeName, FLASH_BAG_TYPE_SUFFIX $type): array
    {
        $flashBagData = $this->sessionFlashBag->get($routeName.$type->value);

        if (empty($flashBagData)) {
            return [];
        }

        if (FLASH_BAG_TYPE_SUFFIX::DATA === $type) {
            return $flashBagData[0];
        }

        return $flashBagData;
    }
}
