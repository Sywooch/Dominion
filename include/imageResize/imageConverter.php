<?php

/**
 * Библиотека с функциями конвертора картинок
 * @package imageConverter
 */
/*
 *  $savePath - куда сохраняем
 *  $filePath - откуда берем
 *  $fName - имя файла
 */

//Конвертор картинки из BMP в JPEG
function ImageCreateFromBMPmax($filename) {
    //Ouverture du fichier en mode binaire
    if (!$f1 = fopen($filename, "rb"))
        return FALSE;

    //1 : Chargement des ent?tes FICHIER
    $FILE = unpack("vfile_type/Vfile_size/Vreserved/Vbitmap_offset", fread($f1, 14));
    if ($FILE['file_type'] != 19778)
        return FALSE;

    //2 : Chargement des ent?tes BMP
    $BMP = unpack('Vheader_size/Vwidth/Vheight/vplanes/vbits_per_pixel' .
                    '/Vcompression/Vsize_bitmap/Vhoriz_resolution' .
                    '/Vvert_resolution/Vcolors_used/Vcolors_important', fread($f1, 40));
    $BMP['colors'] = pow(2, $BMP['bits_per_pixel']);
    if ($BMP['size_bitmap'] == 0)
        $BMP['size_bitmap'] = $FILE['file_size'] - $FILE['bitmap_offset'];
    $BMP['bytes_per_pixel'] = $BMP['bits_per_pixel'] / 8;
    $BMP['bytes_per_pixel2'] = ceil($BMP['bytes_per_pixel']);
    $BMP['decal'] = ($BMP['width'] * $BMP['bytes_per_pixel'] / 4);
    $BMP['decal'] -= floor($BMP['width'] * $BMP['bytes_per_pixel'] / 4);
    $BMP['decal'] = 4 - (4 * $BMP['decal']);
    if ($BMP['decal'] == 4)
        $BMP['decal'] = 0;

    //3 : Chargement des couleurs de la palette
    $PALETTE = array();
    if ($BMP['colors'] < 16777216) {
        $PALETTE = unpack('V' . $BMP['colors'], fread($f1, $BMP['colors'] * 4));
    }

    //4 : Cr?ation de l'image
    $IMG = fread($f1, $BMP['size_bitmap']);
    $VIDE = chr(0);

    $res = imagecreatetruecolor($BMP['width'], $BMP['height']);
    $P = 0;
    $Y = $BMP['height'] - 1;
    while ($Y >= 0) {
        $X = 0;
        while ($X < $BMP['width']) {
            if ($BMP['bits_per_pixel'] == 24)
                $COLOR = unpack("V", substr($IMG, $P, 3) . $VIDE);
            elseif ($BMP['bits_per_pixel'] == 16) {
                $COLOR = unpack("n", substr($IMG, $P, 2));
                $COLOR[1] = $PALETTE[$COLOR[1] + 1];
            } elseif ($BMP['bits_per_pixel'] == 16) {
                $COLOR = unpack("v", substr($IMG, $P, 2));
                $blue = ($COLOR[1] & 0x001f) << 3;
                $green = ($COLOR[1] & 0x07e0) >> 3;
                $red = ($COLOR[1] & 0xf800) >> 8;
                $COLOR[1] = $red * 65536 + $green * 256 + $blue;
            } elseif ($BMP['bits_per_pixel'] == 8) {
                $COLOR = unpack("n", $VIDE . substr($IMG, $P, 1));
                $COLOR[1] = $PALETTE[$COLOR[1] + 1];
            } elseif ($BMP['bits_per_pixel'] == 4) {
                $COLOR = unpack("n", $VIDE . substr($IMG, floor($P), 1));
                if (($P * 2) % 2 == 0)
                    $COLOR[1] = ($COLOR[1] >> 4); else
                    $COLOR[1] = ($COLOR[1] & 0x0F);
                $COLOR[1] = $PALETTE[$COLOR[1] + 1];
            }
            elseif ($BMP['bits_per_pixel'] == 1) {
                $COLOR = unpack("n", $VIDE . substr($IMG, floor($P), 1));
                if (($P * 8) % 8 == 0)
                    $COLOR[1] = $COLOR[1] >> 7;
                elseif (($P * 8) % 8 == 1)
                    $COLOR[1] = ($COLOR[1] & 0x40) >> 6;
                elseif (($P * 8) % 8 == 2)
                    $COLOR[1] = ($COLOR[1] & 0x20) >> 5;
                elseif (($P * 8) % 8 == 3)
                    $COLOR[1] = ($COLOR[1] & 0x10) >> 4;
                elseif (($P * 8) % 8 == 4)
                    $COLOR[1] = ($COLOR[1] & 0x8) >> 3;
                elseif (($P * 8) % 8 == 5)
                    $COLOR[1] = ($COLOR[1] & 0x4) >> 2;
                elseif (($P * 8) % 8 == 6)
                    $COLOR[1] = ($COLOR[1] & 0x2) >> 1;
                elseif (($P * 8) % 8 == 7)
                    $COLOR[1] = ($COLOR[1] & 0x1);
                $COLOR[1] = $PALETTE[$COLOR[1] + 1];
            }
            else
                return FALSE;
            imagesetpixel($res, $X, $Y, $COLOR[1]);
            $X++;
            $P += $BMP['bytes_per_pixel'];
        }
        $Y--;
        $P+=$BMP['decal'];
    }

    //Fermeture du fichier
    fclose($f1);

    return $res;
}

