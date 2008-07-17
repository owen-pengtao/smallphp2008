<?php
class html extends controller{
	function __construct(){
		$this->is_connect_db = 0;
		$this->is_include_head = 0;
	}
	function index(){
		$this->meta->set_title('haha');
	}
}
?>