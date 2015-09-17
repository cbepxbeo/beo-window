<?php

namespace App\ImageHash;

interface Loader
{
    /**
     * Image loader for hashing
     * @param string $path - Image Path
     * @param int $width - Image width
     * @param int $height - Image height
     *
     * @return int[][] - bitmap
     */
    public function load(string $path, int $width, int $height): array;
}