<?php
	include('global.php');
	$controller = new root();
	$controller->is_connect_db = 0;
	include('controller.php');
?>
<?php include('head.php');?>
<?php
	echo '<pre style="text-align:left">';
	print_r(get_defined_constants());
	echo '</pre>';
?>
<?php include('end.php');?>