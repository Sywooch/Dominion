<?php

require_once __DIR__ . '../../../application/configs/config.php';

/* @var $config Zend_Config_Ini */
//$config = new Zend_Config_Ini(APPLICATION_PATH . "/configs/application.ini", "production");
//Zend_Registry::set("config", $config);

$application = new Zend_Application(APPLICATION_ENV, APPLICATION_PATH . '/configs/application.ini');

$pictureTransformed = ImageResize_FacadeResize::resizeOrSave(
    's_324234',
    'd:\Projects\7560000.com.ua\NewVersion\src\tmp\16209.jpeg',
    'd:\Projects\7560000.com.ua\NewVersion\src\tmp',
    40,
    30
);


die();

$resizer = new ImageResize_Resize(40, 30, 20);

$newSize = $resizer->resize('d:\Projects\7560000.com.ua\NewVersion\src\tmp\16209.jpeg', 'd:\Projects\7560000.com.ua\NewVersion\src\tmp\16209_new.jpeg');

$w = $newSize->getWidth();
$h = $newSize->getHeight();

die();

$imagine = new Imagine();

$imageTest = $imagine->open('d:\Projects\756000.com.ua\src\44307111.jpg');

$box = $imageTest->getSize();

$box1 = $box->widen(100);
$box1 = $box->heighten(300);

$imageTest->resize($box1)
    ->save('d:\Projects\756000.com.ua\src\animated-resized-notflatten.jpg', array('flatten' => true));


require 'imageResizeLib/makeImageToStandart.php';
$resize = new Resize();
$resize->addImage();