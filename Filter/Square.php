<?php

namespace Modules\ImageFilter\Filter;

use Intervention\Image\Image;
use Intervention\Image\ImageManager;

class Square implements Filter
{
    protected bool $cut;

    public function __construct(array $parameters)
    {
        $this->cut = isset($parameters['cut']) && (bool)$parameters['cut'];
    }

    public function apply(Image $image): Image
    {
        $size = $image->width() > $image->height() ? $image->width() : $image->height();
        $canvas = (new ImageManager(config('image')))->canvas($size, $size, '#ffffff');
        return $canvas->insert($image, 'center');
    }
}
