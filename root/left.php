<?php
	include('global.php');
	$controller = new root();
	$controller->is_connect_db = 0;
	include('controller.php');
?>
<?php include('head.php');?>
<div id="left">
	<a href="left.php" class="logo"></a>
	<div class="box">
		<div class="title"><h3>基本设置</h3></div>
		<ul>
			<li><a href="<?= SITE_URL?>" target="_blank">网站首页</a></li>
			<li><a href="main.php" target="main">后台首页</a></li>
			<li><a href="login.php?a=logout" target='_parent'>退出后台</a></li>
		</ul>
	</div>
	<div class="box">
		<div class="title"><h3>基本管理1</h3></div>
		<ul>
			<li><a href="category.php?channel=article" target="main">文章分类</a></li>
			<li><a href="category.php?channel=soft" target="main">软件分类</a></li>
			<li><a href="article.php" target="main">文章管理</a></li>
			<li><a href="tag.php" target="main">TAG 管理</a></li>
			<li><a href="comment.php" target="main">评论管理</a></li>
			<li><a href="pic_zoom.php" target="main">图片缩放</a></li>
		</ul>
	</div>
	<div class="box">
		<div class="title"><h3>系统管理</h3></div>
		<ul>
			<li><a href="db_error.php" target="main">数据库错误</a></li>
			<li><a href="user.php" target="main">用户管理</a></li>
		</ul>
	</div>
	<div class="box info">
		<div class="title"><h3>网站系统信息</h3></div>
		<span><?= $controller->meta->site_title?> 后台管理</span>
		<ul>
			<li>技术支持：<a href="<?= $controller->meta->author_url?>" target="_blank"><?= $controller->meta->author?></a></li>
			<li>版权所有：<a href="<?= SITE_URL?>"><?= $controller->meta->copyright?></a></li>
			<li>网站版本：V1.0</li>
		</ul>
	</div>
</div>
<script type="text/javascript">
<!--
	$(document).ready(function(){
		$(".title").click(function () {
			$('ul', $(this).parent()).toggle();
		});
	});
//-->
</script>
<?php include('end.php');?>
