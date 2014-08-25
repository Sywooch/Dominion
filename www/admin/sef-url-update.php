<?php

require_once "../../application/configs/config.php";

require __DIR__ . "/../../lib/CreateSEFU.class.php";
$sefu = new CreateSEFU();
$sefu->applySEFU();
//require $_SERVER['DOCUMENT_ROOT']."/lib/SEFUfromHtaccess.class.php";
//$obj = new SEFUfromHtaccess();
//$obj->apply();