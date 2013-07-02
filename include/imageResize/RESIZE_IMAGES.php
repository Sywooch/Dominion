<?php

require_once realpath(dirname(__FILE__) . '../../../application/configs/') . '/config.php';
use Imagine\Image\Point;

//require_once 'imageResizeLib/config_mage.ini.php';
//ini_set('upload_max_filesize','10M');

//require ('core.php');




use Imagine\Image\Box;
use Imagine\Image\ImageInterface;
use Imagine\GD\Imagine;

$imagine = new Imagine();

$imageTest = $imagine->open('d:\Projects\756000.com.ua\src\44307.jpg');

$box = $imageTest->getSize();

$box1 = $box->widen(100);
$box1 = $box->heighten(300);

$imageTest->resize($box1)
    ->save('d:\Projects\756000.com.ua\src\animated-resized.jpg', array('flatten' => true));


require 'imageResizeLib/makeImageToStandart.php';
$resize = new Resize();
$resize->addImage();