class ImageConvertor {
    /**
     * Уровень качества при конвертации для JPEG
     */
    const QUALITY = 90;
    const QUALITY_GIF = 100;



    const DIF_SIZE = 1;

    private $typeFormat = "";
    /**
     * Ширина картинки источника
     * @var int
     */
    private $imageWidth;
    /**
     * Высота картинки источника
     * @var <type>
     */
    private $imageHeight;
    /**
     * Путь (каталог - без имени) куда сохраняем картинку
     * @var varchar
     */
    private $savePath;
    /**
     * Путь к файлу источнику
     * @var varchar
     */
    private $filePath;
    /**
     * Указатель на картинку которую конвертим
     * @var resourse
     */
    private $itemResourceID;
    /**
     * Имя файла в который сохраняем картинку
     * @var varchar
     */
    private $fName;
    /**
     * Требуемая ширина конвертируемой картинки
     * @var int
     */
    private $distWidth;
    /**
     * Требуемая высота конвертируемой картинки
     * @var int
     */
    private $distHeight;
    /**
     * x-coordinate of destination point
     * @var int
     */
    private $distX;
    /**
     * y-coordinate of destination point
     * @var int
     */
    private $distY;
    /**
     * x-coordinate of source point.
     * @var int
     */
    private $srcX;
    /**
     * y-coordinate of source point.
     * @var int
     */
    private $srcY;
    private $error;

    function __construct($filePath, $savePath, $fName, $distWidth, $distHeigth) {

        try {
            $this->error = 0;

            $this->savePath = $savePath;
            $this->filePath = $filePath;
            //$this->fName = $fName;
            $this->distWidth = $distWidth;
            $this->distHeight = $distHeigth;

//            echo "Get image = {$this->filePath} <br/>";
            $size = getimagesize($this->filePath);
//            echo "Size:";
//            print_r($size);

            if (empty($size))
                throw new Exception('Dont catch this path', '1');

//	echo "Path to file:",$this->filePath," type:", $size['mime'], "<br>";

            $this->fName = $this->getTranslitName($fName);
            $this->typeFormat = self::getFormatImages($size);

            $this->savePath.=$this->fName . "." . $this->typeFormat;

            //$this->savePath.=$this->fName.".jpeg";

            $this->imageWidth = $size[0];
            $this->imageHeight = $size[1];

            $this->itemResourceID = self::getImgFrom($this->filePath, $this->typeFormat);


            $this->distX = 0;
            $this->distY = 0;
            $this->srcX = 0;
            $this->srcY = 0;
        } catch (Exception $e) {
            echo $e->getMessage();
            $this->error = $e->getCode();
            return false;
        }
    }

    public function getSavePath() {
        return $this->savePath;
    }

    public function getError() {
        return $this->error;
    }

    public function getTypeFormat() {
        return $this->typeFormat;
    }

    public function getSourceX() {
        return $this->srcX;
    }

    public function getSourceY() {
        return $this->srcY;
    }

    public function getDistX() {
        return $this->distX;
    }

    public function getDistY() {
        return $this->distY;
    }

    public function getDistWidth() {
        return $this->distWidth;
    }

    public function getDistHeight() {
        return $this->distHeight;
    }

    /**
     * Возвращаем ширину картинки оригинала
     * @return int
     */
    public function getSourceWidth() {
        return $this->imageWidth;
    }

    public function getSourceHeight() {
        return $this->imageHeight;
    }

// возвращает разницу площадей картинок (на сколько процентов требуемая картинка больше оригинальной) 
//    public function getDifferenceInArea($widthOrigin, $heightOrigin, $widthNeed, $heightNeed) {
//        return ($widthNeed * $heightNeed) / ($widthOrigin * $heightOrigin) * 100 - 100;
//    }

