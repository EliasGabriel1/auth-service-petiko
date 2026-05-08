<?php
require 'vendor/autoload.php';
$generator = new \OpenApi\Generator();
$openapi = $generator->generate(['app/OpenApi.php'], null, false);
var_dump($openapi);
