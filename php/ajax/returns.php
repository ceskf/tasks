<?php
require_once '../../inc/autoloader.php';

$returns = new returns();

$returns->getData($_GET['q']);
?>