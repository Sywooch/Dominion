<?php
/**
 * User: Ruslan
 * Date: 05.12.13
 * Time: 18:55
 */

class Converter
{

    private $model;

    /**
     * @param string $pathImage Check full path to the image for convert
     *
     * @return bool
     */
    public function existBaseImage($pathImage)
    {
        return file_exists($pathImage);
    }

    public function __construct($model){
        $this->model = $model;
    }

    public function  convertImage($itemID, $dbImageName, $baseImagePath, $saveImagePath,$newPictureName, $newWidth, $newHeight)
    {
        $pictureTransformed = ImageResize_FacadeResize::resizeOrSave(
            $newPictureName,
            $baseImagePath,
            $saveImagePath,
            $newWidth,
            $newHeight
        );

        if ($pictureTransformed) {
            $updateData[$dbImageName] =  "{$pictureTransformed->getName()}#{$pictureTransformed->getWidth()}#{$pictureTransformed->getHeight()}";
        } else {
            return null;
        }


        // Записываем в базу иноформацию о конвертированных картинках
        if (!empty($updateData)) {
            $updateData['NEED_RESIZE'] = null;

            $this->model->updateGlobalItem(
                $updateData,
                "ITEM_ID = $itemID");

            echo "Saved data for item ID {$row['ITEM_ID']} has been successfully\n";
        }

    }
}