    private function imageComposeAlpha(&$src, &$ovr, $ovr_x, $ovr_y, $ovr_w = false, $ovr_h = false) { //for resize watermark
        if ($ovr_w && $ovr_h)
            $ovr = $this->imageResizeAlpha($ovr, $ovr_w, $ovr_h);

        /* Noew compose the 2 images */
        imagecopy($src, $ovr, $ovr_x, $ovr_y, 0, 0, imagesx($ovr), imagesy($ovr));
    }

    private function imageResizeAlpha(&$src, $w, $h) {  //for resize watermark
        /* create a new image with the new width and height */
        $temp = imagecreatetruecolor($w, $h);

        /* making the new image transparent */
        $background = imagecolorallocate($temp, 0, 0, 0);
        ImageColorTransparent($temp, $background); // make the new temp image all transparent
        imagealphablending($temp, false); // turn off the alpha blending to keep the alpha channel

        /* Resize the PNG file */
        /* use imagecopyresized to gain some performance but loose some quality */
        imagecopyresized($temp, $src, 0, 0, 0, 0, $w, $h, imagesx($src), imagesy($src));
        /* use imagecopyresampled if you concern more about the quality */
        //imagecopyresampled($temp, $src, 0, 0, 0, 0, $w, $h, imagesx($src), imagesy($src));
        return $temp;
    }

    static function getFormatImages($size) {
        return strtolower(substr($size['mime'], strpos($size['mime'], '/') + 1));
    }

    /**
     * Типа стратегии по возврату типа создания картинки
     * @param varchar $src путь к картинке из котрой делаем источник
     * @param varchar $type какой тип картинки необходим
     * @return Resource или false
     */
    static function getImgFrom($src, $type) { //jpg or png
        if ($type == 'bmp')
            $icfunc = 'ImageCreateFromBMPmax';
        else
            $icfunc = "imagecreatefrom" . $type;

        if (!function_exists($icfunc))
            return false;

        $isrc = $icfunc($src);

        return $isrc;
    }

    private function getTranslitName($name) {
        $space_patterns[0] = "/\s/";
        $space_patterns[1] = "/%20/";
        $space_replacements[0] = "_";
        $space_replacements[1] = "_";

        $translitRus = array('а', 'б', 'в', 'г', 'д', 'е', 'ё', 'ж', 'з', 'и', 'й', 'к', 'л', 'м', 'н', 'о', 'п', 'р', 'с', 'т', 'у', 'ф', 'х', 'ц', 'ч', 'ш', 'щ', 'ъ', 'ы', 'ь', 'э', 'ю', 'я', ' ', 'А', 'Б', 'В', 'Г', 'Д', 'Е', 'Ж', 'З', 'И', 'Й', 'К', 'Л', 'М', 'Н', 'О', 'П', 'Р', 'С', 'Т', 'У', 'Ф', 'Х', 'Ц', 'Ч', 'Ш', 'Щ', 'Э', 'Ю', 'Я', 'Ь', 'Ъ', 'Ы');
        $translitEng = array('a', 'b', 'v', 'g', 'd', 'e', 'e', 'g', 'z', 'i', 'y', 'k', 'l', 'm', 'n', 'o', 'p', 'r', 's', 't', 'u', 'f', 'h', 'c', 'c', 's', 's', '_', 'i', '_', 'e', 'u', 'y', '_', 'A', 'B', 'V', 'G', 'D', 'E', 'G', 'Z', 'I', 'Y', 'K', 'L', 'M', 'N', 'O', 'P', 'R', 'S', 'T', 'U', 'F', 'H', 'C', 'C', 'S', 'S', 'E', 'U', 'Y', '_', '_', '_');

        $fName = preg_replace($space_patterns, $space_replacements, $name);

        for ($j = 0; $j < 65; $j++) {
            $fName = str_replace($translitRus[$j], $translitEng[$j], $fName);
        }
        return $fName;
    }

    private function getImageNewSizeWatermark($width, $height, $newWidth, $newHeight) {
        if (($width > $newWidth) || ($height > $newHeight )) {
            if ($width > $newWidth && $height <= $newHeight)
                $prop = $newWidth / $width;
            elseif ($height > $newHeight && $width <= $newWidth)
                $prop = $newHeight / $height;
            else
                $prop = ($width > $height) ? $newWidth / $width : $newHeight / $height;

            $newWidth = $width * $prop;
            $newHeight = $height * $prop;
        }
        return array($newWidth, $newHeight);
    }

