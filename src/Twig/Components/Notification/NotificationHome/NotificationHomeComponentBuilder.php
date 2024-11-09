<?php

declare(strict_types=1);

namespace App\Twig\Components\Notification\NotificationHome;

use App\Controller\Notification\NotificationHome\NOTIFICATION_TYPE;
use App\Controller\Request\Response\NotificationDataResponse;
use App\Twig\Components\HomeSection\Home\HomeSectionComponentDto;
use App\Twig\Components\Modal\ModalComponentDto;
use App\Twig\Components\Notification\NotificationHome\Home\NotificationHomeSectionComponentDto;
use App\Twig\Components\Notification\NotificationHome\ListItem\NotificationListItemComponent;
use App\Twig\Components\Notification\NotificationHome\ListItem\NotificationListItemComponentDto;
use App\Twig\Components\Notification\NotificationRemove\NotificationRemoveComponent;
use App\Twig\Components\Notification\NotificationRemove\NotificationRemoveComponentDto;
use Common\Domain\Config\Config;
use Common\Domain\DtoBuilder\DtoBuilder;
use Common\Domain\DtoBuilder\DtoBuilderInterface;

class NotificationHomeComponentBuilder implements DtoBuilderInterface
{
    private const NOTIFICATION_DELETE_MODAL_ID = 'notification_delete_modal';
    private const NOTIFICATION_INFO_MODAL_ID = 'notification_info_modal';

    private const NOTIFICATION_HOME_COMPONENT_NAME = 'NotificationHomeComponent';
    private const NOTIFICATION_HOME_LIST_COMPONENT_NAME = 'NotificationHomeListComponent';
    private const NOTIFICATION_HOME_LIST_ITEM_COMPONENT_NAME = 'NotificationHomeListItemComponent';

    public const string SHARED_RECOURSE_ID_URL_PLACEHOLDER = '--shared_recourse_id--';

    private readonly DtoBuilder $builder;
    private readonly HomeSectionComponentDto $homeSectionComponentDto;

    private readonly string $sharedRecourseUrlTemplate;

    /**
     * @var NotificationDataResponse[]
     */
    private readonly array $listNotificationsData;

    public function __construct()
    {
        $this->builder = new DtoBuilder([
            'title',
            'errors',
            'notificationRemoveFormModal',
            'pagination',
            'listItems',
            'validation',
            'utils',
        ]);

        $this->homeSectionComponentDto = $this->createHomeSectionComponentDto();
    }

    public function title(?string $title, ?string $titlePath): self
    {
        $this->builder->setMethodStatus('title', true);

        $this->homeSectionComponentDto->title($title, $titlePath);

        return $this;
    }

    /**
     * @param string[] $notificationSectionValidationOk
     * @param string[] $notificationValidationErrorsMessage
     */
    public function errors(array $notificationSectionValidationOk, array $notificationValidationErrorsMessage): self
    {
        $this->builder->setMethodStatus('errors', true);

        $this->homeSectionComponentDto->errors($notificationSectionValidationOk, $notificationValidationErrorsMessage);

        return $this;
    }

    public function validation(bool $validForm): self
    {
        $this->builder->setMethodStatus('validation', true);

        $this->homeSectionComponentDto->validation(
            $validForm,
        );

        return $this;
    }

    public function notificationRemoveFormModal(string $notificationRemoveFormCsrfToken, string $notificationRemoveFormActionUrl): self
    {
        $this->builder->setMethodStatus('notificationRemoveFormModal', true);

        $this->homeSectionComponentDto->removeFormModal(
            $this->createNotificationRemoveModalDto($notificationRemoveFormCsrfToken, $notificationRemoveFormActionUrl)
        );

        return $this;
    }

    public function pagination(int $page, int $pageItems, int $pagesTotal): self
    {
        $this->builder->setMethodStatus('pagination', true);

        $this->homeSectionComponentDto->pagination($page, $pageItems, $pagesTotal);

        return $this;
    }

    /**
     * @param NotificationDataResponse[] $listNotificationsData
     */
    public function listItems(array $listNotificationsData): self
    {
        $this->builder->setMethodStatus('listItems', true);

        $this->listNotificationsData = $listNotificationsData;

        return $this;
    }

