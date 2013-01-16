<?php

require_once 'config_mage.ini.php';
require_once 'imageConverter.php';

/**
 * Класс с помощью которго можно изменить все картикни
 *
 * @author Ruslan Bocharov
 */
class imageResizer {

    /**
     * возвращаем массив где все необходимые опции приведены в порядок
     * @param Array $options
     * @return Array
     */
    private static function makeOptions($options) {
        $options['strictSize'] = isset($options['strictSize']) ? $options['strictSize'] : false;
        $options['pathToWatermark'] = isset($options['pathToWatermark']) ? $options['pathToWatermark'] : '';
        return $options;
    }

    /**
     * Общий метод для конвертации картинок
     * Выполняем конвертацию всех необходимых картинок
     *
     * @param varchar $_imageBase имя файла из которого делаем ресайз
     * @param varchar $_savePath путь куда сохраняем картинку
     * @param sizePicture $pictureNewSize Объект где храним и ширину и высоту
     * @param varchar $_newName Новое имя для картинки
     * @param array  $_options  pathToWatermark=null - путь для вотермарка (если необходим)
     *                          needDif=false  - необходимо ли учитывать разницу размеров
     *                          savePath  - путь относительно it
     */
    public function imageConvert($_imageBase, $_savePath, sizePicture $pictureNewSize, $_newName, array $_options=null) {
        try {

            if (!file_exists($_imageBase))
                throw new Exception("Cant get base image to convert.");

            // Получаем ширину и высоту базовой картинки
            list($withBaseImage, $heihgtBaseImage) = getimagesize($_imageBase);

            $_options = self::makeOptions($_options);
            // Анализируем ширину и высоту картинки - если ширина или высота базовой картинки больше требуемого размера -
            // приводим картинку к этим размерам. Иначе ковертировать не надо
            // Не забываем про переданный параметр $_options['pathToWatermark'] - в этом случае полюбому надо делать конвертацию и прикреплять вотермарк
            $pictureNewSize->getNeedToResize($withBaseImage, $heihgtBaseImage, $_options['pathToWatermark']);

            if ($pictureNewSize->getResize()) {
                $imageResizer = new ImageConvertor($_imageBase, $_savePath, $_newName, $pictureNewSize->getWidth(), $pictureNewSize->getHeight());
                $imageResizer->resizeImage($_options['pathToWatermark'], false, $_options['strictSize']);
                $newFile = $imageResizer->getSavePath();
            } else {
//                $newFile = $_newName . $imageResizer->getTypeFormat();
                $ext = ImageConvertor::getFormatImages(getimagesize($_imageBase));
                $newFile = "$_savePath$_newName.$ext";
                $f = $this->copyImageToCat($_imageBase, $newFile);
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

        unset($imageResizer);
        return $newFile;
    }

    /**
     * Копируем файл по заданному пути
     * и устанавливаем уровень доступа 777
     * @param varchar $source
     * @param varchar $source
     * @return boolean  возвращаем true/false если удалось скопирвать или не удалось
     */
    private function copyImageToCat($source, $dest) {
        try {
            copy($source, $dest); //копируем исходник изменяем имя
        } catch (Exception $e) {
            return false;
        }

        return true;
    }

    /**
     * возвращает разницу площадей картинок (на сколько процентов требуемая картинка больше оригинальной)
     * @deprecated
     */
    static public function getDifferenceInArea($widthOrigin, $heightOrigin, $widthNeed, $heightNeed) {
        return ($widthNeed * $heightNeed) / ($widthOrigin * $heightOrigin) * 100 - 100;
    }

    static function getFilesFromGalleryPath($path) {
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

/**
 * Граничный объект для хранения размеров приведения картинок
 * Применяем рефакторинг "Введение граничного объекта"
 */
class sizePicture {

    private $_height;
    private $_width;
    private $_resize = true;

    public function __construct($width, $height) {
        $this->_width = $width;
        $this->_height = $height;
    }

    function getHeight() {
        return $this->_height;
    }

    function getWidth() {
        return $this->_width;
    }

    function setWidthHeight($w, $h) {
        $this->_width = $w;
        $this->_height = $h;
    }

    /**
     * Возвращем признак необходимости ресайзинга
     * @return bool
     */
    function getResize() {
        return $this->_resize;
    }

    /**
     * Утсновить признак необходимости ресайзинга
     * на основе размеров базовой картинки
     * @param int $withBaseImage Ширина базовой картинки
     * @param int $heightBaseImage Высота базовой картинки
     */
    private function setResize($withBaseImage, $heightBaseImage) {

        if ($withBaseImage > $this->getWidth() || $heightBaseImage > $this->getHeight()) {
//            $newWidth = $needWidth;
//            $newHeight = $needHeight;
            $this->_resize = true;
        } else {
            $this->_width = $withBaseImage;
            $this->_height = $heightBaseImage;
            $this->_resize = false;
        }
    }

    /**
     * Выяснем надо ли ресайзить картинку
     * @param int $withBaseImage Ширина базовой картинки
     * @param int $heihgtBaseImage Высота базовой картинки
     * @param varchar $watermark Вотермарк - путь к вотермарку
     * @return bool
     */
    function getNeedToResize($withBaseImage, $heihgtBaseImage, $watermark = null) {

        // Устанавливаем _resize на основе размера базовой картинки
        $this->setResize($withBaseImage, $heihgtBaseImage);

        if ($this->getResize() && empty($watermark)) {
            // Если не надо ресайзить и при этом не надо вотермарк
//            $resize = self::RESIZE;
            $this->_resize = true;
//            $watetmark = null;
        } elseif ($this->getResize() || !empty($watermark)) {
            // Если надо ресайзить с вотремарком
//            $resize = self::RESIZE;
            $this->_resize = true;
//            $watetmark = $watermark;
        } elseif (!$this->getResize() && empty($watermark))
        // Если не надо ресайзить и не надо крепить вотермарк - просто копируем
//            $resize = self::RESIZE_JUST_MOVE;
            $this->_resize = false;

        return $this->getResize();
    }

    function getDiffSize($widthNeed, $heightNeed) {
//         static public function getDifferenceInArea($widthOrigin, $heightOrigin, $widthNeed, $heightNeed) {
        return ($widthNeed * $heightNeed) / ( $this->getWidth() * $this->getHeight()) * 100 - 100;
    }

}

?>