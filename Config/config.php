<?php
require '../vendor/autoload.php';

$mongoClient = new MongoDB\Client("mongodb://localhost:27017");
$db = $mongoClient->selectDatabase('TFG');

