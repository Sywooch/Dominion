<?php

set_time_limit(0);


define('IMAGE_UPLOAD_PATH', ROOT_PATH.'/upload_images');
define('CC_IMAGE_UPLOAD_PATH', ROOT_PATH.'/cc_images');
//define('IMAGE_TARGET_PATH','/var/www/potapova/data/www/potapova.h7.data-xata.net/images');


define('sufixGall', 'gall');
define('sufixAdd', 'add');
define('sufixPathTech', 'tech');
define('modeImages', 0777);

/**
 * Максимальное число для выборки карточек товара.
 * Необходимое ограничение чтобы серврер не загнулся
 */
define('getImageOneTime', 1);

define('pathToImages', SITE_PATH . '/images/it/');
define('pathToImagesGroupItems', SITE_PATH . '/images/it_link/');

define('pathToWatermark', '');

define('Size_b_X', 500); // большая image3
define('Size_b_Y', 500);

define('Size_X', 200);  // средняя 2
define('Size_Y', 200);

define('Size_s_X', 100);  // мал 1
define('Size_s_Y', 100);

define('Size_gruop_X', 70);  // картинка для группы товаров
define('Size_gruop_Y', 70);

define('Size_gallery_X', 500);  // картинка для доп фото товара большая
define('Size_gallery_Y', 500);
define('Size_gallery_s_X', 70); // картинка для доп фото товара маленькая 
define('Size_gallery_s_Y', 70);


define('DifferenceInArea', 20);  // разница в процентах между площадями картинок

error_reporting(E_ALL);
?>