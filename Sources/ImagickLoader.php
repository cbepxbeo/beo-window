<?php

namespace App\ImageHash;

use Imagick;
use ImagickException;
use ImagickPixelException;

final class ImagickLoader implements Loader
{
    /**
     * Image loader for hashing
     * @param string $path - Image Path
     * @param int $width - Image width
     * @param int $height - Image height
     * @return int[][] - bitmap
     * @throws ImagickException
     * @throws ImagickPixelException
     */
    public function load(string $path, int $width, int $height): array
    {
        $image = new Imagick($path);
        $image->resizeImage($width, $height, Imagick::FILTER_BOX, 1);

        $bitmap = [];

        for ($h = 0; $h < $height; $h++)
        {
            for ($w = 0; $w < $width; $w++)
            {
                $color = $image->getImagePixelColor($w, $h)->getColor();
                $bitmap[$h][$w] = intval($color['r'] * 0.299 + $color['g'] * 0.587 + $color['b'] * 0.114);
            }
        }
        $image->destroy();
        return $bitmap;
    }
}