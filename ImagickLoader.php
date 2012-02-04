<?php

class ImagickLoader implements Loader{
    public function load($path, $width, $height){
        $image = new Imagick($path);
        $image->resizeImage($width, $height, Imagick::FILTER_BOX, 1);

        $bitmap = [];

        for ($h = 0; $h < $height; $h++) {
            for ($w = 0; $w < $width; $w++) {
                $color = $image->getImagePixelColor($w, $h)->getColor();
                $bitmap[$h][$w] = intval($color['r'] * 0.299 + $color['g'] * 0.587 + $color['b'] * 0.114);
            }
        }
        $image->destroy();
        return $bitmap;
    }
}

?>