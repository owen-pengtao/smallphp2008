<?php
/**
 * 无限分类
 * 调用方式
 * /root/category.php?channel=分类表名
 * 分类表名的数据库结构（必须含有以下字段）
 * <pre>
 * CREATE TABLE IF NOT EXISTS `article_categories` (
 *   `id` int(11) NOT NULL AUTO_INCREMENT,
 *   `pid` int(11) DEFAULT '0',
 *   `title` varchar(255) NOT NULL,
 *   `ranking` smallint(6) DEFAULT '0',
 *   PRIMARY KEY (`id`)
 * ) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=0 ;
 * </pre>
 */
class root_category extends root{
	var $cat_arr = array();
	var $cat_row = array();
	var $arr_opt = array('' => '选择分类');
	var $channel;
	var $tab;
	var $str_ul;
	function __construct($channel, $tab=''){
		$this->channel	= $channel;
		$this->tab	= $tab ? $tab : $this->channel.'_categories';
		$this->_init();
	}
	function index() {
		$this->get_cat_row();
	}
	/*
	 * 获取category的数据库记录
	 */
	function get_cat_row() {
		if (!file_exists($this->path_row)) {
			$this->_save_cat();
		}
		$array = array();
		include($this->path_row);
		$this->cat_row = $array;
		$this->tpl['cat_row'] = $this->cat_row;
		return $this->cat_row;
	}
	/*
	 * 获取category的数组
	 */
	function get_cat_arr() {
		if (!file_exists($this->path_arr)) {
			$this->_save_cat();
		}
		$array = array();
		include($this->path_arr);
		$this->cat_arr = $array;
		$this->tpl['cat_arr'] = $this->cat_arr;
		return $this->cat_arr;
	}
	/*
	 * 更新 保存排序顺序
	 */
	function save_ranking() {
		$arr = $_POST['ranking'];
		foreach ((array)$arr as $k=>$v) {
			$row = array('ranking' => $v);
			$this->db->row_update($this->tab, $row, 'id='.$k);
		}
		$this->_unlink_cat();
		$this->_save_cat();
		header_go($this->url_cat);
	}
	/*
	 * 添加分类时，显示select表单
	 */
	function add() {
		$this->tpl['arr_opt'] = $this->get_option_arr();
	}
	/*
	 * 修改分类时，显示select表单和数据库记录
	 */
	function edit() {
		$id = intval($_GET['id']);
		$this->tpl['arr_opt'] = $this->get_option_arr();
		$this->tpl['row'] = $this->cat_row[$id];
	}
	/*
	 * 保存添加、修改后的数据
	 */
	function save() {
		$row = array(
					'pid'	=> intval($_POST['pid']),
					'title'	=> html_encode($_POST['title']),
				);
		$id = intval($_POST['id']);
		if ($id){
			$bool = $this->db->row_update($this->tab, $row, 'id='.$id);
		}else{
			$bool = $this->db->row_insert($this->tab, $row);
		}
		$this->_unlink_cat();
		$this->_save_cat();
		$bool ? header_go($this->url_cat) : header_go();
	}
	/*
	 *	删除分类
	 *	没有子分类 并且 没有相关文章，才可以删除
	 */
	function del() {
		$this->_check_cat();

		$id = intval($_GET['id']);
		if (!$this->_is_child($id)) {
			if($this->db->row_delete($this->tab, 'id='.$id)) {
				$this->_unlink_cat();
				$this->_save_cat();
				header_go($this->url_cat);
			}
		}
	}
	/*
	 *	获取一个分类的所有子分类，不包含自己这个分类
	 *	生成select查询的 IN语句
	 */
	function get_sql_in($id=0){
		$this->_check_cat();

		if (empty($this->cat_arr[$id])){return ;}

		foreach ($this->cat_arr[$id] as $k => $v) {
			$this->cid_in[] = $v["id"];
			$this->get_cat_in($k);
		}
		return join(", ", $this->cid_in);
	}
	function get_cat_ul($id=0, $depth=0) {
		$this->_check_cat();

		if (empty($this->cat_arr[$id])){
			return '<a href="'.$this->url_cat.'&a=add&pid='.$id.'">添加子分类</a>';
		}else{
			foreach ($this->cat_arr[$id] as $k => $v) {
				$this->str_ul.= str_repeat("\t", $depth);
				$this->str_ul.= '<ul class="cat">'."\r\n";
				$this->str_ul.= str_repeat("\t", $depth);
				$this->str_ul.= "<li>";
				$this->str_ul.= "<a href='".($this->_is_child($v['id']) ? $this->url_cat : $this->channel.'.php')."?cid=".$v["id"]."'>".$v['title']."</a>";
				$this->str_ul.= $this->_admin_cat($this->cat_row[$v["id"]]);
				$this->str_ul.= "</li>\r\n";
				$this->get_cat_ul($k, $depth+1);
				$str_ul.= str_repeat("\t", $depth);
				$this->str_ul.= "</ul>\r\n";
			}
		}
		return $this->str_ul;
	}
	/*
	 * @param int $id		分类的id号
	 * @param int $depth	相对于根分类的深度
	 * @desc 生成下拉选项
	 */
	function get_option_arr($id=0, $depth=0) {
		$this->_check_cat();

		if (empty($this->cat_arr[$id])){return ;}

		foreach ($this->cat_arr[$id] as $k => $v) {
			$this->arr_opt[$v["id"]] = str_repeat("　", $depth).($depth ? '&#9495;' : "").$v['title'];
			$this->get_option_arr($k, $depth+1);
		}
		return $this->arr_opt;
	}

