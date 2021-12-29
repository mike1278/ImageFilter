<?php

namespace Modules\ImageFilter\Service;

use Intervention\Image\Image;

class CachedImage extends Image
{
    public function setFromOriginal(Image $original, $cachekey): static
    {
        $this->driver = $original->driver;
        $this->core = $original->core;
        $this->backups = $original->backups;
        $this->encoded = $original->encoded;
        $this->mime = $original->mime;
        $this->dirname = $original->dirname;
        $this->basename = $original->basename;
        $this->extension = $original->extension;
        $this->filename = $original->filename;
        $this->cachekey = $cachekey;

        return $this;
    }
}
