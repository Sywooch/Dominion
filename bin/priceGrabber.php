<?php
/**
 * User: Rus
 * Date: 24.08.13
 * Time: 21:48
 */

require_once __DIR__ . "/../application/configs/config.php";


$application = new Zend_Application(APPLICATION_ENV, APPLICATION_PATH . '/configs/application.ini');
//
//$registry = Zend_Registry::getInstance();

$export = new BrainPriceImport_Connect(
    new Buzz\Client\Curl(), new Buzz\Message\Request(), new Buzz\Message\Response(),
    'adlabs',
    'Ru$LAN'
);


$export->setHost('http://api.brain.com.ua');

$sid =  $export->getAuthSID();



$options = array();

$options[CURLOPT_POSTFIELDS] = array('login' => 'adlabs', 'password' => md5('Ru$LAN'));

$curl->send($request, $response, $options);

$authToken = json_decode($response->getContent());



$request->setResource("/categories/{$authToken->result}");
$request->setMethod('GET');

$curl->send($request, $response);

$g = 88;