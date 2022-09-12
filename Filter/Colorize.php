<?php

namespace Modules\ImageFilter\Filter;

use Intervention\Image\Image;
use Modules\ImageFilter\Exceptions\InvalidParameterFilter;

class Colorize implements Filter
{
    protected int $red;
    protected int $green;
    protected int $blue;

    public function __construct(array $parameters)
    {
        $this->red = isset($parameters['red']) ? (int)$parameters['red'] : 0;
        $this->green = isset($parameters['green']) ? (int)$parameters['green'] : 0;
        $this->blue = isset($parameters['blue']) ? (int)$parameters['blue'] : 0;
    }

    public function apply(Image $image): Image
    {
        $image->colorize($this->red, $this->green, $this->blue);
        return $image;
    }
}
