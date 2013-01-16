<?php
session_start();
include('kcaptcha.php');

$captcha = new KCAPTCHA();

$mode = !empty($_GET['mode']) ? $_GET['mode']:'';

$_SESSION['biz_captcha'] = $captcha->getKeyString();

?>