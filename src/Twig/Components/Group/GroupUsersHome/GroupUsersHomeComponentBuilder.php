<?php

declare(strict_types=1);

namespace App\Twig\Components\Group\GroupUsersHome;

use App\Controller\Request\Response\GroupUserDataResponse;
use App\Form\Product\ProductRemoveMulti\PRODUCT_REMOVE_MULTI_FORM_FIELDS;
use App\Twig\Components\Group\GroupUserAdd\GroupUserAddComponent;
use App\Twig\Components\Group\GroupUserAdd\GroupUserAddComponentDto;
use App\Twig\Components\Group\GroupUserRemove\GroupUserRemoveComponent;
use App\Twig\Components\Group\GroupUserRemove\GroupUserRemoveComponentDto;
use App\Twig\Components\Group\GroupUsersHome\Home\GroupUsersHomeSectionComponentDto;
use App\Twig\Components\Group\GroupUsersHome\ListItem\GroupUsersListItemComponent;
use App\Twig\Components\Group\GroupUsersHome\ListItem\GroupUsersListItemComponentDto;
use App\Twig\Components\Group\GroupUsersInfo\GroupUserInfoComponent;
use App\Twig\Components\Group\GroupUsersInfo\GroupUserInfoComponentDto;
use App\Twig\Components\HomeSection\Home\HomeSectionComponentDto;
use App\Twig\Components\HomeSection\Home\RemoveMultiFormDto;
use App\Twig\Components\HomeSection\SearchBar\SECTION_FILTERS;
use App\Twig\Components\HomeSection\SearchBar\SearchBarComponentDto;
use App\Twig\Components\Modal\ModalComponentDto;
use Common\Domain\Config\Config;
use Common\Domain\DtoBuilder\DtoBuilder;
use Common\Domain\DtoBuilder\DtoBuilderInterface;

class GroupUsersHomeComponentBuilder implements DtoBuilderInterface
{
    private const GROUP_USERS_CREATE_MODAL_ID = 'group_users_create_modal';
    private const GROUP_USERS_REMOVE_MULTI_MODAL_ID = 'group_users_remove_multi_modal';
    private const GROUP_USERS_DELETE_MODAL_ID = 'group_users_delete_modal';
    private const GROUP_USERS_INFO_MODAL_ID = 'group_users_info_modal';

    private const GROUP_USERS_HOME_COMPONENT_NAME = 'GroupUsersHomeComponent';
    private const GROUP_USERS_HOME_LIST_COMPONENT_NAME = 'GroupUsersHomeListComponent';
    private const GROUP_USERS_HOME_LIST_ITEM_COMPONENT_NAME = 'GroupUsersHomeListItemComponent';

    private readonly DtoBuilder $builder;
    private readonly HomeSectionComponentDto $homeSectionComponentDto;
    private readonly ModalComponentDto $groupUsersInfoModalDto;

    /**
     * @var GroupUsersDataResponse[]
     */
    private readonly array $listGroupUsersData;

    private readonly bool $userSessionAdmin;
    private readonly string $groupId;

    public function __construct()
    {
        $this->builder = new DtoBuilder([
            'title',
            'groupUserGrants',
            'groupUserAddFormModal',
            'groupUsersRemoveMultiModal',
            'groupUsersRemoveFormModal',
            'errors',
            'pagination',
            'display',
            'listItems',
            'validation',
            'searchBar',
        ]);

        $this->homeSectionComponentDto = $this->createHomeSectionComponentDto();
    }

    public function title(?string $title): self
    {
        $this->builder->setMethodStatus('title', true);

        $this->homeSectionComponentDto->title($title);

        return $this;
    }

    public function groupUserGrants(string $groupId): self
    {
        $this->builder->setMethodStatus('groupUserGrants', true);

        $this->groupId = $groupId;

        return $this;
    }

    public function groupUserAddFormModal(string $groupId, string $groupUsersCreateFormCsrfToken, string $groupUsersCreateFormActionUrl): self
    {
        $this->builder->setMethodStatus('groupUserAddFormModal', true);

        $this->homeSectionComponentDto->createFormModal(
            $this->createGroupUserAddComponentDto($groupId, $groupUsersCreateFormCsrfToken, $groupUsersCreateFormActionUrl)
        );

        return $this;
    }

