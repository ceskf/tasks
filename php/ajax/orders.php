<?php
require_once '../../inc/autoloader.php';

$orders = new orders();

$orders->getData($_GET['q']);
?>