	private function _init() {
		$this->url_cat	= $_SERVER["PHP_SELF"].'?channel='.$this->channel;
		$this->path_row	= PATH_CACHES.$this->channel.'_cat_row.php';
		$this->path_arr	= PATH_CACHES.$this->channel.'_cat_arr.php';
	}
	private function _get_all_category() {
		$sql = "SELECT id, pid, title, ranking FROM `".$this->tab."` ORDER BY ranking DESC ,id";
		return $this->db->row_query($sql);
	}
	private function _unlink_cat() {
		@unlink($this->path_row);
		@unlink($this->path_arr);
	}
	private function _save_cat() {
		$row = $this->_get_all_category();
		$cat_row = array();
		$cat_arr = array();
		foreach ((array)$row as $v){
			$cat_row[$v["id"]]	= $v;
			$cat_arr[$v['pid']][$v['id']]	= array (
											'id' => $v['id'],
											'pid' => $v['pid'],
											'title' => $v['title'],
										);
		}
		arr_save_to_file($this->path_row, $cat_row);
		arr_save_to_file($this->path_arr, $cat_arr);
	}
	private function _check_cat() {
		empty($this->cat_arr) ? $this->get_cat_arr() : '';
		empty($this->cat_row) ? $this->get_cat_row() : '';
	}
	private function _admin_cat($arr){
		$str = "<span class='admin'>";
		$str.= " - 显示顺序: <input type='text' name='ranking[".$arr["id"]."]' value='".intval($arr["ranking"])."' style='width:30px;' /> - ";
		$str.= " [<a href='".$this->url_cat."&a=add&pid=".$arr["id"]."'>添加</a>]";
		$str.= " [<a href='".$this->url_cat."&a=edit&pid=".$arr["pid"]."&id=".$arr["id"]."'>编辑</a>]";
		$str.= ' [<a href="javascript:if(confirm(\'确认要删除吗？\'))document.location=\''.$this->url_cat.'&a=del&id='.$arr["id"].'\'">删除</a>] ';
		$str.= "</span>";
		return $str;
	}
	private function _is_child($id) {
		return $this->cat_arr[$id] ? true:false;
	}
}
?>