<?php

declare(strict_types=1);

namespace App\Form\Group\GroupUserRemove;

use Common\Domain\Form\FormFieldInterface;

final class GROUP_USER_REMOVE_FORM_FIELDS implements FormFieldInterface
{
    public const FORM = 'group_user_remove_form';
    public const GROUP_ID = 'group_id';
    public const USER_ID = 'users';
    public const TOKEN = 'token';
    public const SUBMIT = 'submit';
}
