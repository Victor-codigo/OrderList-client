<?php

declare(strict_types=1);

namespace Common\Domain\Ports\Form;

use Common\Domain\Form\FormType;
use Symfony\Component\HttpFoundation\Request;

interface FormFactoryInterface
{
    public function create(FormType $formType, Request|null $request = null): FormInterface;
}
