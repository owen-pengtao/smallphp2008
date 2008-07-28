<?php
class root extends controller{
	function meta(){
		parent::meta();
		$this->meta->set_css(array('root.css'));
		$this->meta->set_js(array('root.js'));
		$this->auto_id = $this->auto_id ? $this->auto_id : 'id';
	}
	function index(){
		if ($this->tab) {
			$this->_get_items_op();

			$pl = new pagelist($this->db);
			$pl->table['id'] = $this->auto_id;
			$arr = $pl->get_rows($this->tab, $this->where, $this->auto_id?$this->auto_id:$this->by, $this->order);
			$this->tpl = $arr;
		}
		return $arr;
	}
	function edit(){
		$id = intval($_GET['id']);
		$row = $this->db->row_select_one($this->tab, $this->auto_id.'='.$id);
		$this->tpl['row'] = $row;
		return $row;
	}
	function del(){
		$id = intval($_GET['id']);
		if ($this->_del($this->tab, $this->auto_id.'='.$id)) {
			header_go($_SERVER['PHP_SELF']);
		}
	}
	function _init_param(){
		$this->where	= $this->_get_where();
		$this->by		= $_GET['by'];
		$this->order	= $_GET['order'];	//DESC or ASC
	}
	function _del($tab, $where) {
		$this->db->row_delete($tab, $where);
	}
	function _update($tab, $row, $where) {
		return $this->db->row_update($tab, $row, $where);
	}
	/*
	 * 处理post提交的批量事件
	 */
	function _get_items_op(){
		if (!isset($_POST['items'])){return;}

		$items		= $_POST['items'];
		$item_op	= $_POST['item_op'];
		$where		= $this->auto_id.' IN ('.join(',', $items).')';

		if ($item_op=='del'){
			$this->_del($this->tab, $where);
		}else{
			$row = array();
			if ($item_op){
				 list($field, $value) = explode('-', $item_op);
				 $row[$field] = $value;
			}
			if (!empty($row)){
				$this->_update($this->tab, $row, $where);
			}
		}
		header_go();
	}
	function _get_where(){
		$where = array();
		if (isset($_GET['search_type']) AND isset($_GET['search_key'])){
			$search_type= $_GET['search_type'];
			$search_key	= str_replace(array('%', '"', "'"), '', $_GET['search_key']);
			if ($search_type==$this->auto_id){
				$where[] = $search_type.'='.intval($search_key);
			}else{
				$where[] = $search_type.' LIKE "%'.$search_key.'%"';
			}
		}
		if (isset($_GET['other_where'])){
			foreach((array)$_GET['other_where'] AS $v){
				$where[] = $v;
			}
		}
		if (isset($_GET['where'])){
			$where[] = $_GET['where'];
		}
		return join(' AND ', $where);
	}
	/**
	 * 普通数据形式保存
	 * 根据 唯一$id $_POST['id'] 修改或添加记录，操作成功后，跳转到相应页面
	 * @param array $row 
	 * @param array $row_insert 只有添加时才有的数据
	 * @param array $row_update 只有修改时才有的数据
	 * @author owen 2008-6-10
	 */
	function save_row($row, $row_insert=array(), $row_update=array()){
		$id = intval($_POST['id']);
		if ($id){
			$row = $row_update ? array_merge($row, $row_update) : $row;
			/**
			 * 修改数据库成功， header_go($url)转到被修改记录，并提示
			 *	失败时，header_go()返回
			 */
			if ($this->db->row_update($this->tab, $row, $this->auto_id.'='.$id)) {
				header_go($_SERVER['PHP_SELF'].'?search_type='.$this->auto_id.'&search_key='.$id);
			}else{
				header_go();
			}
		}else{
			$row = $row_insert ? array_merge($row, $row_insert) : $row;
			/**
			 * 添加数据库成功， header_go($url)列表首页，并提示
			 *	失败时，header_go()返回
			 */
			$row = $row_insert ? array_merge($row, $row_insert) : $row;
			$row = $row_insert ? array_merge($row, $row_insert) : $row;
			$row = $row_insert ? array_merge($row, $row_insert) : $row;
			if ($this->db->row_insert($this->tab, $row)) {
				header_go($_SERVER['PHP_SELF']);
			}else{
				header_go();
			}
		}
	}
}
?>