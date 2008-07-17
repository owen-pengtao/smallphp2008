<?php
/**
 * pagelist 分页类
 * 默认情况下，表结构的自增字段名称是id
 * 分页样式
 * <pre>
 * .page{clear:both;padding:10px 0;width:100%;height:18px;line-height:18px;text-align:center;}
 * .page a.go{padding:2px 6px;font-family: Arial, Helvetica, sans-serif;font-weight:bold;font-size:14px;}
 * .page strong{padding:4px 0;font-weight:normal;}
 * .page span, .page a, .page strong, .page input, .page select{display:inline;}
 * .page span, .page a{padding:4px 8px;border:1px solid #59B0E3;background:#F6FBFD;color:#088CD5;}
 * .page a:hover, .page span.b{background:#59B0E3;color:#FFF;text-decoration:none;}
 * </pre>
 * @see db::connect()
 * @license http://www.d5s.cn/ 无影的博客
 * @author yytcpt(无影) 2008-6-16
 */
class pagelist{
	/**
	 * 设置每页显示条数
	 */
	public $page_size;
	/**
	 * 设置数字分页的显示个数
	 */
	public $num_btn;
	/**
	 * 图标路径
	 */
	public $img_path;
	/**
	 * 图标名称
	 */
	public $img;
	/**
	 * 首页、上一页、下一页、末页，样式
	 */
	public $img_btn;
	/**
	 * 数据表相关数据
	 */
	public $table;
	/**
	 * 分页页码
	 */
	private $page;
	/**
	 * 分页sql
	 */
	private $sql;
	/**
	 * 一共分多少页
	 */
	private $total_pages;
	/**
	 * 一共有多少条记录
	 */
	private $total_records;
	private $url;
	private $db;

