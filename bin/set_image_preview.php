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

use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

$application = new Zend_Application(APPLICATION_ENV, APPLICATION_PATH . '/configs/application.ini');

$registry = Zend_Registry::getInstance();

$config = new Zend_Config_Ini(APPLICATION_PATH . '/configs/application.ini', 'production');

$db_config = array(
  'host' => $config->resources->db->params->host,
  'username' => $config->resources->db->params->username,
  'password' => $config->resources->db->params->password,
  'dbname' => $config->resources->db->params->dbname
);

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

// Картинка превью доп фотки
$params->setKey('additional_small', 'item_small_x', 'item_small_y');

// Картинка большая доп. фотки
$params->setKey('additional_big', 'item_big_x', 'item_big_y');


$itemsModels = new models_Item();

$stm = $itemsModels->geImagesNeedResize();

/**
 * @param        $image
 * @param        $path
 *
 * @return null
 */
function findPictures($image, $path)
{
    $finder = new Finder();

    $iterator = $finder->files()->name($image)->in($path);

    /** @var SplFileInfo $baseImage */
    $baseImage = iterator_to_array($iterator, false);

    if (!empty($baseImage)) {
        return $baseImage[0]->getPathname();
    }
    else {
        return null;
    }

}


//$iteratorBase = $finder->files()->in($baseImagePath);
//
//$g = $iteratorBase->getIterator();


while ($row = $stm->fetch()) {


    // Проверяем наличие картинки BASE
    // Если картинка существует - конвертим из ней
    // Если нет - конвертим из IMAGE3
    $pattern = '/(?<=\\\|\/)?[^\\\]+(?=\.)/i';
    $baseImage = null;

    // Если нашили по паттеруну - значит имя базовой картинки ОК
    if (preg_match($pattern, $row['BASE_IMAGE'], $matches)) {
        // find file base image in directory
        $baseImage = findPictures("{$matches[0]}.*", $baseImagePath);
    }
    else {
        $baseImage = null;
    }

    // Если базовою картинку не нашли, то пытаемся сконвертнуть из текущей IMAGE3
    // разумеется если она есть
    if (!$baseImage && !empty($row['IMAGE3'])) {
        $baseImage = findPictures(preg_split('/#/', $row['IMAGE3'])[0], $saveImagePath);
    }

    if (!$baseImage) {
        // Картинок для конвертации совсем нет - следуюущая итерация
        echo "Cant find any base picture {$row['BASE_IMAGE']} \n";
        continue;
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
      $params['height'],
      10,
      false
    );

    if ($pictureTransformed) {
        $updateData['IMAGE3'] = "{$pictureTransformed->getName()}#{$pictureTransformed->getWidth()}#{$pictureTransformed->getHeight()}";
    }
    else {
        $updateData['IMAGE3'] = null;
    }


    // Генерим среднюю картинку
    $params = ImageResize_PictureSizeParams::getSizes('medium');
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

    // Генерим среднюю картинку
    $params = ImageResize_PictureSizeParams::getSizes('small');
    $pictureTransformed = ImageResize_FacadeResize::resizeOrSave(
      "s_{$row['ITEM_ID']}",
      $baseImage,
      $saveImagePath,
      $params['width'],
      $params['height']
    );

    if ($pictureTransformed) {
        $updateData['IMAGE1'] = "{$pictureTransformed->getName()}#{$pictureTransformed->getWidth()}#{$pictureTransformed->getHeight()}";
    }

    // Записываем в базу иноформацию о конвертированных картинках
    if (!empty($updateData)) {
        $updateData['NEED_RESIZE'] = null;

        $itemsModels->updateGlobalItem(
          $updateData,
          "ITEM_ID = {$row['ITEM_ID']}");

        echo "Saved data for item ID {$row['ITEM_ID']} has been successfully\n";
    }

    // Конверим дополнительные фотки
    $finder = new Finder();

    $iterator = $finder
      ->files()
      ->name("/{$row['ITEM_ID']}_[1-5]/")
      ->depth(0)
      ->in($baseImagePath);

    //TODO: Надо заполучить ITEM_ITEM_ID в таблице ITEM_PHOTO из сиквенцов
    $itemItemId = 0;

    /**@var SplFileInfo $file */
    foreach ($iterator as $file) {
        // Генерим превью картинку доп фото
        $params = ImageResize_PictureSizeParams::getSizes('additional_small');
        $pictureTransformed = ImageResize_FacadeResize::resizeOrSave(
          "{$row['ITEM_ID']}_{$itemItemId}_img_sm",
          $file->getRealpath(),
          $saveImagePath,
          $params['width'],
          $params['height']
        );

        // Генерим большую картинку доп фото
        $params = ImageResize_PictureSizeParams::getSizes('additional_big');
        $pictureTransformed = ImageResize_FacadeResize::resizeOrSave(
          "{$row['ITEM_ID']}_{$itemItemId}_img_lrg",
          $file->getRealpath(),
          $saveImagePath,
          $params['width'],
          $params['height']
        );

    }

}

echo "All small items converted";