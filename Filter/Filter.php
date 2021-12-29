<?php

namespace Modules\ImageFilter\Filter;

use Intervention\Image\Image;

interface Filter {
    public function __construct(string $parameters);

    public function apply(Image $image): Image;
}
