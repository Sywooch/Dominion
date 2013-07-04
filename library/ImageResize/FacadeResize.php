<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Admin
 * Date: 04.07.13
 * Time: 17:32
 * To change this template use File | Settings | File Templates.
 */

use Imagine\Image\Box;
use Imagine\Image\BoxInterface;
use Imagine\Image\ImageInterface;
use Imagine\GD\Imagine;

class ImageResize_FacadeResize
{
    static public function resizeOrSave($filePath, $fileSaveDir, $needWidth, $needHeight, $diffSquare = 20)
    {

        $imagine = new Imagine();

        $image = $imagine->open($filePath);

        // Открыть файл не моежем - ошибка
        if (!$image) {
            throw new Exception('Cant open or find file: ' . $filePath);
        }

        $imageSize = $image->getSize();
        $r = new ImageResize_Resize($needWidth, $needHeight, $diffSquare);

        $g = $r->isNeedResize($imageSize->square());
    }

}