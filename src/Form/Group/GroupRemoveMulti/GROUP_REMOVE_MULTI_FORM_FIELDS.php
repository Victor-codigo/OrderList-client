<?php

declare(strict_types=1);

namespace App\Form\Group\GroupRemoveMulti;

use Common\Domain\Form\FormFieldInterface;

final class GROUP_REMOVE_MULTI_FORM_FIELDS implements FormFieldInterface
{
    public const FORM = 'group_remove_multi_form';
    public const GROUPS_ID = 'groups_id';
    public const TOKEN = 'token';
    public const SUBMIT = 'submit';
}
