<?php

namespace Modules\ImageFilter\Filter;

use Intervention\Image\Image;
use Modules\ImageFilter\Exceptions\InvalidParameterFilter;

class Resize implements Filter
{
    protected null|int $height;
    protected null|int $width;
    protected bool $aspectRatio;
    protected bool $upsize;

    public function __construct(string $parameters)
    {
        $array = explode(',', $parameters);
        $this->width = isset($array[0]) ? (int)$array[0] : null;
        $this->height = isset($array[1]) ? (int)$array[1] : null;
        $this->aspectRatio = isset($array[2]) && ($array[2] == 'true' || $array[2] == 1);
        $this->upsize = isset($array[3]) && ($array[3] == 'true' || $array[3] == 1);
        if ($this->height == 0) {
            $this->height = null;
        }
        if ($this->width == 0) {
            $this->width = null;
        }
        if (!$this->height && !$this->width) {
            throw new InvalidParameterFilter(true, true, 'resize');
        }
    }

    public function apply(Image $image): Image
    {
        $image->resize($this->width, $this->height, function ($constraint) {
            if ($this->aspectRatio) {
                $constraint->aspectRatio();
            }
            if ($this->upsize) {
                $constraint->upsize();
            }
        });
        return $image;
    }
}
