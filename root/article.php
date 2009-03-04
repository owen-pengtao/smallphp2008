<?php
	include('global.php');
	include(PATH_CONTROLLER.'root_article.php');
	$controller = new root_article();

	include('controller.php');
?>
<?php include('head.php');?>
<?php
	if ($action=='add' OR $action=='edit') {
		include('edit.php');
		$t = new table();
		$f = new form();

		$str = '';
		$str.= $f->form_start('?a=save');
		$str.= $t->table_start();
		$str.= $t->caption('文章管理');
			$arr_td = array(
					array('类别', $f->select(array('cid', 'required'), $tpl->arr_opt, $tpl->row['cid'])),
					array('标题', $f->text(array('title', 'required', '', 't_text'), array($tpl->row['title']))),
					array('内容', $f->textarea(array('content'), $tpl->row['content'], array(110, 22))),
					array('标签', $f->text(array('tag', '', '', 't_text'), array($tpl->row['tag']))),
					array('简介', $f->textarea(array('info'), $tpl->row['info'], array(55, 5))),
					array('作者', $f->text(array('author', '', '', 't_text'), array($tpl->row['author']))),
					array('转自', $f->text(array('copy_from', '', '', 't_text'), array($tpl->row['copy_from'])).' URL'.$f->text(array('copy_url', '', '', 't_text'), array($tpl->row['copy_url']))),
					array('是否显示', $f->radio(array("is_pass"), array(1=>"显示", 0=>"不显示"), isset($tpl->row['is_pass']) ? intval($tpl->row['is_pass']):1)),
				);
			$arr_td_width = array('12%', '88%');
			foreach ($arr_td AS $v) {
				$str.= $t->tr_td($v, array('', 't_td_left'), $arr_td_width);
			}
		$str.= $t->tr_td_submit();
		$str.= $f->hidden('id', $tpl->row['id']);
		$str.= $t->table_end();
		$str.= $f->form_end();
		echo $str;
	}else{
		$t = new table();
		$str = $t->table_start();
		$arr_td = array(
						'<a href="?">所有文章列表</a>',
						'<a href="?where=cid=0">未分类文章</a>',
						'<a href="?where=is_pass=0">未审核文章</a>',
					);
		$str.= $t->tr_td($arr_td);
			$f = new form();
			$str_f = $f->form_start('?', array(), 'get');
			$arr_s = array('title' => '标题', 'content' => '内容', 'id' => 'ID');
			$str_f.= $f->select(array('search_type'), $arr_s, $_GET['search_type']);

			$str_f.= $f->text(array('search_key', 'required', '请输入搜索关键词'), array($_GET['search_key']));

			$arr_s = array('cid=0' => '未分类', 'is_pass=0' => '未通过');
			$str_f.= $f->checkbox(array('other_where[]'), $arr_s, $_GET['other_where']);

			$arr_s = array('id' => '默认排序', 'title' => '标题', 'cid' => '分类');
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
		$td_width	= array('8%', '46%', '10%', '10%', '10%');
		$td_class	= array('', 't_td_left', '', '', '');
		$tr_th		= array('ID', '标题', '类别', '作者', '时间');
		$t->set_table($td_width, $td_class, $tr_th);

		$str = $t->table_start();
		$str.= $t->caption('所有文章管理');
		$str.= $t->tr_th();

		foreach ((array)$tpl->rows as $v) {
			$arr = array(
					$v['id'], '<a href="show.php?id='.$v['id'].'" target="_blank"'.($v['is_pass']?'':' class="no_pass"').'>'.$v['title'].'</a>'.($v['sum_comments'] ? ' &nbsp; <a href="comment.php?c_id=1&r_id='.$v['id'].'" class="a_red">('.$v['sum_comments'].'条评论)</a>' : ''), 
					'<a href="?cid='.$v['cid'].'">'.$v['cid_name'].'</a>', $v['author'], date('Y-m-d', $v['timer'])
				);
			$str.= $t->tr_td_row($arr);
		}

		$str.= $t->tr_one_op(array('is_pass-0'=>"不通过", 'is_pass-1'=>"通过"));
		$str.= $t->tr_one($tpl->page);
		$str.= $t->table_end();
		echo $str;
	}
?>
<?php include('end.php');?>