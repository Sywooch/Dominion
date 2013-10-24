<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 24.10.13
 * Time: 16:51
 */


class ImageResize_PictureSizeParams
{
    /**
     * Model data for get setting picture
     *
     * @var models_SystemSets
     */
    private $settingsModel;

    static private $picturesParams = array();

    public function __construct($settingsModel)
    {
        $this->settingsModel = $settingsModel;
    }

    /**
     * @param string $keyName           Key name
     * @param int    $dataBaseKeyWidth  key name in DB for Width this picture size
     * @param        $dataBaseKeyHeight key name in DB for Height this picture size
     *
     * @throws Exception
     */
    public function setKey($keyName, $dataBaseKeyWidth, $dataBaseKeyHeight)
    {

        $param = $this->settingsModel->getSettingValue($dataBaseKeyWidth);
        if (!$param) {
            throw new Exception("Cant get a data from database in settings on name $dataBaseKeyWidth");
        }

        self::$picturesParams[$keyName]['width'] = $param;

        $param = $this->settingsModel->getSettingValue($dataBaseKeyHeight);
        if (!$param) {
            throw new Exception("Cant get a data from database in settings on name $dataBaseKeyHeight");
        }

        self::$picturesParams[$keyName]['height'] = $param;
    }

    static public function getSizes($keyName)
    {
        if (!empty(self::$picturesParams[$keyName])) {
            return self::$picturesParams[$keyName];
        } else {
            throw new RuntimeException("Key $keyName hasn't set. Should set first");
        }
    }
} 