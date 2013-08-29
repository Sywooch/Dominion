<?php
/**
 * User: Rus
 * Date: 24.08.13
 * Time: 21:48
 */

require_once __DIR__ . "/../application/configs/config.php";


$curl = new Buzz\Client\Curl();

$request = new Buzz\Message\Request();

$response = new Buzz\Message\Response();


$request->setHost('http://api.brain.com.ua');
$request->setMethod('POST');
$request->setResource('/auth');

$options = array();

$options[CURLOPT_POSTFIELDS] = array('login' => 'adlabs', 'password' => md5('Ru$LAN'));

$curl->send($request, $response, $options);

$authToken = json_decode($response->getContent());



$request->setResource("/categories/{$authToken->result}");
$request->setMethod('GET');

$curl->send($request, $response, $options);

$g = 88;