<?php
	$ctl_file = PATH_CONTROLLER.$controller.'.php';	//获取控制器文件

	if(file_exists($ctl_file)){
		include($ctl_file);
		$controller = new $controller();
		$controller->is_connect_db ? $controller->connect_db():'';
		$controller->meta();
		if (method_exists($controller, $action)) {
			$controller->$action();
		}else{
			$controller->index();
		}
		$tpl = $controller->tpl;
	}else{
		exit('不能找到控制器文件.');
	}
?>