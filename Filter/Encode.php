<?php

namespace Modules\ImageFilter\Filter;

use Intervention\Image\Image;
use Modules\ImageFilter\Exceptions\InvalidParameterFilter;

class Encode implements Filter
{

    private string $encode;
    private int $quality;

    public function __construct(array $parameters)
    {
        if (!$parameters['encode'] && !$this->isValidEncode($parameters['encode'])) {
            throw new InvalidParameterFilter(true, true, 'Encode');
        }
        $this->encode = $parameters['encode'];
        $this->quality = (int)($parameters['quality'] ?? 100);
    }

    public function apply(Image $image): Image
    {
        return $image->encode($this->encode, $this->quality);
    }

    public function isValidEncode($encode): bool
    {
        return in_array($encode, [
            'jpg',
            'png',
            'gif',
            'tif',
            'bmp',
            'ico',
            'psd',
            'webp',
            'data-url',
        ]);
    }
}
