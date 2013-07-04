<?php

define('IMPORT_IMAGE_PATH', '');

class item_image_convert
{

    private $GoodsGroup;
    private $Item;

    public function __construct()
    {
        Zend_Loader::loadClass('models_GoodsGroup');
        Zend_Loader::loadClass('models_Item');

        $this->GoodsGroup = new models_GoodsGroup();
        $this->Item = new models_Item();
    }

    public function run()
    {
        $this->getItemImages();
        $this->getGoodsGroupsImages();
    }

    private function getItemImages()
    {
        $base_item_img = $this->Item->getBaseItemImage();

        if (!empty($base_item_img)) {
            foreach ($base_item_img as $img) {
                $item_id = $img['ITEM_ID'];
                $pictures['name'] = (substr($img['BASE_IMAGE'], 0, 1) =='/')  ? $img['BASE_IMAGE']:'/'.$img['BASE_IMAGE'];
                $pictures['type'] = 'base';
                if (!empty($pictures['name']) && file_exists(IMAGE_UPLOAD_PATH . $pictures['name'])) {
//          if(!$this->Item->itemHasImage($item_id)){
                    list($_data['IMAGE1'], $_data['IMAGE2'], $_data['IMAGE3']) = $this->convertItemImage($pictures,
                                                                                                         $item_id);
                    $_data['NEED_RESIZE'] = 0;
                    $this->Item->updateItemImport($_data, $item_id);
//          }
                    $pathToImage = pathToImages . $pictures['name'];
                    echo "Image {$pathToImage} for ID {$item_id} converted\r\n<br>";
                    unset($_data);
                } else {
                    echo "Image " . IMAGE_UPLOAD_PATH . $pictures['name'] . " for ID {$item_id} not found\r\n<br>";
                }
                $this->getItemPhotos($item_id);
            }
        }
    }

    private function getItemPhotos($item_id)
    {
        $base_item_img = $this->Item->getBaseItemPhotos($item_id);
        if (!empty($base_item_img)) {
            foreach ($base_item_img as $img) {
                $pictures['name'] = $img['NAME'];
                $pictures['type'] = 'big';

                if (!empty($pictures['name']) && file_exists(IMAGE_UPLOAD_PATH . $pictures['name'])) {
                    list($_data['IMAGE1'], $_data['IMAGE2']) = $this->convertItemFotos($pictures,
                                                                                       $item_id,
                                                                                       $img['ITEM_ITEM_ID']);
                    $this->Item->updateItemFotos($_data, $img['ITEM_ITEM_ID']);

                    $pathToImage = pathToImages . $pictures['name'];
                    echo "Image {$pathToImage} for ID {$item_id} converted\r\n<br>";
                    unset($_data);
                }

//                else {
//                    echo "Image ".IMAGE_UPLOAD_PATH."{$pictures['name']} for ID {$item_id} not found\r\n<br>";
//                }
            }
        }
    }

    private function getGoodsGroupsImages()
    {
        $base_item_img = $this->GoodsGroup->getBaseGoodsGroupImage(array(IS_LIDER, IS_RECOMEND));
        if (!empty($base_item_img)) {
            foreach ($base_item_img as $img) {
                $item_id = $img['ITEM_ID'];
                $pictures['name'] = $img['BASE_IMAGE'];
                $pictures['type'] = 'base';

                if (!empty($pictures['name']) && file_exists(IMAGE_UPLOAD_PATH . $pictures['name'])) {
                    list($_goods_group_data['IMAGE']) = $this->convertGroupItemFotos($pictures,
                                                                                     $item_id);
                    $this->GoodsGroup->updateItemToGoodGroup($_goods_group_data,
                                                             $img['GOODS_GROUP_ID'],
                                                             $img['ITEM_ID']);

                    $pathToImage = pathToImagesGroupItems . $pictures['name'];
                    echo "Image {$pathToImage} for ID {$item_id} converted\r\n";
                    unset($_goods_group_data);
                }
            }
        }
    }

    private function convertItemImage($picture, $V_ITEM_ID)
    {
        $_image_name_result = array('', '', '');

        $V_IMAGE1 = str_replace("/", "_", $picture['name']);
        $V_IMAGE1 = strtolower($V_IMAGE1);

        $image1 = $picture['type'] . $V_ITEM_ID . '.jpeg';
        $buf = file_get_contents(IMAGE_UPLOAD_PATH . $picture['name']);

        if (!$buf) {
            echo "Image " . $picture['name'] . " not founded \r\n<br>";
            return $_image_name_result;
        }

        try {
            $OUT = fopen(pathToImages . $image1, "w");
            if (!$OUT)
                throw new Exception("Cant get to write into " . pathToImages);
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


            $options['strictSize'] = false;
            $options['pathToWatermark'] = pathToWatermark;

            $sizes = new SizePicture(Size_b_X, Size_b_Y);

            // Делаем большую картинку IMAGE3
            $newFile = $resizer->imageConvert($image1, pathToImages, $sizes,
                                              "b_$V_ITEM_ID");
            if ($newFile)
                $_image_name_result[2] = $this->setDBupdateAfterConvert($newFile,
                                                                        "b_$V_ITEM_ID");

            // Делаем среднюю картинку IMAGE2

            $img_name = $V_ITEM_ID;

            $sizes->setWidthHeight(Size_X, Size_Y);
            $newFile = $resizer->imageConvert($image1, pathToImages, $sizes,
                                              $img_name);
            if ($newFile)
                $_image_name_result[1] = $this->setDBupdateAfterConvert($newFile,
                                                                        $img_name);

            // Делаем маленькую картинку IMAGE1

            $img_name = 's_' . $V_ITEM_ID;

            $sizes->setWidthHeight(Size_s_X, Size_s_Y);
            $newFile = $resizer->imageConvert($image1, pathToImages, $sizes,
                                              $img_name);
            if ($newFile)
                $_image_name_result[0] = $this->setDBupdateAfterConvert($newFile,
                                                                        $img_name);
        } catch (Exception $e) {
            echo $exc->getTraceAsString();
            echo "<br>" . $exc->getMessage();
            echo "<br>Line " . $exc->getLine();
            echo "<br>File " . $exc->getFile() . "<br/>";
            die('Stop converting');
        }

        unset($resizer);

        unlink($image1); // удаляем base-фото

        return $_image_name_result;
    }

