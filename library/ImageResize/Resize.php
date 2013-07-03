<?php
/**
 * User: Rus
 * Date: 03.07.13
 * Time: 23:34
 */

use Imagine\Image\Box;
use Imagine\Image\ImageInterface;
use Imagine\GD\Imagine;

/**
 * Коенвертор картинок
 * Юзаем как в приложении так и в админке
 */
class ImageResize_Resize
{

    /**
     * Путь к картинки из которой ресайзим
     * @var string
     */
    private $filePath;

    /**
     * Требуемая ширина картинки
     * @var int
     */
    private $width;

    /**
     * Требуемая высота картинки
     * @var int
     */
    private $height;

    /**
     * Разница прощадей
     * @var float
     */
    private $diffSquare;

    /**
     * @param int   $width      Требуемая ширина картинки
     * @param int   $height     Требуемая высота картинки
     * @param float $diffSquare Разница площади картинки для проверки условяи надо ли её конвертить
     */
    public function __construct($width, $height, $diffSquare)
    {
        $this->width = $width;
        $this->height = $height;
        $this->diffSquare = $diffSquare;
    }


    /**
     * @param string $filePath     Путь к файлу который открываем
     * @param string $saveFilePath Путь куда сохраняем
     *
     * @throws Exception
     */
    public function resize($filePath, $saveFilePath)
    {
        try {
            $imagine = new Imagine();

            $image = $imagine->open($filePath);

            if (!$image) {
                throw new Exception('Cant open or find file: ' . $filePath);
            }
            $box = $image->getSize();


            if ($this->isNeedResize($box->square(), $this->diffSquare)) {
                $newBox = $box->widen($this->width);

                $image->resize($newBox)
                    ->save($saveFilePath, array('flatten' => true));
            }

        } catch (Exception $e) {
            echo "123";
        }


//        return array('width'=>) $newFilePath;
    }


    /**
     * Вычисляем процентное отношение между новой картинкой и текущей
     *
     * @param float $square  Площадь изменяемой картинки
     *
     * @return bool
     */
    private function isNeedResize($square)
    {
        $diffPictureSize = ($this->width * $this->height) / $square * 100;

        if ($diffPictureSize < $this->diffSquare) {
            return true;
        } else {
            return false;
        }

    }

}