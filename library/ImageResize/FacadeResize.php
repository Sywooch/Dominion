<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Ruslan
 * Date: 04.07.13
 * Time: 17:32
 */

use Imagine\Gd\Imagine;

class ImageResize_FacadeResize
{
    /**
     * @param int    $newImageName      Id товара в БД
     * @param string $filePath          Абсолютный путь к картинке
     * @param string $fileSaveDir       Имя каталога где хотим сохранить
     * @param int    $needWidth         требуемая ширина
     * @param int    $needHeight        требуемая высота
     * @param int    $diffSquare        допустимая разница в площадях картинок для ресайзинга
     *
     * @return ImageResize_ImageParams
     */
    static public function resizeOrSave($newImageName, $filePath, $fileSaveDir, $needWidth, $needHeight, $diffSquare = 20)
    {
        try {
            $imagine = new Imagine();

            $image = $imagine->open($filePath);

            // Получаем новое имя картинки
            $fileExtension = pathinfo($filePath, PATHINFO_EXTENSION);
            $newItemName = "$fileSaveDir/$newImageName.$fileExtension";

            $imageSize = $image->getSize();

            $imageResizeItem = new ImageResize_Resize($needWidth, $needHeight, $diffSquare);

            // Проверяем надо ли ресайзит пиктчу
            if ($imageResizeItem->isNeedResize($imageSize->square())) {
                $image = $imageResizeItem->resize($image)->save($newItemName);
            } else {
                if (!copy($filePath, $newItemName)) {
                    throw new Exception('Can\'t save file: ' . $newItemName);
                }
            }

        } catch (Exception $e) {
            echo $e->getMessage();
            echo "\n" . $e->getFile() . "; Line:" . $e->getLine();
            return false;
        }

        // Вернуть размеры картинки
        return new ImageResize_ImageParams("$newImageName.$fileExtension", $image->getSize()->getWidth(), $image->getSize()->getHeight());
    }

}