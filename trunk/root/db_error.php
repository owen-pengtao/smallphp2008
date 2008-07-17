<?php
	include('global.php');
	include(PATH_CONTROLLER.'root_db_error.php');
	$controller = new root_db_error();

	include('controller.php');
?>
<?php include('head.php');?>
<?php
	$t = new table();
	$str = $t->table_start();
		$f = new form();
		$str_f = $f->form_start('?', array(), 'get');
		$arr_s = array('error_str' => '错误信息', 'id' => 'ID');
		$str_f.= $f->select(array('search_type'), $arr_s, $_GET['search_type']);

		$str_f.= $f->text(array('search_key', 'required', '请输入搜索关键词'), array($_GET['search_key']));

		$arr_s = array('id' => '默认排序', 'page_name' => '页面文件');
		$str_f.= $f->select(array('by'), $arr_s, $_GET['by']);

		$arr_s = array('DESC' => '降序↓', 'ASC' => '升序↑');
		$str_f.= $f->select(array('order'), $arr_s, $_GET['order']);

		$str_f.= $f->submit();
		$str_f.= $f->form_end();
	$str.= $t->tr_one($str_f, 'tr_one');
	$str.= $t->table_end();
	echo $str;
	unset($f, $str_f, $str);

	$t->set_op(0, 0);

	$td_width	= array('8%', '46%', '20%', '10%');
	$td_class	= array('', 't_td_left', '', '');
	$tr_th		= array('ID', '错误信息', '页面文件', '时间');
	$t->set_table($td_width, $td_class, $tr_th);

	$str = $t->table_start();
	$str.= $t->caption('所有数据库错误管理');
	$str.= $t->tr_th();

	foreach ((array)$tpl->rows as $v) {
		$arr = array(
				$v['id'], $v['error_str'], 
				'<a href="'.$v['url'].'" target="_blank">'.$v['page_name'].'</a>', date('y-m-d H:i', $v['timer'])
			);
		$str.= $t->tr_td_row($arr);
	}

	$str.= $t->tr_one_op();
	$str.= $t->tr_one($tpl->page);
	$str.= $t->table_end();
	echo $str;
?>
<?php include('end.php');?>