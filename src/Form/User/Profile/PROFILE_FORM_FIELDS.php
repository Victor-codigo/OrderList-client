<?php

declare(strict_types=1);

namespace App\Form\User\Profile;

use Common\Domain\Form\FormErrorInterface;

class PROFILE_FORM_FIELDS implements FormErrorInterface
{
    public const FORM = 'profile_form';
    public const TOKEN = 'token';
    public const EMAIL = 'email';
    public const PASSWORD = 'password';
    public const NICK = 'nick';
    public const IMAGE = 'image';
    public const IMAGE_REMOVE = 'image_remove';
    public const SUBMIT = 'save';
}
