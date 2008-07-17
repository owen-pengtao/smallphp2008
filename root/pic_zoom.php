<?php
	include('global.php');
	include(PATH_CONTROLLER.'root_pic_zoom.php');
	$controller = new root_pic_zoom();

	include('controller.php');
?>
<?php include('head.php');?>
<?php
	$t = new table();

	$str = $t->table_start();
	$str.= $t->caption('图片缩放');
	$f = new form();
	$str.= $f->form_start('?a=save');
	$arr_td = array(
			array('选择文件', $f->file(array('file', 'required'))),
			array('宽、高', $f->text(array('width', 'number')).' X '.$f->text(array('height', 'number')).'100X100, 150X200, 133X100X5'),
			array('文件名称', $f->text(array('filename')).'若为空，则文件名 宽_高.jpg<br/>'.PATH_UP_IMAGES_SMLLL.'<br/>'.URL_UP_IMAGES_SMLLL),
		);
	$arr_td_width = array('12%', '88%');
	foreach ($arr_td AS $v) {
		$str.= $t->tr_td($v, array('', 't_td_left'), $arr_td_width);
	}
	$str.= $t->tr_td_submit();
	$str.= $f->form_end();
		$str_img = $tpl->small_pic ? '图片访问地址：'.$tpl->small_pic.'<br/><a href="'.$tpl->small_pic.'" target="_blank"><img src="'.$tpl->small_pic.'" /></a>':'';
	$str.= $str_img ? $t->tr_one($str_img):'';
	$str.= $t->table_end();
	echo $str;
?>
<?php include('end.php');?>