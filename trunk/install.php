<?php
	include('config.php');
	$arr = array(
				PATH_UP, PATH_UP_IMAGES, PATH_UP_IMAGES_SMALL, 
				PATH_TMP, PATH_CACHES
			);
	foreach ($arr AS $v){
		if(!file_exists($v)){
			mkdir($v, 0777);
			chmod($v, 0777);
			echo $v.' create successful.<br/>';
		}else{
			echo $v.' is exists.<br/>';
		}
	}
?>