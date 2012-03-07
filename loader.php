<?php

interface Loader
{
    /**
     * Загрузчик картинок для хеширования
     * @param string $path
     * @param int $width
     * @param int $height
     *
     * @return int[][]
     */
    public function load($path, $width, $height);
}