    /**
     * Получить новые размеры высоты и ширины картинки
     * @param int $sourceWidth
     * @param int $sourceHeight
     * @param int $distWidth
     * @param int $distHeight
     * @return array
     */
    private function getImageNewSize($sourceWidth, $sourceHeight, $distWidth, $distHeight) {
        if (($sourceWidth > $distWidth) || ($sourceHeight > $distHeight )) {

            $k = $sourceHeight / $sourceWidth;

            $Hnew = $distWidth * $k;

            if ($Hnew > $distHeight) {
                $distWidth = $distHeight * (1 / $k);
                $distHeight = $distWidth * $k;
            } else {
                $distHeight = $distWidth * $k;
            }
        } else {
            $distWidth = $sourceWidth;
            $distHeight = $sourceHeight;
        }

        return array($distWidth, $distHeight);
    }

    /**
     * Вычисляет новые размеры картинки с условием, что высота ВСЕГДА равна требуемой
     * @param $width ширина исх картинки
     * @param $height высота исх картинки
     * @param $newWidth требуемая ширина
     * @param $newHeight требуемая высота
     * @return unknown_type
     */
    private function getImageNewSizeStrictHeight($width, $height, $newWidth, $newHeight) {
        if ($height > $newHeight) {
            $k = $height / $width;
            $newWidth = $newHeight / $k;
        } else {
            $newWidth = $width;
            $newHeight = $height;
        }

        return array($newWidth, $newHeight);
    }

    /**
     * Метод, определяющий размеры картинки, если они должны быть равны заданным изначально
     * и не могут изменяться.
     * @return array array(width,height)
     */
    private function getImageStrictSize() {
        return array($this->distWidth, $this->distHeight);
    }

    private function getPosition($width, $height, $waterWidth, $waterHeight) {

        $x = ($width - $waterWidth);
        $y = ($height - $waterHeight);

        return array($x, $y);
    }

    function createWaterMark($watermark_path) {

        try {
            $watermark_info = getimagesize($watermark_path);

            $watermark_x = $watermark_info[0];
            $watermark_y = $watermark_info[1];

            $imageLogoIn = $this->itemResourceID;


            $type = self::getFormatImages($watermark_info);

            $imageLogoType = self::getImgFrom($watermark_path, $type);

            if ($type == 'png') {

                $size = $this->getImageNewSize($watermark_x, $watermark_y, $this->imageWidth, $this->imageHeight);
                //print_r($size);
                //die();
                if ($size) {
                    $watermark_x = $size[0];
                    $watermark_y = $size[1];
                }


                $size = $this->getPosition($this->imageWidth, $this->imageHeight, $watermark_x, $watermark_y);

                $this->imageComposeAlpha($imageLogoIn, $imageLogoType, $size[0], $size[1], $watermark_x, $watermark_y);
                $path = $this->savePath;

                $func = "image" . $this->getTypeFormat();

                $f = $func($imageLogoIn, $path); // output to browser
                if (!$f)
                    throw new Exception('Cant do the function ' . $func);
            }
//     else{
//      imagecolorclosestalpha($imageLogo, 0, 0, 0, 0);
//
//      imagecopy($imageOut, $imageLogoIn, 0, 0, 0, 0, $endX, $endY);
//
//      $w_x = ceil($endX/2) + (ceil($endX/2) - $watermark_x);
//      $w_y = ceil($endY/2) + (ceil($endY/2) - $watermark_y);
//      ImageCopyMerge($imageOut,$imageLogo, $w_x, $w_y, 0, 0, $watermark_x, $watermark_y, 100);
//    }
        } catch (Exception $exc) {
//            echo $exc->getTraceAsString();
//            echo "<br>{$exc->getMessage()} <br>";
            throw $exc;
        }
        unset($imageLogoIn);
    }

    private function getKoefSource() {
        return $this->getSourceWidth() / $this->getSourceHeight();
    }

    /**
     * Если надо отресайзить по высоте - true.
     * Если надо отресайзить по широте - false
     * @return bool 
     */
    private function resizeByHeigth() {
        if (($this->getSourceWidth() - $this->getDistWidth()) < ($this->getSourceHeight() - $this->getDistHeight()))
            return true;
        else
            return false;
    }

