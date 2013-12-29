#!/usr/bin/env php

<?php
require_once "CreateEnvironment.php";

$createEnvironment = new CreateEnvironment();
$createEnvironment->setType("selection");

$createEnvironment->deleteIndex();

echo "Index delete success";
