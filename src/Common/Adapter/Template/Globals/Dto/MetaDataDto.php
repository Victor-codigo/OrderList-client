<?php

declare(strict_types=1);

namespace Common\Adapter\Template\Globals\Dto;

readonly class MetaDataDto
{
    public function __construct(
        public string $description
    ) {
    }
}
