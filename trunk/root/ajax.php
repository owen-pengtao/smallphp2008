<?php
	include('global.php');
	include(PATH_CONTROLLER.'root_ajax.php');
	$controller = new root_ajax();
	include('controller.php');
	
	$a = $_GET['a'] ? $_GET['a'] : $_POST['a'];
	echo $controller->$a();
?>