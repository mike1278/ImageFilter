<?php

namespace Modules\ImageFilter\Filter;

use Intervention\Image\Image;
use Modules\ImageFilter\Exceptions\InvalidParameterFilter;

class Colorize implements Filter
{
    protected int $red;
    protected int $green;
    protected int $blue;

    public function __construct(string $parameters)
    {
        $array = explode(',', $parameters);
        $this->red = isset($array[0]) ? (int)$array[0] : 0;
        $this->green = isset($array[1]) ? (int)$array[1] : 0;
        $this->blue = isset($array[2]) ? (int)$array[2] : 0;
    }

    public function apply(Image $image): Image
    {
        $image->colorize($this->red, $this->green, $this->blue);
        return $image;
    }
}
