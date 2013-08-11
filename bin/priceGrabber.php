#!/usr/bin/env php

<?php
/**
 * User: Rus
 * Date: 10.08.13
 * Time: 19:27
 */
require_once __DIR__ . '../../application/configs/config.php';

use Buzz\Client\Curl;
use Buzz\Message\Request;
use Buzz\Message\Response;

$curl = new Curl();

$request = new Request('POST', '/auth', 'http://api.brain.com.ua');

$response = new Response();


$options = array(
    CURLOPT_POSTFIELDS => array(
        'login' => 'Dominion',
        'password' => md5('barcelona')
    )
);

$curl->send($request, $response, $options);


$json = $response->getContent();
$g = 0;