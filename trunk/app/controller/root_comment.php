<?php
class root_comment extends root{
	function __construct(){
		$this->tab = T.'comments';
		$this->_init_param();
	}
	function del(){
		$id = intval($_GET['id']);
		$row = $this->db->row_select_one($this->tab, 'id='.$id, 'c_id, r_id');
		if ($this->db->row_delete($this->tab, 'id='.$id)) {
			$this->db->update_op($this->c_tab[$row['c_id']], 'sum_comments', '-', 'c_id='.$row['c_id'].' AND r_id='.$row['r_id'], 2);
			header_go($_SERVER['PHP_SELF']);
		}
	}
}
?>