    public function shareUrl(string $sharedRecourseUrlTemplate): self
    {
        $this->builder->setMethodStatus('utils', true);

        $this->sharedRecourseUrlTemplate = $sharedRecourseUrlTemplate;

        return $this;
    }

    public function build(): NotificationHomeSectionComponentDto
    {
        $this->builder->validate();

        $this->homeSectionComponentDto->translationDomainNames(
            self::NOTIFICATION_HOME_COMPONENT_NAME,
            self::NOTIFICATION_HOME_LIST_COMPONENT_NAME,
            self::NOTIFICATION_HOME_LIST_ITEM_COMPONENT_NAME,
        );
        $this->homeSectionComponentDto->removeMultiFormModal(null);
        $this->homeSectionComponentDto->createFormModal(null);
        $this->homeSectionComponentDto->searchBar(null);
        $this->homeSectionComponentDto->modifyFormModal(null);
        $this->homeSectionComponentDto->listItems(
            NotificationListItemComponent::getComponentName(),
            $this->createNotificationListItemsComponentsDto(),
            Config::NOTIFICATION_IMAGE_NO_IMAGE_PUBLIC_PATH_200_200
        );
        $this->homeSectionComponentDto->display(
            true,
            false
        );

        return $this->createNotificationHomeSectionComponentDto();
    }

    private function createNotificationRemoveModalDto(string $notificationRemoveFormCsrfToken, string $notificationRemoveFormActionUrl): ModalComponentDto
    {
        $homeModalDelete = new NotificationRemoveComponentDto(
            NotificationRemoveComponent::getComponentName(),
            [],
            $notificationRemoveFormCsrfToken,
            mb_strtolower($notificationRemoveFormActionUrl),
            false
        );

        return new ModalComponentDto(
            self::NOTIFICATION_DELETE_MODAL_ID,
            '',
            false,
            NotificationRemoveComponent::getComponentName(),
            $homeModalDelete,
            []
        );
    }

    private function createNotificationListItemsComponentsDto(): array
    {
        $notificationsData = array_map(
            fn (NotificationDataResponse $notificationData): NotificationDataResponse => $this->notificationListOrdersSharedMapper($notificationData),
            $this->listNotificationsData
        );

        return array_map(
            fn (NotificationDataResponse $listItemData) => new NotificationListItemComponentDto(
                NotificationListItemComponent::getComponentName(),
                $listItemData->id,
                self::NOTIFICATION_DELETE_MODAL_ID,
                self::NOTIFICATION_INFO_MODAL_ID,
                self::NOTIFICATION_HOME_LIST_ITEM_COMPONENT_NAME,
                $listItemData->message,
                Config::NOTIFICATION_IMAGE_NO_IMAGE_PUBLIC_PATH_200_200,
                $listItemData->viewed,
                $listItemData->createdOn
            ),
            $notificationsData
        );
    }

    private function notificationListOrdersSharedMapper(NotificationDataResponse $notificationData): NotificationDataResponse
    {
        if ($notificationData->type !== NOTIFICATION_TYPE::SHARE_LIST_ORDERS_CREATED->value) {
            return $notificationData;
        }

        $sharedListOrdersUrl = str_replace(
            self::SHARED_RECOURSE_ID_URL_PLACEHOLDER,
            $notificationData->data['shared_recourse_id'],
            $this->sharedRecourseUrlTemplate
        );
        $notificationMessage = str_replace(
            '{shared_list_orders_url}',
            $sharedListOrdersUrl,
            $notificationData->message
        );

        return new NotificationDataResponse(
            $notificationData->id,
            $notificationData->type,
            $notificationData->userId,
            $notificationMessage,
            $notificationData->data,
            $notificationData->viewed,
            $notificationData->createdOn
        );
    }

    private function createHomeSectionComponentDto(): HomeSectionComponentDto
    {
        return new HomeSectionComponentDto();
    }

    private function createNotificationHomeSectionComponentDto(): NotificationHomeSectionComponentDto
    {
        return (new NotificationHomeSectionComponentDto())
            ->homeSection(
                $this->homeSectionComponentDto
            )
            ->build();
    }
}
