<?php

declare(strict_types=1);

namespace App\Tests\Common\Adapter\Form\Fixtures;

use Common\Domain\Form\FormErrorInterface;

enum FORM_ERRORS: string implements FormErrorInterface
{
    case FORM_ERROR_1 = 'form_error_1';
    case FORM_ERROR_2 = 'form_error_2';
    case FORM_ERROR_3 = 'form_error_3';
}
