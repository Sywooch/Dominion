#!/usr/bin/env php

<?php
/**
 * Конвертация картинок в самые маленькие для поиска
 *
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
$baseImagePath = UPLOAD_IMAGES;

$params = new ImageResize_PictureSizeParams(new models_SystemSets());

// Сетим размеры для иконок
$params->setKey('small_icon', 'item_main_icon_x_small', 'item_main_icon_y_small');

// Размер самой большой картинки
$params->setKey('big', 'item_main_big_x', 'item_main_big_y');

// Средняя картинка которая показывается на странице по умолчанию
$params->setKey('medium', 'item_main_small_x', 'item_main_small_y');

// Картинка превью в каталоге
$params->setKey('small', 'item_main_icon_x', 'item_main_icon_y');

$g = ImageResize_PictureSizeParams::getSizes('small');


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
        echo "Cant find a base picture $baseImage \n";
        continue;
    } elseif (file_exists($baseImage)) {
        // nothing to do
    } elseif (file_exists($bigImage)) {
        $baseImage = $bigImage;
    }

    $updateData = array();
    // Генерим самую маленькую картинку для поиска
    $params = ImageResize_PictureSizeParams::getSizes('small_icon');
    $pictureTransformed = ImageResize_FacadeResize::resizeOrSave(
        "small_{$row['ITEM_ID']}",
        $baseImage,
        $saveImagePath,
        $params['width'],
        $params['height']
    );

    if ($pictureTransformed) {
        $updateData['IMAGE0'] = "{$pictureTransformed->getName()}#{$pictureTransformed->getWidth()}#{$pictureTransformed->getHeight()}";
    }

    // Генерим самую большую картинку
    $params = ImageResize_PictureSizeParams::getSizes('big');
    $pictureTransformed = ImageResize_FacadeResize::resizeOrSave(
        "b_{$row['ITEM_ID']}",
        $baseImage,
        $saveImagePath,
        $params['width'],
        $params['height']
    );

    if ($pictureTransformed) {
        $updateData['IMAGE3'] = "{$pictureTransformed->getName()}#{$pictureTransformed->getWidth()}#{$pictureTransformed->getHeight()}";
    }


    // Генерим среднюю картинку
    $params = ImageResize_PictureSizeParams::getSizes('big');
    $pictureTransformed = ImageResize_FacadeResize::resizeOrSave(
        "{$row['ITEM_ID']}",
        $baseImage,
        $saveImagePath,
        $params['width'],
        $params['height']
    );

    if ($pictureTransformed) {
        $updateData['IMAGE2'] = "{$pictureTransformed->getName()}#{$pictureTransformed->getWidth()}#{$pictureTransformed->getHeight()}";
    }

    if ($pictureTransformed) {
        $itemsModels->updateGlobalItem(
            $updateData,
            "ITEM_ID = {$row['ITEM_ID']}");
    }


}

echo "All small items converted";