    public function groupUsersRemoveMultiFormModal(string $groupId, string $groupUsersRemoveMultiFormCsrfToken, string $groupUsersRemoveMultiFormActionUrl): self
    {
        $this->builder->setMethodStatus('groupUsersRemoveMultiModal', true);

        $this->homeSectionComponentDto->removeMultiFormModal(
            $this->createGroupUsersRemoveMultiComponentDto($groupId, $groupUsersRemoveMultiFormCsrfToken, $groupUsersRemoveMultiFormActionUrl)
        );

        return $this;
    }

    public function groupUsersRemoveFormModal(string $groupId, string $groupUsersRemoveFormCsrfToken, string $groupUsersRemoveFormActionUrl): self
    {
        $this->builder->setMethodStatus('groupUsersRemoveFormModal', true);

        $this->homeSectionComponentDto->removeFormModal(
            $this->createGroupUsersRemoveModalDto($groupId, $groupUsersRemoveFormCsrfToken, $groupUsersRemoveFormActionUrl)
        );

        return $this;
    }

    /**
     * @param string[] $groupUsersSectionValidationOk
     * @param string[] $groupUsersValidationErrorsMessage
     */
    public function errors(array $groupUsersSectionValidationOk, array $groupUsersValidationErrorsMessage): self
    {
        $this->builder->setMethodStatus('errors', true);

        $this->homeSectionComponentDto->errors($groupUsersSectionValidationOk, $groupUsersValidationErrorsMessage);

        return $this;
    }

    public function pagination(int $page, int $pageItems, int $pagesTotal): self
    {
        $this->builder->setMethodStatus('pagination', true);

        $this->homeSectionComponentDto->pagination($page, $pageItems, $pagesTotal);

        return $this;
    }

    public function display(bool $headerButtonsHide): self
    {
        $this->builder->setMethodStatus('display', true);

        $this->homeSectionComponentDto->display($headerButtonsHide);

        return $this;
    }

