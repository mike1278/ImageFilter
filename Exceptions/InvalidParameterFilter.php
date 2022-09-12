<?php

namespace Modules\ImageFilter\Exceptions;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class InvalidParameterFilter extends HttpException
{

    public function __construct(bool $minimumParameter, bool $invalidParameter, string $filterName)
    {
        $message = '';
        if ($minimumParameter) {
            $message = 'Minimum number of parameters not met';
        }
        if ($invalidParameter) {
            $message = ($message != '' ?: ', ') . 'Invalid parameters';
        }
        $message .= ' filter ' . $filterName;

        parent::__construct(Response::HTTP_BAD_REQUEST, $message);
    }
}
