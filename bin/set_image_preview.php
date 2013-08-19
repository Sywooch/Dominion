#!/usr/bin/env php

<?php
/**
 * User: Rus
 * Date: 15.07.13
 * Time: 0:06
 */

require_once __DIR__ . '../../application/configs/config.php';

$application = new Zend_Application(APPLICATION_ENV, APPLICATION_PATH . '/configs/application.ini');

$registry = Zend_Registry::getInstance();

$config = new Zend_Config_Ini(APPLICATION_PATH . '/configs/application.ini', 'production');

$db_config = array(
    'host' => $config->resources->db->params->host,
    'username' => $config->resources->db->params->username,
    'password' => $config->resources->db->params->password,
    'dbname' => $config->resources->db->params->dbname);

$adapter = $config->resources->db->adapter;

$db = Zend_Db::factory($adapter, $db_config);
$db->getConnection();

$registry->set('db_connect', $db);

$saveImagePath = SITE_PATH . "/images/it";
$baseImagePath = UPLOAD_PATH;

$settingsModel = new models_SystemSets();
$width = $settingsModel->getSettingValue('item_main_icon_x_small');
if (!$width) {
    throw new Exception('Should new width size. Set up into database in settings on name "item_main_icon_x_small"');
}

$height = $settingsModel->getSettingValue('item_main_icon_y_small');
if (!$width) {
    throw new Exception('Should new height size. Set up into database in settings on name "item_main_icon_y_small"');
}


$itemsModels = new models_Item();

$stm = $itemsModels->getAllImageBase();

while ($row = $stm->fetch()) {

    // Проверяем наличие картинки BASE
    // Если картинка существует - конвертим из ней
    // Если нет - конвертим из IMAGE3

    $baseImage = "{$baseImagePath}/{$row['BASE_IMAGE']}";

    $bigImage = "";

    if (!empty($row['IMAGE3'])) {
        $bigImage = preg_split('/#/', $row['IMAGE3']);
        $bigImage = "$saveImagePath/{$bigImage[0]}";
    }

    if (!file_exists($baseImage) && !file_exists($bigImage)) {
        // Картинок для конвертации совсем нет - следуюущая итерация
        continue;
    } elseif (file_exists($baseImage)) {
        // nothing to do
    } elseif (file_exists($bigImage)) {
        $baseImage = $bigImage;
    }


    $pictureTransformed = ImageResize_FacadeResize::resizeOrSave(
        "small_{$row['ITEM_ID']}",
        $baseImage,
        $saveImagePath,
        $width,
        $height
    );

    if ($pictureTransformed) {
        $itemsModels->updateGlobalItem(
            array('IMAGE0' => "{$pictureTransformed->getName()}#{$pictureTransformed->getWidth()}#{$pictureTransformed->getHeight()}"),
            "ITEM_ID = {$row['ITEM_ID']}");
    }
}

echo "All small items converted";