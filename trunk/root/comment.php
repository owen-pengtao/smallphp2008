<?php
	include('global.php');
	include(PATH_CONTROLLER.'root_comment.php');
	$controller = new root_comment();

	include('controller.php');
?>
<?php include('head.php');?>
<?php
	$t = new table();
	$str = $t->table_start();
	$arr_td = array(
					'<a href="?">所有评论列表</a>',
					'<a href="?where=is_pass=0">未审核评论</a>',
				);
	$str.= $t->tr_td($arr_td);
		$f = new form();
		$str_f = $f->form_start('?', array(), 'get');
		$arr_s = array('content' => '内容', 'id' => 'ID');
		$str_f.= $f->select(array('search_type'), $arr_s, $_GET['search_type']);

		$str_f.= $f->text(array('search_key', 'required', '请输入搜索关键词'), array($_GET['search_key']));

		$arr_s = array('is_pass=0' => '未通过');
		$str_f.= $f->checkbox(array('other_where[]'), $arr_s, $_GET['other_where']);

		$arr_s = array('id' => '默认排序', 'title' => '标题', 'c_id' => '类别频道');
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

	$td_width	= array('8%', '46%', '10%', '10%', '10%');
	$td_class	= array('', 't_td_left', '', '', '');
	$tr_th		= array('ID', '内容', '类别频道', '评论者', '时间');
	$t->set_table($td_width, $td_class, $tr_th);

	$str = $t->table_start();
	$str.= $t->caption('所有评论管理');
	$str.= $t->tr_th();

	foreach ((array)$tpl->rows as $v) {
		$arr = array(
				$v['id'], '<a href="'.SITE_URL.'/comment.php?c_id='.$v['c_id'].'&r_id='.$v['r_id'].'" target="_blank"'.($v['is_pass']?'':' class="no_pass"').'>'.$v['content'].'</a>', 
				$v['c_id'], $v['username'], date('y-m-d H:i', $v['timer'])
			);
		$str.= $t->tr_td_row($arr);
	}

	$str.= $t->tr_one_op(array('is_pass-0'=>"不通过", 'is_pass-1'=>"通过"));
	$str.= $t->tr_one($tpl->page);
	$str.= $t->table_end();
	echo $str;
?>
<?php include('end.php');?>