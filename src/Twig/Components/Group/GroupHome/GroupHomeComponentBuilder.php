<?php

declare(strict_types=1);

namespace App\Twig\Components\Group\GroupHome;

use App\Controller\Request\Response\GroupDataResponse;
use App\Form\Group\GroupRemoveMulti\GROUP_REMOVE_MULTI_FORM_FIELDS;
use App\Twig\Components\Group\GroupCreate\GroupCreateComponent;
use App\Twig\Components\Group\GroupCreate\GroupCreateComponentDto;
use App\Twig\Components\Group\GroupHome\Home\GroupHomeSectionComponentDto;
use App\Twig\Components\Group\GroupHome\ListItem\GroupListItemComponent;
use App\Twig\Components\Group\GroupHome\ListItem\GroupListItemComponentDto;
use App\Twig\Components\Group\GroupInfo\GroupInfoComponent;
use App\Twig\Components\Group\GroupInfo\GroupInfoComponentDto;
use App\Twig\Components\Group\GroupModify\GroupModifyComponent;
use App\Twig\Components\Group\GroupModify\GroupModifyComponentDto;
use App\Twig\Components\Group\GroupRemove\GroupRemoveComponent;
use App\Twig\Components\Group\GroupRemove\GroupRemoveComponentDto;
use App\Twig\Components\HomeSection\Home\HomeSectionComponentDto;
use App\Twig\Components\HomeSection\Home\RemoveMultiFormDto;
use App\Twig\Components\HomeSection\SearchBar\SearchBarComponentDto;
use App\Twig\Components\HomeSection\SearchBar\SECTION_FILTERS;
use App\Twig\Components\Modal\ModalComponentDto;
use Common\Domain\Config\Config;
use Common\Domain\DtoBuilder\DtoBuilder;
use Common\Domain\DtoBuilder\DtoBuilderInterface;

class GroupHomeComponentBuilder implements DtoBuilderInterface
{
    private const GROUP_CREATE_MODAL_ID = 'group_create_modal';
    private const GROUP_REMOVE_MULTI_MODAL_ID = 'group_remove_multi_modal';
    private const GROUP_DELETE_MODAL_ID = 'group_delete_modal';
    private const GROUP_MODIFY_MODAL_ID = 'group_modify_modal';
    private const GROUP_INFO_MODAL_ID = 'group_info_modal';

    private const GROUP_HOME_COMPONENT_NAME = 'GroupHomeComponent';
    private const GROUP_HOME_LIST_COMPONENT_NAME = 'GroupHomeListComponent';
    private const GROUP_HOME_LIST_ITEM_COMPONENT_NAME = 'GroupHomeListItemComponent';

    private readonly DtoBuilder $builder;
    private readonly HomeSectionComponentDto $homeSectionComponentDto;
    private readonly ModalComponentDto $groupInfoModalDto;

    /**
     * @var GroupDataResponse[]
     */
    private readonly array $listGroupsData;

