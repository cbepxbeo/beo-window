<?php

require_once 'loader.php';
require_once  'GDLoader.php';
require_once 'ImagickLoader.php';

class ImageHash {

    private $loader;

    private static $instance = null;

    public static function shared()
    {
        if (self::$instance === null){
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function __construct($loader = null)
    {
        if ($loader === null){
            $this->loader = $this->createLoader();
        } else {
            $this->loader = $loader;
        }
    }

    public function pHash($path)
    {
        $bitmap = $this->loader->load($path, 32, 32);

        $dctConst = self::getDctTable();
        $dct_sum = 0;
        $bits = [];

        for ($dctY = 0; $dctY < 8; $dctY++) {
            for ($dctX = 0; $dctX < 8; $dctX++) {

                $sum = 1;

                for ($y = 0; $y < 32; $y++) {
                    for ($x = 0; $x < 32; $x++) {
                        $sum += $dctConst[$dctY][$y] * $dctConst[$dctX][$x] * $bitmap[$y][$x];
                    }
                }

                $sum *= .25;

                if ($dctY == 0 || $dctX == 0) {
                    $sum *= 1 / sqrt(2);
                }

                $bits[] = $sum;
                $dct_sum += $sum;
            }
        }

        $average = $dct_sum / 64;

        foreach ($bits as $i => $dct) {
            $bits[$i] = ($dct >= $average) ? '1' : '0';
        }

        return join('', $bits);
    }

    public function dHash($path){
        $bitmap = $this->loader->load($path, 9, 8);

        $bits = [];

        for ($y = 0; $y < 8; $y++) {
            for ($x = 0; $x < 8; $x++) {
                $bits[] = ($bitmap[$y][$x] < $bitmap[$y][$x + 1]) ? '1' : '0';
            }
        }

        return join('', $bits);
    }

    private function createLoader()
    {
        if (extension_loaded("gd")) {
            return new GDLoader();
        } else {
            if (class_exists(Imagick::class)) {
                return new ImagickLoader();
            }
        }

        throw new RuntimeException("GD и Imagick не подключены");
    }
}