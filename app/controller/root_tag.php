<?php
class root_tag extends root{
	function __construct(){
		$this->tab = T.'tags';
		$this->_init_param();
	}
	function save(){
		$row = array(
				'title' => $_POST['title'],
			);
		$this->save_row($row, array('timer' => $_SERVER['REQUEST_TIME']));
	}
}
?>