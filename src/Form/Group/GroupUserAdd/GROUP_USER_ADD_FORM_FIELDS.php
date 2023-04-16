<?php

declare(strict_types=1);

namespace App\Form\Group\GroupUserAdd;

use Common\Domain\Form\FormFieldInterface;

final class GROUP_USER_ADD_FORM_FIELDS implements FormFieldInterface
{
    public const FORM = 'group_user_add_form';
    public const GROUP_ID = 'group_id';
    public const NAME = 'name';
    public const TOKEN = 'token';
    public const SUBMIT = 'submit';
}
