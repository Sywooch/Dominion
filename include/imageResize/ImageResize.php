<?php

require_once 'config_mage.ini.php';
require_once 'imageConverter.php';

/**
 * Класс с помощью которго можно изменить все картикни
 *
 * @author Ruslan Bocharov
 */
class ImageResize
{

    /**
     * возвращаем массив где все необходимые опции приведены в порядок
     * @param Array $options
     *
     * @return Array
     */
    private static function makeOptions($options)
    {
        $options['strictSize'] = isset($options['strictSize']) ? $options['strictSize'] : false;
        $options['pathToWatermark'] = isset($options['pathToWatermark']) ? $options['pathToWatermark'] : '';

        return $options;
    }

    /**
     * Общий метод для конвертации картинок
     * Выполняем конвертацию всех необходимых картинок
     *
     * @param varchar     $imageBase      имя файла из которого делаем ресайз
     * @param varchar     $savePath       путь куда сохраняем картинку
     * @param SizePicture $pictureNewSize Объект где храним и ширину и высоту
     * @param varchar     $newName        Новое имя для картинки
     * @param array       $options        pathToWatermark=null - путь для вотермарка (если необходим)
     *                                    needDif=false  - необходимо ли учитывать разницу размеров
     *                                    savePath  - путь относительно it
     *
     * @return bool|string|\varchar
     */
    public function imageConvert($imageBase, $savePath, SizePicture $pictureNewSize, $newName, array $options = null)
    {
        try {

            if (!file_exists($imageBase))
                throw new Exception("Cant get base image to convert.");

            // Получаем ширину и высоту базовой картинки
            list($withBaseImage, $heihgtBaseImage) = getimagesize($imageBase);

            $options = self::makeOptions($options);
            // Анализируем ширину и высоту картинки - если ширина или высота базовой картинки больше требуемого размера -
            // приводим картинку к этим размерам. Иначе ковертировать не надо
            // Не забываем про переданный параметр $_options['pathToWatermark'] - в этом случае полюбому надо делать конвертацию и прикреплять вотермарк
            $pictureNewSize->getNeedToResize($withBaseImage, $heihgtBaseImage, $options['pathToWatermark']);

            if ($pictureNewSize->getResize()) {
                $imageResize = new ImageConvertor($imageBase, $savePath, $newName, $pictureNewSize->getWidth(), $pictureNewSize->getHeight());
                $imageResize->resizeImage($options['pathToWatermark'], false, $options['strictSize']);
                $newFile = $imageResize->getSavePath();
            } else {
//                $newFile = $_newName . $imageResizer->getTypeFormat();
                $ext = ImageConvertor::getFormatImages(getimagesize($imageBase));
                $newFile = "$savePath$newName.$ext";
                $f = $this->copyImageToCat($imageBase, $newFile);
                if (!$f)
                    throw new Exception("Cant copy file into $newFile");
            }

            // Меняем привелегии на 777
            // чтоб в будущем можно было менять эту картинку
            $f = chmod($newFile, modeImages);
            if (!$f)
                throw new Exception("Can't change mod for $newFile");
        } catch (Exception $exc) {
            // Если что-то пошло не так - показываем ошибки и останавливаемся
            // потому как бестолку конвертить фото дальше
            echo $exc->getTraceAsString();
            echo "<p><b>" . $exc->getMessage() . "</b></p>";

            return false;
        }

        unset($imageResize);

        return $newFile;
    }

    /**
     * Копируем файл по заданному пути
     * и устанавливаем уровень доступа 777
     *
     * @param varchar $source
     * @param         $destination
     *
     * @return boolean  возвращаем true/false если удалось скопирвать или не удалось
     */
    private function copyImageToCat($source, $destination)
    {
        try {
            //копируем исходник изменяем имя
            copy($source, $destination);
        } catch (Exception $e) {
            return false;
        }

        return true;
    }

    /**
     * возвращает разницу площадей картинок (на сколько процентов требуемая картинка больше оригинальной)
     * @deprecated
     */
    static public function getDifferenceInArea($widthOrigin, $heightOrigin, $widthNeed, $heightNeed)
    {
        return ($widthNeed * $heightNeed) / ($widthOrigin * $heightOrigin) * 100 - 100;
    }

    static public function getFilesFromGalleryPath($path)
    {
        if ($handle = opendir($path)) {
            $files = array();
            while (false !== ($file = readdir($handle)))
                array_push($files, $file);

            closedir($handle);

            return $files;
        } else
            return false;
    }

}