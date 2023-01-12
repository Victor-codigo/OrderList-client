<?php

declare(strict_types=1);

namespace App\Form\Profile;

enum PROFILE_FORM_ERRORS: string
{
    case NAME = 'name';
    case IMAGE = 'image';
    case INTERNAL_SERVER = 'networkError';
}
