<?php
require_once '../../inc/autoloader.php';

$calls = new calls();

$calls->getData($_GET['q']);
?>