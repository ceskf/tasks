<?php
	if (isset($_GET['exit'])){
		require_once "class/class.users.php";

		$u = new users;
		$u->close();
	}
?>
<!DOCTYPE html>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<link rel="stylesheet" type="text/css" media="screen" href="../libs/bootstrap/css/bootstrap.min.css" />
		<link rel="stylesheet" href='../css/auth.css'/>
		<link rel="stylesheet" type="text/css" media="screen" href="../../libs/jqgrid/css/ui.jqgrid.css" />
		<title>Укрдомик панель управления</title>
		<script src="../libs/jquery-2.0.3.js"></script>
		<script src="../libs/bootstrap/js/bootstrap.min.js"></script>
		<script src="../libs/jqgrid/js/i18n/grid.locale-ru.js"></script>
		<script src="../libs/jqgrid/js/jquery.jqGrid.min.js"></script>
		<script src="../libs/angular.min.js"></script>
		<script src="../js/auth.js"></script>
	</head>
	<body>
    	<div id="auth" class="modal hide fade">
    		<div class="modal-header">
    			<h3>Укрдомик панель управления</h3>
    		</div>
    		<div class="modal-body">
    			<p><input type="text" placeholder="Логин" class="input_text" id="login"></p>
    			<p><input type="password" placeholder="Пароль" class="input_text" id="pwd"></p>
    			<p align="center">
    				<img src="../img/ajax-loader.gif" id="loader">
    				<span id="error">Не правильный логин/пароль</span>
    			</p>
    		</div>
    		<div class="modal-footer">
    			<a href="#" class="btn btn-primary" id="enter">Войти</a>

    		</div>
    	</div>
	</body>
</html>