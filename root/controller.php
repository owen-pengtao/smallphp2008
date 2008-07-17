<?php
	$action = $_GET['a'] ? $_GET['a']:'index';
	$controller->meta();
	$controller->meta->set_css('sp_frame.css');
	if (method_exists($controller, $action)){
		$controller->is_connect_db ? $controller->connect_db():'';
		$controller->$action();
		$tpl = (object)$controller->tpl;
	}
?>