	function __construct($db){
		$this->page_size = 14;
		$this->num_btn	= 9;
		$this->img_path	= URL_IMAGES;
		$this->img		= array("ico_first.gif", "ico_front.gif", "ico_next.gif", "ico_last.gif");
		$this->_set_table();
		$tmp_page = abs(intval(trim($_GET["page"])));
		$this->page = $tmp_page>1 ? $tmp_page : 1;
		$this->db = $db;
	}
	/**
	 * 最常用的分页方法，只需要传3个参数
	 * @param string $tab 表名
	 * @param string $where 查询条件
	 * @param string $orderby 排序字段（默认以id倒序排列）
	 * @param DESC,ASC $descasc 排序方式
	 * @param string $fileds 被查询字段
	 * @return array 查询结果（包括总数、分页代码、查询结果）
	 * @see get_rows_by_sql()
	 * @author owen 2008-6-16
	 */
	function get_rows($tab, $where="", $orderby="", $descasc='', $fileds=''){
		$this->table["tablename"]	= $tab;
		$this->table["where"]		= $where ? $where : $this->table["where"];
		$this->table["orderby"] = $orderby ? $orderby : $this->table["orderby"];
		$this->table["descasc"] = ($descasc=='ASC') ? 'ASC' : 'DESC';
		$this->table["fileds"]	= $fileds ? $fileds : $this->table["fileds"];
		$arr = array(
			"page"	=> $this->show_page(),			//分页代码
			"rows"	=> $this->get_rows_by_sql(),	//记录数
			"sum"	=> $this->total_records,		//总记录数
		);
		return $arr;
	}
	/**
	 * 指定sql和总数，特殊查询
	 * @param enclosing_method_type $sql_query 查询sql语句，不必指定limit
	 * @param enclosing_method_type $row_count 统计总数
	 * @return array 查询结果（包括总数、分页代码、查询结果）
	 * @author owen 2008-6-16
	 */
	function get_rows_sql($sql_query, $row_count=0) {
		$this->total_records = $row_count ? $row_count : 0;
		$arr["rows"]	= $this->get_rows_by_sql($sql_query);
		$arr["page"]	= $this->show_page();
		$arr["sum"]		= $this->total_records;
		return $arr;
	}
	function show_page(){
		if ($this->total_records<1){
			$this->_set_total_records();
		}
		$this->_set_show_page();
		$str = '';
		if ($this->total_pages>1){
			$str.= '<div class="page">';
			$str.= $this->_show_first_prv().$this->_show_num_btn().$this->_show_next_last().$this->_show_page_info().$this->_show_num_select().$this->_show_num_text();
			$str.= '</div>';
		}
		return $str;
	}
	private function _set_table(){
		$this->table["tablename"]	= "";
		$this->table["id"]		= "id";
		$this->table["orderby"]	= $this->table["id"];
		$this->table["descasc"]	= "DESC";
		$this->table["fileds"]	= "*";
		$this->table["where"]	= "";
	}
	private function _set_img(){
		$this->img_btn[0]	= "&lt;&lt;";
		$this->img_btn[1]	= "&lt;";
		$this->img_btn[2]	= "&gt;";
		$this->img_btn[3]	= "&gt;&gt;";
	}
	private function _set_show_page(){
		$this->_set_img();		//设置翻页图片路径
		$this->_set_url();
		if ($this->total_records<$this->page_size){
			$this->total_pages = 1;
		}else{
			$this->total_pages = $this->_set_total_pages();
		}
		if ($this->page>$this->total_pages){
			$this->page = $this->total_pages;
		}
	}
	private function _show_first_prv(){
		if ($this->page==1){
			$str = '<span>'.$this->img_btn[0].'</span> <span>'.$this->img_btn[1].'</span>';
		}else{
			$str = "<a href='".$this->url."1"."' onfocus='this.blur()'>".$this->img_btn[0]."</a> ";	//此处1为首页，page值为1
			$str.= "<a href='".$this->url.($this->page-1)."' onfocus='this.blur()'>".$this->img_btn[1]."</a>";
		}
		return $str;
	}
	private function _show_next_last(){
		if ($this->page>=$this->total_pages){
			$str = '<span>'.$this->img_btn[2].'</span> <span>'.$this->img_btn[3]."</span>";
		}else{
			$str = "<a href='".$this->url.($this->page+1)."' onfocus='this.blur()'>".$this->img_btn[2]."</a> ";
			$str.= "<a href='".$this->url.$this->total_pages."' onfocus='this.blur()'>".$this->img_btn[3]."</a>";
		}
		return $str;
	}
	private function _show_num_text(){
		$str = " <strong>转到第</strong> <input id='go_num_text' type='text' value='".$this->page."' style='border:0;border-bottom:1px solid #CCC;text-align:center;width:30px;'/> <strong>页</strong> ";
		$str.= "<a href='#' onClick=\"window.location='".$this->url."'+document.getElementById('go_num_text').value;\" class='go' onfocus='this.blur()'>[Go]</a>";
		return $str;
	}
	private function _show_num_select(){
		if ($this->total_pages<50){
			$str = "<select onchange=\"if(this.options[this.selectedIndex].value!=''){window.location=this.options[this.selectedIndex].value;}\">";
			for ($i=1; $i<=$this->total_pages; $i++){
				$str.= "<option value='".$this->url.$i."' ".($this->page==$i ? " selected='selected'":"").">".$i."</option>";
			}
			$str.= "</select> ";
		}else{
			$str = "";
		}
		return $str;
	}
	private function _show_num_btn(){
		if ($this->page>=1 and $this->page<=$this->total_pages){
			$tmp_p	= ($this->num_btn-1)/2;
			if (($this->page - $tmp_p)<=0){
				$start_p	= 1;
			}else{
				if (($this->page-$tmp_p)>$this->num_btn and ($this->page-$tmp_p)>($this->total_pages - $this->num_btn+1)){
					$start_p	= $this->total_pages - $this->num_btn + 1;
				}else{
					$start_p	= $this->page - $tmp_p;
				}
			}
			if (($this->page+$tmp_p) < $this->total_pages){
				$end_p = ($this->page + $tmp_p)<$this->num_btn?$this->num_btn:($this->page + $tmp_p);
				if ($end_p>$this->total_pages){
					$end_p = $this->total_pages;
				}
			}else{
				$end_p = $this->total_pages;
			}
		}
		$str = "";
		for ($i=$start_p; $i<=$end_p; $i++){
			if ($i==$this->page){
				$str.= " <span class='b'>".$i."</span> ";
			}else{
				$str.= " <a href='".$this->url.$i."' onfocus='this.blur()'>".$i."</a> ";
			}
		}
		return $str;
	}
	private function _show_page_info(){
		$str = " <strong>共".$this->total_records."条/".$this->total_pages."页</strong>";
		return $str;
	}
	/**
	 * 计算总页数
	 */
	private function _set_total_pages(){
		return ceil($this->total_records/$this->page_size);
	}
	/**
	 * 设置总记录数
	 */
	private function _set_total_records(){
		if ($this->total_records==0 or !isset($this->total_records)){
			if (empty($this->count_sql) and !empty($this->table["tablename"])){
				$sql = "SELECT count(".$this->table["id"].") as count_id FROM `".$this->table["tablename"]."` ".($this->table["where"]!=""?" WHERE ".$this->table["where"]:"");
			}else{
				$sql = preg_replace("/SELECT(.*)FROM(.*)ORDER(.*)/i", "SELECT count(".$this->table["id"].") AS count_id FROM\${2}", $this->sql);
			}
			$arr = $this->db->row_query_one($sql);
			$this->total_records = $arr["count_id"];
		}
	}
	/**
	 * 根据sql返回查询数据
	 * @param string $sql  指定$sql时，不必指定limit；$sql为空时，自动拼出sql
	 * @return array 查询结果
	 * @see _get_sql()
	 * @author owen 2008-6-16
	 */
	private function get_rows_by_sql($sql=''){
		if ($sql) {
			$this->sql = $sql;
			$this->sql.= " LIMIT ".$this->page_size*($this->page-1).", ".$this->page_size;	//指定的SQL;
		}else{
			$this->_get_sql();
		}
		return $this->db->row_query($this->sql);
	}
	private function _get_sql(){
		if ($this->total_records>10000) {
			$this->sql = "SELECT ".$this->table["fileds"]." FROM `".$this->table["tablename"]."` ";
			$this->sql.= ($this->table["where"]!=""?" WHERE ".$this->table["where"].' AND ' : ' WHERE ').$this->table["orderby"];
			$this->sql.= (strtoupper($this->table["descasc"])=='ASC' ? '>=' : '<=');
			$this->sql.= '(SELECT '.$this->table["orderby"].' FROM `'.$this->table["tablename"].'` ORDER BY '.$this->table["orderby"].' '.$this->table["descasc"].' LIMIT '.$this->page_size*($this->page-1).', 1)';
			$this->sql.= " ORDER BY ".$this->table["orderby"]." ".$this->table["descasc"]." LIMIT ".$this->page_size;
		}else{
			$this->sql = "SELECT ".$this->table["fileds"]." FROM `".$this->table["tablename"]."` ".($this->table["where"]!=""?" WHERE ".$this->table["where"]:"")." ORDER BY ".$this->table["orderby"]." ".$this->table["descasc"]." LIMIT ".$this->page_size*($this->page-1).", ".$this->page_size;
		}
		//SELECT * FROM articles ORDER BY id DESC LIMIT 0, 20
		//SELECT * FROM articles WHERE category_id = 123 AND id >= (SELECT id FROM articles ORDER BY id LIMIT 10000, 1) LIMIT 10
		return $this->sql;		//SQL语句
	}
	private function _set_url(){
		$arr_url = array();
		parse_str($_SERVER["QUERY_STRING"], $arr_url);
		unset($arr_url["page"]);
		if (empty($arr_url)){
			$str = "page=";
		}else{
			$str = http_build_query($arr_url)."&page=";
		}
		$this->url = "http://".$_SERVER["HTTP_HOST"].$_SERVER["PHP_SELF"]."?".str_replace('&', '&amp;', $str);
	}
}
?>