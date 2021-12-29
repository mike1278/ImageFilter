<?php

namespace Modules\ImageFilter\Http\Controllers;

use Config;
use Illuminate\Http\Response as IlluminateResponse;
use Illuminate\Routing\Controller;
use Intervention\Image\ImageManager;
use Modules\ImageFilter\Service\ImageCache;
use Modules\ImageFilter\Service\ImageFilter;

class ImageFilterCacheController extends Controller
{
    public function __invoke(string $filename): IlluminateResponse
    {
        abort_unless(
            class_exists('Intervention\Image\ImageManager'),
            500,
            'Need Intervention image'
        );
        $response = $this->getImage($filename);
        if (request()->has('image[download]')) {
            $this->getDownload($response, $filename);
        }
        return $response;
    }

    public function getImage($filename): IlluminateResponse
    {
        $path = $this->getImagePath($filename);

        $manager = new ImageManager(Config::get('image'));
        $content = (new ImageCache($manager))->cache(function () use ($path) {
            return ImageFilter::make($path);
        }, config('image-filter.lifetime'));

        return $this->buildResponse($content);
    }

    protected function buildResponse($content): IlluminateResponse
    {
        // define mime type
        $mime = finfo_buffer(finfo_open(FILEINFO_MIME_TYPE), $content);

        // respond with 304 not modified if browser has the image cached
        $etag = md5($content);
        $not_modified = isset($_SERVER['HTTP_IF_NONE_MATCH']) && $_SERVER['HTTP_IF_NONE_MATCH'] == $etag;
        $content = $not_modified ? null : $content;
        $status_code = $not_modified ? 304 : 200;

        // return http response
        return new IlluminateResponse($content, $status_code, [
            'Content-Type' => $mime,
            'Cache-Control' => 'max-age=' . (config('image-filter.lifetime') * 60) . ', public',
            'Content-Length' => strlen($content),
            'Etag' => $etag
        ]);
    }

    public function getDownload($response, $filename): IlluminateResponse
    {
        return $response->header(
            'Content-Disposition',
            'attachment; filename=' . $filename
        );
    }

    public function getImagePath(string $filename)
    {
        foreach (config('image-filter.paths') as $path) {
            $image_path = $path . '/' . str_replace('..', '', $filename);
            if (file_exists($image_path) && is_file($image_path)) {
                // file found
                return $image_path;
            }
        }

        // file not found
        abort(404);
    }

}
