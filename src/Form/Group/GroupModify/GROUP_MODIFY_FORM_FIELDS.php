<?php

declare(strict_types=1);

namespace App\Form\Group\GroupModify;

use Common\Domain\Form\FormFieldInterface;

final class GROUP_MODIFY_FORM_FIELDS implements FormFieldInterface
{
    public const FORM = 'group_modify_form';
    public const GROUP_ID = 'group_id';
    public const NAME = 'name';
    public const DESCRIPTION = 'description';
    public const IMAGE = 'image';
    public const IMAGE_REMOVE = 'image_remove';
    public const TOKEN = 'token';
    public const SUBMIT = 'submit';
}
