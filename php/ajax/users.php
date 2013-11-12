<?php
require_once '../../inc/autoloader.php';

$users = new users;

$type = (isset($_POST['type'])) ? $_POST['type'] : $_GET['type'];

switch ($type){
    case 'check' : $users->checkUser($_POST); break;
    case 'checks' : $users->checksUser(); break;
    /*case 'add' : $users->addUser($_POST); break;
    case 'edit' : $users->editUser($_POST); break;
    case 'getUser' : $users->getUser($_POST); break;
    case 'editusers' : $users->regeditSpis($_POST);break;
    case 'shopList' : $shops->usersShoplist();break;
    case 'regedit_oper' : $users->regedit($_POST);break;*/
    default : $users->getData($_GET['q']);
}
?>
