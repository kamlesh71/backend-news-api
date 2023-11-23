<?php

namespace App\Enums;

enum NewsDataSource: string
{
    case NEWS_API = 'news';
    case GUARDIAN_API = 'guardian';

    case NEWYORK_TIMES = 'newyork times';
}
