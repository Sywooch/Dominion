<?php

require_once __DIR__ . '../../../application/configs/config.php';

use Symfony\Component\ClassLoader\UniversalClassLoader;

$loader = new UniversalClassLoader();

$loader->registerPrefixes(
    array(
        'ContextSearch_' => LIBRARY_PATH,
        "Helpers_" => APPLICATION_PATH,
        "models_" => APPLICATION_PATH,
        "Format_" => LIBRARY_PATH,
        "ImageResize_" => LIBRARY_PATH
    )
);
$loader->register();
$config = new Zend_Config_Ini(APPLICATION_PATH . "/configs/application.ini", "production");
Zend_Registry::set("config", $config);



$resizer = new ImageResize_Resize(100,200, 20);

$resizer->resize('d:\Projects\756000.com.ua\src\44307.jpg', 'd:\Projects\756000.com.ua\src\animated-resized-notflatten.jpg');

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