<?php

namespace Modules\ImageFilter\Service;

use Config;
use Intervention\Image\Image;
use Intervention\Image\ImageManager;
use Intervention\Image\ImageManagerStatic;
use Modules\ImageFilter\Exceptions\InvalidFilter;
use Modules\ImageFilter\Filter\Filter;

class ImageFilter
{
    public function getFilter($filter, $parameters): Filter
    {
        return new $filter($parameters ?? '');
    }

    public function getRequestData()
    {
        if (config('query-builder.request_data_source') === 'body') {
            return request()->input('filters');
        }

        return request()->query('filters');
    }

    public static function make(string $path): Image
    {
        $object = new self();
        $image = (new ImageManager(Config::get('image')))->make($path);
        $filters = $object->getRequestData() ?? [];
        $configFilters = config('image-filter.filters');
        foreach ($filters as $key => $filter) {
            if (isset($configFilters[$key])) {
                $filter = $object->getFilter($configFilters[$key], $filter);
                $filter->apply($image);
            } elseif (config('image-filter.disable_invalid_filter_query_exception')) {
                throw new InvalidFilter($key);
            }
        }
        return $image;
    }
}