    private function convertItemFotos($picture, $V_ITEM_ID, $V_ITEM_ITEM_ID)
    {
        $_image_name_result = array('', '', '');

        $V_IMAGE1 = str_replace("/", "_", $picture['name']);
        $V_IMAGE1 = strtolower($V_IMAGE1);

        $image1 = $picture['type'] . $V_ITEM_ID . '.jpeg';
        $buf = file_get_contents(IMAGE_UPLOAD_PATH . $picture['name']);

        if (!$buf) {
            echo "Image " . IMAGE_UPLOAD_PATH . $picture['name'] . " not founded \n<br>";
            return $_image_name_result;
        }

        try {
            $OUT = fopen(pathToImages . $image1, "w");
            if (!$OUT)
                throw new Exception("Cant get to write into " . pathToImages);
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


            $options['strictSize'] = false;
            $options['pathToWatermark'] = pathToWatermark;

            $sizes = new SizePicture(Size_gallery_X, Size_gallery_Y);


            // Делаем большую картинку IMAGE3
            $img_name = 'item_' . $picture['type'] . '_' . $V_ITEM_ID . '_' . $V_ITEM_ITEM_ID;

            $newFile = $resizer->imageConvert($image1, pathToImages, $sizes,
                                              $img_name);
            if ($newFile)
                $_image_name_result[1] = $this->setDBupdateAfterConvert($newFile,
                                                                        $img_name);

            // Делаем аленькая картинку IMAGE2
            $img_name = 'item_s_' . $picture['type'] . '_' . $V_ITEM_ID . '_' . $V_ITEM_ITEM_ID;

            $sizes->setWidthHeight(Size_gallery_s_X, Size_gallery_s_Y);
            $newFile = $resizer->imageConvert($image1, pathToImages, $sizes,
                                              $img_name);
            if ($newFile)
                $_image_name_result[0] = $this->setDBupdateAfterConvert($newFile,
                                                                        $img_name);
        } catch (Exception $e) {
            echo $exc->getTraceAsString();
            echo "<br>" . $exc->getMessage();
            echo "<br>Line " . $exc->getLine();
            echo "<br>File " . $exc->getFile() . "<br/>";
            die('Stop converting');
        }

        unset($resizer);

        unlink($image1); // удаляем base-фото

        return $_image_name_result;
    }

    private function convertGroupItemFotos($picture, $V_ITEM_ID)
    {
        $_image_name_result = array('', '', '');

        $V_IMAGE1 = str_replace("/", "_", $picture['name']);
        $V_IMAGE1 = strtolower($V_IMAGE1);

        $image1 = $picture['type'] . $V_ITEM_ID . '.jpeg';
        $buf = file_get_contents(IMAGE_UPLOAD_PATH . $picture['name']);

        if (!$buf) {
            echo "Image " . IMAGE_UPLOAD_PATH . $picture['name'] . " not founded \n<br>";
            return $_image_name_result;
        }

        try {
            $OUT = fopen(pathToImagesGroupItems . $image1, "w");
            if (!$OUT)
                throw new Exception("Cant get to write into " . pathToImages);
            fwrite($OUT, $buf);
            fclose($OUT);

            list($W, $H) = getimagesize(pathToImagesGroupItems . $image1);
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
            $image1 = pathToImagesGroupItems . $image1;

            $resizer = new ImageResize();


            $options['strictSize'] = false;
            $options['pathToWatermark'] = pathToWatermark;

            $sizes = new SizePicture(Size_gruop_X, Size_gruop_Y);


            // Делаем большую картинку IMAGE3
            $img_name = 'i_link_' . $V_ITEM_ID;

            $newFile = $resizer->imageConvert($image1, pathToImagesGroupItems,
                                              $sizes, $img_name);
            if ($newFile)
                $_image_name_result[0] = $this->setDBupdateAfterConvert($newFile,
                                                                        $img_name);
        } catch (Exception $e) {
            echo $exc->getTraceAsString();
            echo "<br>" . $exc->getMessage();
            echo "<br>Line " . $exc->getLine();
            echo "<br>File " . $exc->getFile() . "<br/>";
            die('Stop converting');
        }

        unset($resizer);

        unlink($image1); // удаляем base-фото

        return $_image_name_result;
    }

    private function setDBupdateAfterConvert($newFile, $newFileName)
    {
        try {
            $size = getimagesize($newFile);
            $format = ImageConvertor::getFormatImages($size);

            return $newFileName . '.' . $format . "#{$size[0]}#{$size[1]}";
        } catch (Exception $exc) {
            echo $exc->getTraceAsString();
            echo "<p><b>" . $exc->getMessage() . "</b></p>";
        }
    }

}

?>