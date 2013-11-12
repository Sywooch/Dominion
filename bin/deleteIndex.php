#!/usr/bin/env php

<?php
require_once "CreateEnvironment.php";

$createEnvironment = new CreateEnvironment();
$createEnvironment->setType("products");

$createEnvironment->deleteIndex();

echo "Index delete success";
