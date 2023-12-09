<?php

declare(strict_types=1);

namespace Common\Domain\DtoBuilder;

interface DtoBuilderInterface
{
    public function build(): self;
}
