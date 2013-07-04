<?php
/**
 * User: Rus
 * Date: 03.07.13
 * Time: 23:34
 */

use Imagine\Image\Box;
use Imagine\Image\BoxInterface;
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
     * @return BoxInterface
     * @throws Exception
     */
    public function resize($filePath, $saveFilePath)
    {

        $imagine = new Imagine();

        $image = $imagine->open($filePath);

        if (!$image) {
            throw new Exception('Cant open or find file: ' . $filePath);
        }
        $box = $image->getSize();


        if ($this->isNeedResize($box->square(), $this->diffSquare)) {

            $newBox = $box->widen($this->width);
            // Проверяем - влазит ли по высоте
            if ($newBox->getHeight() > $this->height) {
                $newBox = $newBox->heighten($this->height);
            }

            // Ресайзим и копируем
            $image->resize($newBox)->save($saveFilePath, array('flatten' => true));
        } else {
            // Ресайзить не надо - просто копируем под новым именем
            if (!copy($filePath, $saveFilePath)) {
                throw new Exception('Cant save file: ' . $saveFilePath);
            }
        }

        // возвращаем размеры новой картинки
        return $newBox;
    }


    /**
     * Вычисляем процентное отношение между новой картинкой и текущей
     *
     * @param float $square  Площадь изменяемой картинки
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