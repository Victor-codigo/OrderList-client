<?php

declare(strict_types=1);

namespace Common\Domain\JwtToken\Exception;

use Common\Adapter\Events\Exceptions\AccessDeniedException;

class JwtTokenGetPayLoadException extends AccessDeniedException
{
}
