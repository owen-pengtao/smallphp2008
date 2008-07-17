<?php
class root_user extends root{
	function __construct(){
		$this->tab = 'users';
		$this->_init_param();
	}
	function save(){
		$row = array(
				'username' => $_POST['username'],
			);
		$password = $_POST['password'];
		$password ? $row['password'] = md5($password) : '';
		$this->save_row($row, array('grade' => 9));
	}
}
?>