    /**
     * Устанавливаем значения шиириы и высоты картинки приёмника
     */
    private function SetCoordinate() {
        // TODO: Расчёт работает неправильно
        // если приводить сразу ширину, то может оказаться что после привидения эта ширина не вписывается в требуюмую ширину
        // Первым шагом необходимо вычислить по какой строне прямоугольника мы впишемся однозначно!
        // Для этого надо узнать разницы одноименных стороно требуемого и оригинально прямогоугольников
        // Там где разница наибольшая - по той стороне и делается приведение размера. По другой стороне уже впишемся однозначно

        if ($this->resizeByHeigth()) {
            // Делаем привидение по Высоте
            $this->distWidth = $this->getDistHeight() * $this->getKoefSource();
        } else {
            $this->distHeight = $this->getDistWidth() / $this->getKoefSource();
        }

//        if ($this->getSourceHeight() > $this->getDistHeight())
//            $this->distWidth = $this->getDistHeight() * $this->getKoefSource();
//
//        if ($this->getSourceWidth() > $this->getDistWidth())
//            $this->distHeight = $this->getDistWidth() / $this->getKoefSource();
    }

    /**
     * Устанавливаем значения координат X Y для источника и приёмника
     * метод для условия что картинку надо привести к новому размеру а потом
     * лишнее обрезать. Приводим к высоте
     */
    private function SetStrictCoordinate() {
        if ($this->getSourceWidth() < $this->getDistWidth() && $this->getSourceHeight() < $this->getDistHeight())
            return false;

        // делаем приведение в две итерации
        // Последовательно проверям высоту и ширину
        // 1. Приводим к требуемой ширине и смотрим какая получится высота.
//        $height = $this->getDistWidth() / $this->getKoefSource();
//        if ($height < $this->getDistHeight())
//        $this->distWidth = 250;
//        $this->distHeight = 210;
//        return true;
        // Приведение делаем по той стороне где разница меньше
        if ($this->resizeByHeigth()) {
            // Фиксим по ширине - а именно приводим к дист только ширину
            $height = $this->getDistWidth() / $this->getKoefSource();
            if ($height > $this->getDistHeight())
                $this->distHeight = $height;
            else
                $this->distWidth = $this->getDistHeight() * $this->getKoefSource();
        } else {
            $width = $this->getDistHeight() * $this->getKoefSource();
            if ($width > $this->distWidth)
                $this->distWidth = $width;
            else
                $this->distHeight = $this->getDistWidth() / $this->getKoefSource();
        }

        return true;
    }

// $fillWhiteBackground = дополнять или нет высоту картинок до нужного размера белым цветом
    function resizeImage($watermark_path="", $fillWhiteBackground=0, $isStrictHeight=false) {
        try {

            if ($isStrictHeight) {

                $imageOut = imagecreatetruecolor($this->getDistWidth(), $this->getDistHeight());
                $isStrictHeight = $this->SetStrictCoordinate();
            }

            if (!$isStrictHeight) {
                $this->SetCoordinate();
                $imageOut = imagecreatetruecolor($this->getDistWidth(), $this->getDistHeight());
            }

            $white = imagecolorallocate($imageOut, 255, 255, 255);
            imagefill($imageOut, 0, 0, $white);

            $f = imagecopyresampled($imageOut, $this->itemResourceID, $this->getDistX(), $this->getDistY(), $this->getSourceX(), $this->getSourceY(), $this->getDistWidth(), $this->getDistHeight(), $this->getSourceWidth(), $this->getSourceHeight());

            if (!$f)
                throw new Exception("\nСконвертировать не удалось для $imageOut => строка " . __LINE__);


//            if ($isStrictHeight && $this->SetStrictCoordinate()) {
//
//                $f = imagecopyresampled($imageOut, $this->itemResourceID, $this->getDistX(), $this->getDistY(), $this->getSourceX(), $this->getSourceY(), $this->getDistWidth(), $this->getSourceHeight());
//
//                if (!$f)
//                    throw new Exception("\nСконвертировать не удалось для $imageOut => строка " . __LINE__);
//            } else {
//                $this->SetCoordinate();
//                $f = imagecopyresampled($imageOut, $this->itemResourceID, $this->getDistX(), $this->getDistY(), $this->getSourceX(), $this->getSourceY(), $this->getDistWidth(), $this->getDistHeight(), $this->getSourceWidth(), $this->getSourceHeight());
//                if (!$f)
//                    throw new Exception("\nСконвертировать не удалось для $imageOut => строка " . __LINE__);
//            }


            if ($watermark_path) {
                $this->itemResourceID = $imageOut;
                $this->createWaterMark($watermark_path);
            } else {
                $func = "image" . $this->getTypeFormat();
                $quality = $this->typeFormat == 'gif' ? self::QUALITY_GIF : self::QUALITY;

                $f = $func($imageOut, $this->savePath, $quality);
                if (!$f)
                    throw new Exception("$func не удалось для $this->savePath строка " . __LINE__);
//          echo "\n$func не удалось для $this->savePath строка ".__LINE__."\n";
            }
        } catch (Exception $e) {
            $e->getTraceAsString();
            throw $e;
        }
    }

}

?>
