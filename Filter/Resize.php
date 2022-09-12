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

    public function __construct(array $parameters)
    {
        $this->width = isset($parameters['width']) ? (int)$parameters['width'] : null;
        $this->height = isset($parameters['height']) ? (int)$parameters['height'] : null;
        $this->aspectRatio = !isset($parameters['aspectRatio']) || $parameters['aspectRatio'] == 'true' || $parameters['aspectRatio'] == 1;
        $this->upsize = isset($parameters['upsize']) && ($parameters['upsize'] == 'true' || $parameters['upsize'] == 1);
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
