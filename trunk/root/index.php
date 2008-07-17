<?php
	include('global.php');
	$controller = new root();
	$controller->is_connect_db = 0;
	include('controller.php');
?>
<?php include('head.php');?>
<body>
<script type="text/javascript">
<!--
	if (top.location != self.location){
		top.location = self.location;
	}
	if ($.browser.msie) {
		document.body.scroll = "no";
	}
//-->
</script>
<div id="left_iframe">
	<iframe frameborder="0" name="left" src="left.php" scrolling="yes"></iframe>
</div>
<div id="main_iframe">
	<iframe frameborder="0" name="main" src="main.php" scrolling="yes"></iframe>
</div>
<?php include('end.php');?>