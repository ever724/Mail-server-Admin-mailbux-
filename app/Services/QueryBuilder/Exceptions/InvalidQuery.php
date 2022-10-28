<?php

namespace App\Services\QueryBuilder\Exceptions;

use Symfony\Component\HttpKernel\Exception\HttpException;

abstract class InvalidQuery extends HttpException
{
}
