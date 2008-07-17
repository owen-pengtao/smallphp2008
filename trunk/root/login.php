<?php
	include('../config.php');

	include(PATH_CONTROLLER.'root.php');
	include(PATH_CONTROLLER.'root_login.php');
	$controller = new root_login();

	include('controller.php');
?>
<?php include('head.php');?>
<div id="login_box">
	<div class="login">
		<div class="title">登录　<?= $controller->meta->site_title?>　后台管理</div>
		<form action="login.php?a=login" method="post" onsubmit="return check()">
			<label for="username">用户名：<input type="text" id="username" name="username" maxlength="20" tabindex="1" value=''/></label>
			<label for="password">密　码：<input type="password" id="password" name="password" maxlength="32" tabindex="2" /></label>
			<label for="vcode">验证码：<input name='vcode' id="vcode" type='text' size='10' maxlength="4" tabindex="3">&nbsp;<img src='imgchk.php' align='absmiddle' border='0'/></label>
			<p>
				<input type="submit" class="btxp" tabindex="4" value="提 交"/>
				<input type="reset" class="btxp" tabindex="5" value="重 置">
			</p>
		</form>
	</div>
</div>
<script type="text/javascript">
<!--
	function g(id) {
		return document.getElementById(id);
	}
	function check(){
		var bool = false;
		if (g("username").value==""){
			alert("请输入用户名");
			g("username").focus();
		}else if(g("password").value==""){
			alert("请输入密码！");
			g("password").focus();
		}else if(g("vcode").value==""){
			alert("请输入验证码！");
			g("vcode").focus();
		}else{
			bool = true;
		}
		return bool;
	}
//-->
</script>
<?php include('end.php');?>