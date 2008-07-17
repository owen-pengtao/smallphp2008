<?php
	include('global.php');
	include(PATH_CONTROLLER.'root_category.php');
	$channel = $_GET['channel'] ? $_GET['channel'] : 'article';
	$controller = new root_category($channel);

	include('controller.php');
?>
<?php include('head.php');?>
<?php
	$t = new table();
	$f = new form();
	$str = $t->table_start();
	$str.= $t->caption($channel.' 分类管理');

	if ($action=='add' OR $action=='edit') {
		$str.= $f->form_start('?a=save&channel='.$channel);
		$arr_td = array(
				array('父级分类', $f->select(array('pid'), $tpl->arr_opt, intval($_GET['pid']))),
				array('分类名称', $f->text(array('title', 'required', '', 't_text'), array(html_decode($tpl->row['title'])))),
			);
		$arr_td_width = array('20%', '80%');
		foreach ($arr_td AS $v) {
			$str.= $t->tr_td($v, array('', 't_td_left'), $arr_td_width);
		}
		$str.= $f->hidden('id', $tpl->row['id']);
	}else{
		$f->is_validate = 0;
		$str.= $f->form_start('?a=save_ranking&channel='.$channel);
		$str_ul = $controller->get_cat_ul(intval($_GET['cid']));
		$str.= $t->tr_one($str_ul);
	}

	$str.= $t->tr_one($f->submit());
	$str.= $f->form_end();
	$str.= $t->table_end();
	echo $str;
?>
<?php include('end.php');?>