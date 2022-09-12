<?php

namespace Modules\ImageFilter\Service;

use Carbon\Carbon;
use Closure;
use Illuminate\Cache\CacheManager;
use Illuminate\Cache\FileStore;
use Illuminate\Cache\Repository;
use Illuminate\Cache\Repository as Cache;
use Illuminate\Filesystem\Filesystem;
use Intervention\Image\Exception\MissingDependencyException;
use Intervention\Image\Image;
use Intervention\Image\ImageManager;

class ImageCache
{
    /**
     * Cache lifetime in minutes
     *
     * @var integer
     */
    public int $lifetime = 5;

    /**
     * History of name and arguments of calls performed on image
     *
     * @var array
     */
    public array $calls = [];

    /**
     * Additional properties included in checksum
     *
     * @var array
     */
    public array $properties = [];

    /**
     * Processed Image
     *
     * @var Image
     */
    public Image $image;

    /**
     * Intervention Image Manager
     *
     * @var ImageManager
     */
    public ImageManager $manager;

    /**
     * Illuminate Cache Manager
     *
     * @var CacheManager
     */
    public $cache;

    public function __construct(ImageManager $manager = null, Cache $cache = null)
    {
        $this->manager = $manager ?: new ImageManager();

        if (is_null($cache)) {

            $app = function_exists('app') ? app() : null;

            if (is_a($app, 'Illuminate\Foundation\Application')) {
                $cache = $app->make('cache');
            }

            if (is_a($cache, 'Illuminate\Cache\CacheManager')) {
                $cache_driver = config('image-filter.cache_driver');
                $this->cache = $cache_driver ? $cache->driver($cache_driver) : $cache;
            } else {
                // define path in filesystem
                $path = $manager->config['cache']['path'] ?? storage_path('/cache');

                $filesystem = new Filesystem();
                $storage = new FileStore($filesystem, $path);
                $this->cache = new Repository($storage);
            }
        } else {
            $this->cache = $cache;
        }
    }

    /**
     * Set custom property to be included in checksum
     *
     * @param mixed $key
     * @param mixed $value
     * @return ImageCache
     */
    public function setProperty(mixed $key, mixed $value): static
    {
        $this->properties[$key] = $value;

        return $this;
    }

    /**
     * Checks if given data is file, handles mixed input
     *
     * @param mixed $value
     * @return boolean
     */
    protected function isFile(mixed $value): bool
    {
        $value = strval(str_replace("\0", "", $value));

        return strlen($value) <= PHP_MAXPATHLEN && is_file($value);
    }

    protected function getKey(): string
    {
        return md5(
            url()->full()
        );
    }

    /**
     * Special make method to add modified data to checksum
     *
     * @param  mixed $data
     * @return ImageCache
     */
    public function make(mixed $data): static
    {
        // include "modified" property for any files
        if ($this->isFile($data)) {
            $this->setProperty('modified', filemtime((string) $data));
        }

        return $this;
    }

    /**
     * Get image either from cache or directly processed
     * and save image in cache if it's not saved yet
     *
     * @param bool $returnObj
     * @return mixed
     */
    public function get(bool $returnObj = false): mixed
    {
        $key = $this->getKey();

        $cachedImageData = $this->cache->get($key);

        // if image data exists in cache
        if ($cachedImageData) {
            if ($returnObj) {
                $image = $this->manager->make($cachedImageData);
                return (new CachedImage())->setFromOriginal($image, $key);
            }
            // return raw data
            return $cachedImageData;
        } else {
            return null;
        }
    }

    public function set(Image $image, int $lifetime = null, bool $returnObj = true)
    {
        if (!config('image-filter.cache_active')) {
            return $image->encode();
        }
        $key = $this->getKey();

        $encoded = $image->encoded ?: (string)$image->encode();

        $this->cache->put($key, $encoded, Carbon::now()->addMinutes($lifetime));

        return $returnObj ? $image : $encoded;
    }

    public function cache(Closure $callback, int $lifetime = null)
    {
        $image = $this->get();
        if (!$image) {
            $image = $this->set($callback(), $lifetime);
        }
        return $image;
    }
}