    public function listItems(array $listGroupUsersData, bool $userSessionAdmin): self
    {
        $this->builder->setMethodStatus('listItems', true);

        $this->listGroupUsersData = $listGroupUsersData;
        $this->userSessionAdmin = $userSessionAdmin;

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

    public function searchBar(
        string $groupId,
        ?string $searchValue,
        ?string $sectionFilterValue,
        ?string $nameFilterValue,
        string $searchBarCsrfToken,
        string $searchAutoCompleteUrl,
        string $searchBarFormActionUrl,
    ): self {
        $this->builder->setMethodStatus('searchBar', true);

        $this->homeSectionComponentDto->searchBar(new SearchBarComponentDto(
            $groupId,
            $searchValue,
            [SECTION_FILTERS::GROUP_USERS],
            $sectionFilterValue,
            $nameFilterValue,
            $searchBarCsrfToken,
            $searchBarFormActionUrl,
            $searchAutoCompleteUrl,
        ));

        return $this;
    }

    public function build(): GroupUsersHomeSectionComponentDto
    {
        $this->builder->validate();

        $this->homeSectionComponentDto->translationDomainNames(
            self::GROUP_USERS_HOME_COMPONENT_NAME,
            self::GROUP_USERS_HOME_LIST_COMPONENT_NAME,
            self::GROUP_USERS_HOME_LIST_ITEM_COMPONENT_NAME,
        );
        $this->homeSectionComponentDto->createRemoveMultiForm(
            $this->createRemoveMultiFormDto()
        );
        $this->homeSectionComponentDto->modifyFormModal(null);
        $this->homeSectionComponentDto->listItems(
            GroupUsersListItemComponent::getComponentName(),
            $this->createGroupUsersListItemsComponentsDto($this->userSessionAdmin),
            Config::PRODUCT_IMAGE_NO_IMAGE_PUBLIC_PATH_200_200
        );

        $this->groupUsersInfoModalDto = $this->createGroupUsersInfoModalDto();

        return $this->createGroupUsersHomeSectionComponentDto($this->groupId, $this->groupUsersInfoModalDto);
    }

    private function createGroupUserAddComponentDto(string $groupId, string $groupUserAddFormCsrfToken, string $groupUserAddFormActionUrl): ModalComponentDto
    {
        $homeSectionCreateComponentDto = new GroupUserAddComponentDto(
            [],
            '',
            $groupId,
            '',
            $groupUserAddFormCsrfToken,
            false,
            mb_strtolower($groupUserAddFormActionUrl),
        );

        return new ModalComponentDto(
            self::GROUP_USERS_CREATE_MODAL_ID,
            '',
            false,
            GroupUserAddComponent::getComponentName(),
            $homeSectionCreateComponentDto,
            []
        );
    }

    private function createGroupUsersRemoveMultiComponentDto(string $groupId, string $groupUsersRemoveMultiFormCsrfToken, string $groupUsersRemoveFormActionUrl): ModalComponentDto
    {
        $homeSectionRemoveMultiComponentDto = new GroupUserRemoveComponentDto(
            GroupUserRemoveComponent::getComponentName(),
            [],
            $groupId,
            $groupUsersRemoveMultiFormCsrfToken,
            mb_strtolower($groupUsersRemoveFormActionUrl),
            true,
        );

        return new ModalComponentDto(
            self::GROUP_USERS_REMOVE_MULTI_MODAL_ID,
            '',
            false,
            GroupUserRemoveComponent::getComponentName(),
            $homeSectionRemoveMultiComponentDto,
            []
        );
    }

    private function createGroupUsersRemoveModalDto(string $groupId, string $groupUsersRemoveFormCsrfToken, string $groupUsersRemoveFormActionUrl): ModalComponentDto
    {
        $homeModalDelete = new GroupUserRemoveComponentDto(
            GroupUserRemoveComponent::getComponentName(),
            [],
            $groupId,
            $groupUsersRemoveFormCsrfToken,
            mb_strtolower($groupUsersRemoveFormActionUrl),
            false,
        );

        return new ModalComponentDto(
            self::GROUP_USERS_DELETE_MODAL_ID,
            '',
            false,
            GroupUserRemoveComponent::getComponentName(),
            $homeModalDelete,
            []
        );
    }

    private function createRemoveMultiFormDto(): RemoveMultiFormDto
    {
        return new RemoveMultiFormDto(
            PRODUCT_REMOVE_MULTI_FORM_FIELDS::FORM,
            sprintf('%s[%s]', PRODUCT_REMOVE_MULTI_FORM_FIELDS::FORM, PRODUCT_REMOVE_MULTI_FORM_FIELDS::TOKEN),
            sprintf('%s[%s]', PRODUCT_REMOVE_MULTI_FORM_FIELDS::FORM, PRODUCT_REMOVE_MULTI_FORM_FIELDS::SUBMIT),
            sprintf('%s[%s][]', PRODUCT_REMOVE_MULTI_FORM_FIELDS::FORM, PRODUCT_REMOVE_MULTI_FORM_FIELDS::PRODUCTS_ID),
            self::GROUP_USERS_REMOVE_MULTI_MODAL_ID
        );
    }

    private function createGroupUsersListItemsComponentsDto(bool $userSessionAdmin): array
    {
        return array_map(
            fn (GroupUserDataResponse $listItemData) => new GroupUsersListItemComponentDto(
                GroupUsersListItemComponent::getComponentName(),
                $listItemData->id,
                $listItemData->name,
                self::GROUP_USERS_DELETE_MODAL_ID,
                self::GROUP_USERS_INFO_MODAL_ID,
                self::GROUP_USERS_HOME_LIST_ITEM_COMPONENT_NAME,
                $listItemData->image ?? Config::USER_IMAGE_NO_IMAGE_PUBLIC_PATH_200_200,
                null === $listItemData->image ? true : false,
                $listItemData->admin,
                $userSessionAdmin
            ),
            $this->listGroupUsersData
        );
    }

    private function createGroupUsersInfoModalDto(): ModalComponentDto
    {
        $groupUsersInfoComponentDto = new GroupUserInfoComponentDto(
            GroupUserInfoComponent::getComponentName()
        );

        return new ModalComponentDto(
            self::GROUP_USERS_INFO_MODAL_ID,
            '',
            false,
            GroupUserInfoComponent::getComponentName(),
            $groupUsersInfoComponentDto,
            []
        );
    }

    private function createHomeSectionComponentDto(): HomeSectionComponentDto
    {
        return new HomeSectionComponentDto();
    }

    private function createGroupUsersHomeSectionComponentDto(string $groupId, ModalComponentDto $groupUsersInfoModalDto): GroupUsersHomeSectionComponentDto
    {
        return (new GroupUsersHomeSectionComponentDto())
            ->groupData(
                $groupId
            )
            ->homeSection(
                $this->homeSectionComponentDto
            )
            ->groupUsersInfoModal(
                $groupUsersInfoModalDto
            )
            ->build();
    }
}
