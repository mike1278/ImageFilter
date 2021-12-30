<?php

namespace Modules\ImageFilter\Service;

use Config;
use Intervention\Image\Image;
use Intervention\Image\ImageManager;
use Intervention\Image\ImageManagerStatic;
use Modules\ImageFilter\Exceptions\InvalidFilter;
use Modules\ImageFilter\Filter\Filter;
use ReflectionClass;
use function Clue\StreamFilter\fun;

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
        $configFilters = $object->getConfigFilters();

        foreach ($filters as $key => $filter) {
            if (isset($configFilters[$key])) {
                $object->getFilter($configFilters[$key], $filter)->apply($image);
            } elseif (config('image-filter.disable_invalid_filter_query_exception')) {
                throw new InvalidFilter($key);
            }
        }

        return $image;
    }

    public function getConfigFilters(): array
    {
        $filters = collect(config('image-filter.filters'));
        return $filters->map(function ($filter) {
            return is_subclass_of($filter, Filter::class);
        })->toArray();
    }
}
