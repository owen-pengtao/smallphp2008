<?php
class root_article extends root{
	function __construct(){
		$this->tab			= T.'articles';
		$this->tab_content	= T.'articles_content';
		$this->tab_tag		= T.'tags';
		$this->tab_comment	= T.'comments';
		$this->_init_param();
	}
	function add(){
		$this->tpl['arr_opt'] = $this->_get_option_arr();
	}
	function edit(){
		$this->tpl['arr_opt'] = $this->_get_option_arr();

		$sql = 'SELECT a.*,b.content FROM `'.$this->tab.'` AS a LEFT JOIN `'.$this->tab_content.'` AS b ON a.id=b.article_id WHERE a.id='.intval($_GET['id']);
		$row = $this->db->row_query_one($sql);
		$row['tag'] = str_replace(',', ' ', $row['tag']);
		$this->tpl['row'] = $row;
		return $row;
	}
	function save(){
		$content = addslashes_str($_POST['content']);
		$content = preg_replace("/<p>[ (&nbsp;)　]+/s", '<p>', $content);

		$info = $_POST['info'] ? addslashes_str($_POST['info']) : $content;
		$info = utf8_substr(clear_html(html_decode($info)), 100);

		$cid = intval($_POST['cid']);
		include(PATH_CONTROLLER.'category.php');
		$cat = new category($this->db);
		$arr = $cat->get_category('article');
		$cid_name = $arr[$cid]['title'];
		unset($cat, $arr);
		$tag = preg_replace("/[ ,]+/", ',', $_POST['tag']);

		$row = array(
				'cid'		=> $cid,
				'cid_name'	=> $cid_name,
				'title'		=> html_encode($_POST['title']),
				'author'	=> html_encode($_POST['author']),
				'copy_from'	=> html_encode($_POST['copy_from']),
				'copy_url'	=> html_encode($_POST['copy_url']),
				'tag'		=> trim($tag),
				'info'		=> $info,
				'is_pass'	=> $_POST['is_pass'],
				'timer'		=> time(),
			);
		$row = addslashes_str($row);
		$id = intval($_POST['id']);
		if ($id){	//修改记录
			$row_content = array(
							'content' => $content
						);
			if ($this->db->row_update($this->tab, $row, 'id='.$id) AND $this->db->row_update($this->tab_content, $row_content, 'article_id='.$id)) {
				$this->_save_tags($id, $tag, 1);
				header_go($_SERVER['PHP_SELF'].'?search_type=id&search_key='.$id);
			}else{
				header_go();
			}
		}else{		//添加记录
			if ($insert_id = $this->db->row_insert($this->tab, $row)) {
				$this->_save_tags($insert_id, $tag);
				$row_content = array(
								'article_id'	=> $insert_id,
								'content'		=> $content,
							);
				$this->db->row_insert($this->tab_content, $row_content);
				header_go($_SERVER['PHP_SELF']);
			}else{
				header_go();
			}
		}
	}
	function del(){
		$id = intval($_GET['id']);
		if($this->db->row_delete($this->tab, 'id='.$id)) {
			$this->_del_all_comment(1, $id);
			$this->_del_all_tag($id);
			header_go($_SERVER['PHP_SELF']);
		}
	}
	/*
	 * 根据资源id，删除某资源所有评论
	 * $c_id 资源类型， $r_id 资源id
	 */
	private function _del_all_comment($c_id, $r_id){
		$where = "c_id=".$c_id." AND r_id ".(is_array($r_id) ? 'IN ('.join(',', $r_id).')' : '='.$r_id);
		$this->db->row_delete($this->tab_comment, $where, 2);
	}
	private function _del_all_tag($r_id) {
		$where = 'r_id '.(is_array($r_id) ? 'IN ('.join(',', $r_id).')' : '='.$r_id);
		$this->db->row_delete($this->tab_tag, $where, 2);
	}
	/*	
	 *	保存标签，$tag 是标签字符串
	 */
	private function _save_tags($r_id, $tag, $is_edit=0) {
		$tag = explode(',', $tag);
		foreach ((array)$tag as $v) {
			if ($this->_check_tag($v, $is_edit)) {
				$row = array(
							'title'	=> $v,
							'r_id'	=> $r_id,
							'timer'	=> time(),
						);
				$this->db->row_insert($this->tab_tag, $row, 2);
			}
		}
	}
	private function _check_tag($tag, $is_op=0) {
		$where = 'title="'.$tag.'"';
		if ($this->db->row_count($this->tab_tag, $where)==0) {
			return true;
		}else{
			$is_op ? '':$this->db->update_op($this->tab_tag, 'sum_tags', '+', $where, 2);
			return false;
		}
	}
	private function _get_option_arr() {
		include(PATH_CONTROLLER.'root_category.php');
		$cat = new root_category('article');
		$cat->db = $this->db;
		$arr = $cat->get_option_arr();
		unset($cat);
		return $arr;
	}
	/*
	 * 处理post提交的批量事件
	 */
	function _get_items_op(){
		if (!isset($_POST['items'])){return;}

		$items		= $_POST['items'];
		$item_op	= $_POST['item_op'];
		$cid		= $_POST['cid'];
		$where		= 'id IN ('.join(',', $items).')';

		if ($item_op=='del'){
			$this->db->row_delete($this->tab, $where);
			$this->_del_all_comment(1, $items);
			$this->_del_all_tag($items);
		}else{
			$row = array();
			if ($item_op){
				 list($field, $value) = explode('-', $item_op);
				 $row[$field] = $value;
			}
			if($cid>0){
				$row['cid'] = $cid;
			}
			if (!empty($row)){
				$this->db->row_update($this->tab, $row, $where);
			}
		}
		header_go();
	}
}
?>