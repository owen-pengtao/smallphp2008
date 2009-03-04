<?php
	include('global.php');
	include(PATH_CONTROLLER.'root_user.php');
	$controller = new root_user();

	include('controller.php');
?>
<?php include('head.php');?>
<?php
	if ($action=='add' OR $action=='edit') {
		$t = new table();

		$str = $f->form_start('?a=save');
		$str.= $t->table_start();
		$str.= $t->caption('用户管理');
		$f = new form();
			$arr_td = array(
					array('用户名', $f->text(array('username', 'required', '', 't_text'), array($tpl->row['username']))),
					array('密码', $f->password(array('password', 'password', '', 't_text'), '', '')),
					array('重复密码', $f->password(array('password', 'password_re', '', 't_text'), '', '')),
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
		$t->set_op();

		$td_width	= array('8%', '46%', '30%');
		$td_class	= array('', 't_td_left', '');
		$tr_th		= array('ID', '用户名', '管理级别');
		$t->set_table($td_width, $td_class, $tr_th);

		$str = $t->table_start();
		$str.= $t->caption('所有用户管理');
		$str.= $t->tr_th();

		$arr_admin = array(9 => '管理员');
		foreach ((array)$tpl->rows as $v) {
			$arr = array(
					$v['id'], $v['username'], $arr_admin[$v['grade']]
				);
			$str.= $t->tr_td_row($arr);
		}

		$str.= $t->tr_one_op();
		$str.= $t->tr_one($tpl->page);
		$str.= $t->table_end();
		echo $str;
	}
?>
<?php include('end.php');?>