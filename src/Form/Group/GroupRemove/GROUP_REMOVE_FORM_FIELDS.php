<?php

declare(strict_types=1);

namespace App\Form\Group\GroupRemove;

use Common\Domain\Form\FormFieldInterface;

final class GROUP_REMOVE_FORM_FIELDS implements FormFieldInterface
{
    public const FORM = 'group_remove_form';
    public const GROUP_ID = 'group_id';
    public const TOKEN = 'token';
    public const SUBMIT = 'submit';
}
