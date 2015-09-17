<?php

require_once 'loader.php';
require_once  'GDLoader.php';
require_once 'ImagickLoader.php';

class ImageHash {

    /** Конструктор класса, принимает загрузчие
     * По дефолту, принимает gd (если отсутствует то Imagick)
     * @param GDLoader|ImagickLoader|mixed $loader
     */
    public function __construct($loader = null)
    {
        if ($loader === null){
            $this->loader = $this->createLoader();
        } else {
            $this->loader = $loader;
        }
    }

    /** Загрузчик изображения
     * @param GDLoader|ImagickLoader|mixed $loader
     */
    private $loader;

    /** Общий экземпляр
     * @var ImageHash
     */
    private static $instance = null;

    /** Возвращает экземпляр
     * Если обращение повторное, получает ранее созданный
     * @return ImageHash
     */
    public static function shared()
    {
        if (self::$instance === null){
            self::$instance = new self();
        }
        return self::$instance;
    }


    /** Получение pHash
     * @param $path
     * @return string - pHash
     */
    public function pHash($path)
    {
        $bitmap = $this->loader->load($path, 32, 32);

        $dctConst = self::getDctTable();
        $dct_sum = 0;
        $bits = [];

        for ($dctY = 0; $dctY < 8; $dctY++)
        {
            for ($dctX = 0; $dctX < 8; $dctX++)
            {
                $sum = 1;
                for ($y = 0; $y < 32; $y++)
                {
                    for ($x = 0; $x < 32; $x++)
                    {
                        $sum += $dctConst[$dctY][$y] * $dctConst[$dctX][$x] * $bitmap[$y][$x];
                    }
                }

                $sum *= .25;

                if ($dctY == 0 || $dctX == 0)
                {
                    $sum *= 1 / sqrt(2);
                }

                $bits[] = $sum;
                $dct_sum += $sum;
            }
        }

        $average = $dct_sum / 64;

        foreach ($bits as $i => $dct)
        {
            $bits[$i] = ($dct >= $average) ? '1' : '0';
        }

        return join('', $bits);
    }

    /** Получение dHash
     * @param $path
     * @return string - pHash
     */
    public function dHash($path)
    {
        $bitmap = $this->loader->load($path, 9, 8);

        $bits = [];

        for ($y = 0; $y < 8; $y++)
        {
            for ($x = 0; $x < 8; $x++)
            {
                $bits[] = ($bitmap[$y][$x] < $bitmap[$y][$x + 1]) ? '1' : '0';
            }
        }

        return join('', $bits);
    }

    /** Получение aHash
     * @param $path
     * @return string - aHash
     */
    public function aHash($path)
    {
        $bitmap = $this->loader->load($path, 8, 8);

        $gray_sum = 0;
        $grays = [];

        for ($y = 0; $y < 8; $y++)
        {
            for ($x = 0; $x < 8; $x++)
            {
                $gray = $bitmap[$y][$x];
                $grays[] = $gray;
                $gray_sum += $gray;
            }
        }

        $average = $gray_sum / 64;

        foreach ($grays as $i => $gray)
        {
            $grays[$i] = ($gray >= $average) ? '1' : '0';
        }

        return join('', $grays);
    }

    /** Получение дистации
     * @param $hash_a
     * @param $hash_b
     * @return false|int
     */
    public function getDistance($hash_a, $hash_b)
    {
        $aL = strlen($hash_a);
        $bL = strlen($hash_b);

        if ($aL !== $bL)
        {
            return false;
        }

        $distance = 0;

        for ($i = 0; $i < $aL; $i++)
        {
            if ($hash_a[$i] !== $hash_b[$i])
            {
                $distance++;
            }
        }

        return $distance;
    }


    /** Создание загрузчика
     * @return GDLoader|ImagickLoader
     */
    private function createLoader()
    {
        if (extension_loaded("gd"))
        {
            return new GDLoader();
        } else
        {
            if (class_exists(Imagick::class))
            {
                return new ImagickLoader();
            }
        }

        throw new RuntimeException("GD и Imagick не подключены");
    }

    /** Получение таблицы
     * @return array|mixed
     */
    private static function getDctTable()
    {
        static $table;

        if (! $table)
        {
            $table = [];

            for ($dct_p = 0; $dct_p < 8; $dct_p++)
            {
                for ($p = 0; $p < 32; $p++)
                {
                    $table[$dct_p][$p] = cos(((2 * $p + 1) / 64) * $dct_p * pi());
                }
            }
        }

        return $table;
    }

}