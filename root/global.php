<?php
	include('../config.php');
	session_start();
	if (empty($_SESSION['user']) OR $_SESSION["is_root"]!=1) {
		header('Location: login.php');exit;
	}
	include(PATH_CONTROLLER.'root.php');
?>