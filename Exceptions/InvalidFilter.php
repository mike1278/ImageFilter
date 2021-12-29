<?php

namespace Modules\ImageFilter\Exceptions;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class InvalidFilter extends HttpException
{
    public string $unknownFilter;

    public function __construct(string $unknownFilter)
    {
        $this->unknownFilter = $unknownFilter;

        $message = "Requested filter `{$unknownFilter}` are not allowed.";

        parent::__construct(Response::HTTP_BAD_REQUEST, $message);
    }

    public static function filtersNotAllowed(string $unknownFilter): static
    {
        return new static(...func_get_args());
    }
}
