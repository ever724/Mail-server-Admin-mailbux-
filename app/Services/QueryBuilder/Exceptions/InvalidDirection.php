<?php

namespace App\Services\QueryBuilder\Exceptions;

use App\Services\QueryBuilder\Enums\SortDirection;
use Exception;

class InvalidDirection extends Exception
{
    public static function make(string $sort)
    {
        return new static('The direction should be either `' . SortDirection::DESCENDING . '` or `' . SortDirection::ASCENDING) . "`. {$sort} given.";
    }
}
