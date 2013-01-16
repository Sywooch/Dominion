<?php
require_once 'imageResizeLib/config_mage.ini.php';
//ini_set('upload_max_filesize','10M');

require ('core.php');
require 'imageResizeLib/makeImageToStandart.php';
$resize = new Resize();
$resize->addImage();