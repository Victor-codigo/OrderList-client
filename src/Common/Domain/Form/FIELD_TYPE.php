<?php

declare(strict_types=1);

namespace Common\Domain\Form;

enum FIELD_TYPE
{
    case BIRTH_DAY;
    case BUTTON;
    case CHECKBOX;
    case CHOICE;
    case COLLECTION;
    case COLOR;
    case COUNTRY;
    case CURRENCY;
    case DATEINTERVAL;
    case DATETIME;
    case DATE;
    case EMAIL;
    case ENUM;
    case FILE;
    case FORM;
    case HIDDEN;
    case INTEGER;
    case LANGUAGE;
    case LOCALE;
    case MONEY;
    case NUMBER;
    case PASSWORD;
    case PERCENT;
    case RADIO;
    case RANGE;
    case REPEATED;
    case RESET;
    case SEARCH;
    case SUBMIT;
    case TEL;
    case TEXTAREA;
    case TEXT;
    case TIME;
    case TIMEZONE;
    case ULID;
    case URL;
    case UUID;
    case WEEK;
}
