<?php
	include('global.php');
	include(PATH_CONTROLLER.'root_tag.php');
	$controller = new root_tag();

	include('controller.php');
?>
<?php include('head.php');?>
<?php
	$t = new table();
	if ($action=='add' OR $action=='edit') {
		$str = $t->table_start();
		$str.= $t->caption('标签管理');
		$f = new form();
		$str.= $f->form_start('?a=save');
			$arr_td = array(
					array('TAG标签', $f->text(array('title', 'required', '', 't_text'), array($tpl->row['title']))),
				);
			$arr_td_width = array('12%', '88%');
			foreach ($arr_td AS $v) {
				$str.= $t->tr_td($v, array('', 't_td_left'), $arr_td_width);
			}
		$str.= $t->tr_td_submit();
		$str.= $f->hidden('id', $tpl->row['id']);
		$str.= $f->form_end();
		$str.= $t->table_end();
	}else{
		$str = $t->table_start();
			$f = new form();
			$str_f = $f->form_start('?', array(), 'get');
			$arr_s = array('title' => 'TAG', 'id' => 'ID');
			$str_f.= $f->select(array('search_type'), $arr_s, $_GET['search_type']);

			$str_f.= $f->text(array('search_key', 'required', '请输入搜索关键词'), array($_GET['search_key']));

			$arr_s = array('id' => '默认排序', 'title' => 'TAG');
			$str_f.= $f->select(array('by'), $arr_s, $_GET['by']);

			$arr_s = array('DESC' => '降序↓', 'ASC' => '升序↑');
			$str_f.= $f->select(array('order'), $arr_s, $_GET['order']);

			$str_f.= $f->submit();
			$str_f.= $f->form_end();
		$str.= $t->tr_one($str_f, 'tr_one');
		$str.= $t->table_end();
		echo $str;
		unset($f, $str_f, $str);

		$t->set_op();

		$td_width	= array('8%', '36%', '10%', '10%', '10%', '10%');
		$td_class	= array('', 't_td_left', '', '', '', '');
		$tr_th		= array('ID', 'TAG', '文章ID', '使用次数', '点击次数', '时间');
		$t->set_table($td_width, $td_class, $tr_th);

		$str = $t->table_start();
		$str.= $t->caption('所有TAG管理');
		$str.= $t->tr_th();

		foreach ((array)$tpl->rows as $v) {
			$arr = array(
					$v['id'], '<a href="'.SITE_URL.'/article/tag.php?tag='.rawurlencode($v['title']).'" target="_blank">'.$v['title'].'</a>', 
					$v['r_id'], $v['sum_tags'], $v['visit'], date('y-m-d H:i', $v['timer'])
				);
			$str.= $t->tr_td_row($arr);
		}

		$str.= $t->tr_one_op();
		$str.= $t->tr_one($tpl->page);
		$str.= $t->table_end();
	}
	echo $str;
?>
<?php include('end.php');?>