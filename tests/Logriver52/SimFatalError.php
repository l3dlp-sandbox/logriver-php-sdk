<?php

ini_set('error_reporting', E_ALL);
ini_set('display_errors', 0);
ini_set('error_log', '/dev/null');

require(dirname(__FILE__).'/../bootstrap52.php'); // __DIR__ appears only in Php 5.3

$client = Logriver_Client::init('12345baced3340aa940fc402e652d30d');
$client->setDebugMode(1);
$client->startListener();

functionNotExists();