    public function __construct()
    {
        $this->builder = new DtoBuilder([
            'title',
            'groupCreateModal',
            'groupModifyFormModal',
            'groupRemoveMultiModal',
            'groupRemoveFormModal',
            'errors',
            'pagination',
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

    public function groupCreateFormModal(string $groupCreateFormCsrfToken, string $groupCreateFormActionUrl): self
    {
        $this->builder->setMethodStatus('groupCreateModal', true);

        $this->homeSectionComponentDto->createFormModal(
            $this->createGroupCreateComponentDto($groupCreateFormCsrfToken, $groupCreateFormActionUrl)
        );

        return $this;
    }

    public function groupModifyFormModal(string $groupModifyFormCsrfToken, string $groupModifyFormActionUrlPlaceholder): self
    {
        $this->builder->setMethodStatus('groupModifyFormModal', true);

        $this->homeSectionComponentDto->modifyFormModal(
            $this->createGroupModifyModalDto($groupModifyFormCsrfToken, $groupModifyFormActionUrlPlaceholder)
        );

        return $this;
    }

    public function groupRemoveMultiFormModal(string $groupRemoveMultiFormCsrfToken, string $groupRemoveMultiFormActionUrl): self
    {
        $this->builder->setMethodStatus('groupRemoveMultiModal', true);

        $this->homeSectionComponentDto->removeMultiFormModal(
            $this->createGroupRemoveMultiComponentDto($groupRemoveMultiFormCsrfToken, $groupRemoveMultiFormActionUrl)
        );

        return $this;
    }

    public function groupRemoveFormModal(string $groupRemoveFormCsrfToken, string $groupRemoveFormActionUrl): self
    {
        $this->builder->setMethodStatus('groupRemoveFormModal', true);

        $this->homeSectionComponentDto->removeFormModal(
            $this->createGroupRemoveModalDto($groupRemoveFormCsrfToken, $groupRemoveFormActionUrl)
        );

        return $this;
    }

    /**
     * @param string[] $groupSectionValidationOk
     * @param string[] $groupValidationErrorsMessage
     */
    public function errors(array $groupSectionValidationOk, array $groupValidationErrorsMessage): self
    {
        $this->builder->setMethodStatus('errors', true);

        $this->homeSectionComponentDto->errors($groupSectionValidationOk, $groupValidationErrorsMessage);

        return $this;
    }

    public function pagination(int $page, int $pageItems, int $pagesTotal): self
    {
        $this->builder->setMethodStatus('pagination', true);

        $this->homeSectionComponentDto->pagination($page, $pageItems, $pagesTotal);

        return $this;
    }

    public function listItems(array $listGroupsData): self
    {
        $this->builder->setMethodStatus('listItems', true);

        $this->listGroupsData = $listGroupsData;

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
        ?string $searchValue,
        ?string $sectionFilterValue,
        ?string $nameFilterValue,
        string $searchBarCsrfToken,
        string $searchAutoCompleteUrl,
        string $searchBarFormActionUrl,
    ): self {
        $this->builder->setMethodStatus('searchBar', true);

        $this->homeSectionComponentDto->searchBar(new SearchBarComponentDto(
            '',
            $searchValue,
            [SECTION_FILTERS::GROUP],
            $sectionFilterValue,
            $nameFilterValue,
            $searchBarCsrfToken,
            $searchBarFormActionUrl,
            $searchAutoCompleteUrl,
        ));

        return $this;
    }

    public function build(): GroupHomeSectionComponentDto
    {
        $this->builder->validate();

        $this->homeSectionComponentDto->translationDomainNames(
            self::GROUP_HOME_COMPONENT_NAME,
            self::GROUP_HOME_LIST_COMPONENT_NAME,
            self::GROUP_HOME_LIST_ITEM_COMPONENT_NAME,
        );
        $this->homeSectionComponentDto->createRemoveMultiForm(
            $this->createRemoveMultiFormDto()
        );
        $this->homeSectionComponentDto->listItems(
            GroupListItemComponent::getComponentName(),
            $this->createGroupListItemsComponentsDto(),
            Config::GROUP_IMAGE_NO_IMAGE_PUBLIC_PATH_200_200
        );

        $this->groupInfoModalDto = $this->createGroupInfoModalDto();

        return $this->createGroupHomeSectionComponentDto($this->groupInfoModalDto);
    }

    private function createGroupCreateComponentDto(string $groupCreateFormCsrfToken, string $groupCreateFormActionUrl): ModalComponentDto
    {
        $homeSectionCreateComponentDto = new GroupCreateComponentDto(
            [],
            '',
            '',
            $groupCreateFormCsrfToken,
            false,
            mb_strtolower($groupCreateFormActionUrl),
        );

        return new ModalComponentDto(
            self::GROUP_CREATE_MODAL_ID,
            '',
            false,
            GroupCreateComponent::getComponentName(),
            $homeSectionCreateComponentDto,
            []
        );
    }

    private function createGroupRemoveMultiComponentDto(string $groupRemoveMultiFormCsrfToken, string $groupRemoveFormActionUrl): ModalComponentDto
    {
        $homeSectionRemoveMultiComponentDto = new GroupRemoveComponentDto(
            GroupRemoveComponent::getComponentName(),
            [],
            $groupRemoveMultiFormCsrfToken,
            mb_strtolower($groupRemoveFormActionUrl),
            true,
        );

        return new ModalComponentDto(
            self::GROUP_REMOVE_MULTI_MODAL_ID,
            '',
            false,
            GroupRemoveComponent::getComponentName(),
            $homeSectionRemoveMultiComponentDto,
            []
        );
    }

    private function createGroupRemoveModalDto(string $groupRemoveFormCsrfToken, string $groupRemoveFormActionUrl): ModalComponentDto
    {
        $homeModalDelete = new GroupRemoveComponentDto(
            GroupRemoveComponent::getComponentName(),
            [],
            $groupRemoveFormCsrfToken,
            mb_strtolower($groupRemoveFormActionUrl),
            false
        );

        return new ModalComponentDto(
            self::GROUP_DELETE_MODAL_ID,
            '',
            false,
            GroupRemoveComponent::getComponentName(),
            $homeModalDelete,
            []
        );
    }

    private function createRemoveMultiFormDto(): RemoveMultiFormDto
    {
        return new RemoveMultiFormDto(
            GROUP_REMOVE_MULTI_FORM_FIELDS::FORM,
            sprintf('%s[%s]', GROUP_REMOVE_MULTI_FORM_FIELDS::FORM, GROUP_REMOVE_MULTI_FORM_FIELDS::TOKEN),
            sprintf('%s[%s]', GROUP_REMOVE_MULTI_FORM_FIELDS::FORM, GROUP_REMOVE_MULTI_FORM_FIELDS::SUBMIT),
            sprintf('%s[%s][]', GROUP_REMOVE_MULTI_FORM_FIELDS::FORM, GROUP_REMOVE_MULTI_FORM_FIELDS::GROUPS_ID),
            self::GROUP_REMOVE_MULTI_MODAL_ID
        );
    }

    private function createGroupModifyModalDto(string $groupModifyFormCsrfToken, string $groupModifyFormActionUrl): ModalComponentDto
    {
        $homeModalModify = new GroupModifyComponentDto(
            [],
            '{name_placeholder}',
            '{description_placeholder}',
            '{image_placeholder}',
            Config::GROUP_IMAGE_NO_IMAGE_PUBLIC_PATH_200_200,
            $groupModifyFormCsrfToken,
            mb_strtolower($groupModifyFormActionUrl),
            self::GROUP_MODIFY_MODAL_ID,
            false
        );

        return new ModalComponentDto(
            self::GROUP_MODIFY_MODAL_ID,
            '',
            false,
            GroupModifyComponent::getComponentName(),
            $homeModalModify,
            [],
        );
    }

    private function createGroupListItemsComponentsDto(): array
    {
        return array_map(
            fn (GroupDataResponse $listItemData) => new GroupListItemComponentDto(
                GroupListItemComponent::getComponentName(),
                $listItemData->id,
                $listItemData->name,
                self::GROUP_MODIFY_MODAL_ID,
                self::GROUP_DELETE_MODAL_ID,
                self::GROUP_INFO_MODAL_ID,
                self::GROUP_HOME_LIST_ITEM_COMPONENT_NAME,
                $listItemData->description,
                $listItemData->image ?? Config::GROUP_IMAGE_NO_IMAGE_PUBLIC_PATH_200_200,
                null === $listItemData->image ? true : false,
                $listItemData->createdOn,
                $listItemData->admin,
            ),
            $this->listGroupsData
        );
    }

    private function createGroupInfoModalDto(): ModalComponentDto
    {
        $groupInfoComponentDto = new GroupInfoComponentDto(
            GroupInfoComponent::getComponentName()
        );

        return new ModalComponentDto(
            self::GROUP_INFO_MODAL_ID,
            '',
            false,
            GroupInfoComponent::getComponentName(),
            $groupInfoComponentDto,
            []
        );
    }

    private function createHomeSectionComponentDto(): HomeSectionComponentDto
    {
        return new HomeSectionComponentDto();
    }

    private function createGroupHomeSectionComponentDto(ModalComponentDto $groupInfoModalDto): GroupHomeSectionComponentDto
    {
        return (new GroupHomeSectionComponentDto())
            ->homeSection(
                $this->homeSectionComponentDto
            )
            ->groupUsersInfoModal(
                $groupInfoModalDto
            )
            ->build();
    }
}
