<?php

declare(strict_types=1);

namespace Common\Domain\Config;

enum UNIT_MEASURE: string
{
    case UNITS = 'unit';

    case KG = 'Kg';
    case G = 'g';
    case CG = 'cg';

    case M = 'm';
    case DM = 'dm';
    case CM = 'cm';
    case MM = 'mm';

    case L = 'l';
    case DL = 'dl';
    case CL = 'cl';
    case ML = 'ml';
}
