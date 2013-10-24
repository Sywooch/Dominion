<?php
/**
 * User: Ruslan
 * Date: 03.07.13
 * Time: 23:34
 */

use Imagine\Image\ImageInterface;

/**
 * Коенвертор картинок
 * Юзаем как в приложении так и в админке
 */
class ImageResize_Resize
{

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
     * @param ImageInterface $image Экземплар класса картинки
     *
     * @return ImageInterface
     */
    public function resize($image)
    {

        $box = $image->getSize();

        $newBox = $box->widen($this->width);
        // Проверяем - влазит ли по высоте
        if ($newBox->getHeight() > $this->height) {
            $newBox = $newBox->heighten($this->height);
        }

        // Ресайзим и копируем
        return $image->resize($newBox);

    }


    /**
     * Вычисляем процентное отношение между новой картинкой и текущей
     *
     * @param float $square Площадь изменяемой картинки
     *
     * @return bool
     */
    public function isNeedResize($square)
    {
        if (($this->width * $this->height) / $square * 100 < $this->diffSquare) {
            return true;
        }

        return false;
    }
}