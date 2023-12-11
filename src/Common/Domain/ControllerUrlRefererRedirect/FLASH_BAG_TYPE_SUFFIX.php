<?php

declare(strict_types=1);

namespace Common\Domain\ControllerUrlRefererRedirect;

enum FLASH_BAG_TYPE_SUFFIX: STRING
{
    case MESSAGE_OK = '-ok';
    case MESSAGE_ERROR = '-error';
    case DATA = '-data';
}
