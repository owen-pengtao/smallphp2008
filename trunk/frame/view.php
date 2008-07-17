<?php
	$view_file = PATH_VIEWS.basename($_SERVER['PHP_SELF']);

	if(file_exists($view_file)){
		echo $controller->head_start();
		if ($controller->is_include_head){
			include(PATH_VIEWS.'head.php');
			include($view_file);
			include(PATH_VIEWS.'end.php');
		}else{
			include($view_file);
		}
		echo $controller->head_end();
	}else{
		exit('不能找到模板文件.');
	}
?>