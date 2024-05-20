<?php

declare(strict_types=1);

namespace App\Controller\Notification\NotificationHome;

use App\Controller\Request\RequestDto;
use App\Controller\Request\Response\NotificationDataResponse;
use App\Form\Notification\NotificationRemove\NotificationRemoveForm;
use App\Twig\Components\Notification\NotificationHome\Home\NotificationHomeSectionComponentDto;
use App\Twig\Components\Notification\NotificationHome\NotificationHomeComponentBuilder;
use Common\Domain\Config\Config;
use Common\Domain\ControllerUrlRefererRedirect\ControllerUrlRefererRedirect;
use Common\Domain\ControllerUrlRefererRedirect\FLASH_BAG_TYPE_SUFFIX;
use Common\Domain\PageTitle\GetPageTitleService;
use Common\Domain\Ports\Endpoints\EndpointsInterface;
use Common\Domain\Ports\FlashBag\FlashBagInterface;
use Common\Domain\Ports\Form\FormFactoryInterface;
use Common\Domain\Ports\Form\FormInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(
    path: '{_locale}/{section}/page-{page}-{page_items}',
    name: 'notification_home',
    methods: ['GET', 'POST'],
    requirements: [
        '_locale' => Config::CLIENT_DOMAIN_LOCALE_VALID,
        'section' => 'notifications',
        'page' => '\d+',
        'page_items' => '\d+',
    ]
)]
class NotificationHomeController extends AbstractController
{
    public function __construct(
        private FormFactoryInterface $formFactory,
        private EndpointsInterface $endpoints,
        private FlashBagInterface $sessionFlashBag,
        private ControllerUrlRefererRedirect $controllerUrlRefererRedirect,
        private GetPageTitleService $getPageTitleService
    ) {
    }

    public function __invoke(RequestDto $requestDto): Response
    {
        $notificationRemoveForm = $this->formFactory->create(new NotificationRemoveForm(), $requestDto->request);

        $notificationsData = $this->getNotificationsData(
            $requestDto->page,
            $requestDto->pageItems,
            $requestDto->locale,
            $requestDto->getTokenSessionOrFail()
        );
        $this->markNotificationsAsViewed($notificationsData['notifications'], $requestDto->getTokenSessionOrFail());

        $notificationHomeComponentDto = $this->createNotificationHomeComponentDto(
            $requestDto,
            $notificationRemoveForm,
            $notificationsData['notifications'],
            $notificationsData['pages_total']
        );

        return $this->renderTemplate($notificationHomeComponentDto);
    }

    private function getNotificationsData(int $page, int $pageItems, string $lang, string $tokenSession): array
    {
        $notificationsData = $this->endpoints->notificationGetData(
            $page,
            $pageItems,
            $lang,
            $tokenSession
        );

        $notificationsData['data']['notifications'] = array_map(
            fn (array $notificationData) => NotificationDataResponse::fromArray($notificationData),
            $notificationsData['data']['notifications']
        );

        return $notificationsData['data'];
    }

    private function markNotificationsAsViewed(array $notificationsData, string $tokenSession): void
    {
        $notificationsId = array_map(
            fn (NotificationDataResponse $notificationData) => $notificationData->id,
            $notificationsData
        );
        $this->endpoints->notificationMarkAsViewed($notificationsId, $tokenSession);
    }

    private function createNotificationHomeComponentDto(RequestDto $requestDto, FormInterface $notificationRemoveForm, array $notificationsData, int $pagesTotal): NotificationHomeSectionComponentDto
    {
        $notificationHomeMessagesError = $this->sessionFlashBag->get(
            $requestDto->request->attributes->get('_route').FLASH_BAG_TYPE_SUFFIX::MESSAGE_ERROR->value
        );
        $notificationHomeMessagesOk = $this->sessionFlashBag->get(
            $requestDto->request->attributes->get('_route').FLASH_BAG_TYPE_SUFFIX::MESSAGE_OK->value
        );

        return (new NotificationHomeComponentBuilder())
            ->title(
                null
            )
            ->errors(
                $notificationHomeMessagesOk,
                $notificationHomeMessagesError
            )
            ->pagination(
                $requestDto->page,
                $requestDto->pageItems,
                $pagesTotal
            )
            ->listItems(
                $notificationsData,
            )
            ->validation(
                !empty($notificationHomeMessagesError) || !empty($notificationHomeMessagesOk) ? true : false,
            )
            ->notificationRemoveFormModal(
                $notificationRemoveForm->getCsrfToken(),
                $this->generateUrl('notification_remove')
            )
            ->build();
    }

    private function renderTemplate(NotificationHomeSectionComponentDto $notificationHomeSectionComponent): Response
    {
        return $this->render('notification/notification_home/index.html.twig', [
            'NotificationHomeSectionComponent' => $notificationHomeSectionComponent,
            'pageTitle' => $this->getPageTitleService->__invoke('NotificationHomeComponent'),
        ]);
    }
}
