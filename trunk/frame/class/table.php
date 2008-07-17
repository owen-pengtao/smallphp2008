<?php
/**
 * 表格类，和 form 类一起工作
 * @author yytcpt(无影) 2008-6-11
 * @link http://www.d5s.cn/
 */
class table{
	/**
	 * 表格的初始化属性参数
	 * @var public
	 */
	public $tab_type;
	public $is_js;

	private $is_add;
	private $is_edit;
	private $is_del;
	private $is_select;
	private $admin_page;
	
	private $_n;
	private $td_sum;
	private $td_width;
	private $td_class;
	private $tr_th;
	function __construct(){
		$this->tab_type	= array('97%', '#0099FF', 't_table', '', '');	//表格宽度，边框颜色，表格Class，id，style
		
		$this->_n		= "\r\n";
		$this->td_sum	= 0;
		$this->td_width	= array();
		$this->td_class	= array();
		$this->tr_th	= array();

		$this->f = new form();
		$this->f->tab_i = 100;		//设定表单的 tabindex 起始值
		$this->f->is_validate = 0;
	}
	/**
	 * 设置表格中，添加、编辑、删除链接，复选框，添加/编辑页面的文件名
	 * @param boolean $is_add 1显示添加
	 * @param boolean $is_edit 1显示编辑，并且支持js验证
	 * @param boolean $is_del 1显示删除
	 * @param boolean $is_select 1显示复选框
	 * @param string $admin_page 添加/编辑页面的文件名
	 * @return void()
	 * @author owen 2008-6-12
	 */
	function set_op($is_add=0, $is_edit=0, $is_del=0, $is_select=1, $admin_page='') {
		$this->is_add	= $is_add;
		$this->is_edit	= $is_edit;
		$this->is_del	= $is_del;
		$this->is_select	= $is_select;
		$this->admin_page = $admin_page ? $admin_page : basename($_SERVER['PHP_SELF']);
	}
	/**
	 * 设置表格参数，每一列的宽度、样式、标题
	 * 当 $this->is_select=1 时，左边增加一列 宽度为 5%<br/>
	 * 当 $this->is_edit=1 或 $this->is_del=1 时，最右边增加一列 宽度为 10%
	 * @param array $td_width 每列的宽度，总和=85%, 90%, 95%, 100%
	 * @return array $td_class 每列的class样式
	 * @return array $tr_th 每列的标题
	 * @author owen 2008-6-12
	 */
	function set_table($td_width, $td_class, $tr_th) {
		$this->td_width	= $td_width;
		$this->td_class	= $td_class;
		$this->tr_th	= $tr_th;

		if ($this->is_select) {
			array_unshift($this->td_width, '5%');
			array_unshift($this->td_class, '');
			array_unshift($this->tr_th, '选择');
		}
		
		if ($this->is_edit or $this->is_del) {
			$this->td_width[]	= '10%';
			$this->td_class[]	= '';
			$this->tr_th[]	= ($this->is_edit ? '修改':'').(($this->is_edit and $this->is_del)?' / ':'').($this->is_del ? '删除':'');
		}
	}
	/**
	 * 设置表格属性
	 * @param array $arr_type array('width', 'bordercolor', 'class', 'id', 'style')
	 * @param boolean $border 是否显示表格边框
	 * @return string <table>标签
	 * @author owen 2008-6-12
	 */
	function table_start($arr_type=array(), $border=1){
		$width		= $arr_type[0] ? $arr_type[0] : $this->tab_type[0];
		$bordercolor= $arr_type[1] ? $arr_type[1] : $this->tab_type[1];
		$class		= $arr_type[2] ? $arr_type[2].' '.$this->tab_type[2] : $this->tab_type[2];
		$str_tmp = $arr_type[3] ? ' id="'.$arr_type[3].'"' : '';
		$str_tmp.= $arr_type[4] ? ' style="'.$arr_type[3].'"' : '';

		$str = '<table width="'.$width.'" bordercolor="'.$bordercolor.'" class="'.$class.'" border="'.$border.'" align="center" cellpadding="0" cellspacing="0"'.$str_tmp.'>'.$this->_n;
		return $str;
	}
	/**
	 * 输出表格尾部代码
	 * @return string </table>标签
	 * @author owen 2008-6-12
	 */
	function table_end(){
		if ($this->is_edit or $this->is_del or $this->is_select) {
			$str = $this->f->form_end();
		}
		$str.= '</table>'.$this->_n;
		$str.= $this->is_js ? $this->_js().$this->_n:'';
		return $str;
	}
	/**
	 * 设置表格表头标题
	 * @param string $caption 表头标题
	 * @return string <caption>表头标题</caption>
	 * @author owen 2008-6-12
	 */
	function caption($caption='') {
		$str = $caption ? '<caption>'.$caption.'</caption>'.$this->_n : '';
		return $str;
	}
	/**
	 * 设置表格的标题 <th>
	 * 生成标题行的代码
	 * @return string <th>标题</th>
	 * @author owen 2008-6-12
	 */
	function tr_th() {
		$this->_th_init($this->tr_th);
		if ($this->is_edit or $this->is_del or $this->is_select) {
			$str = $this->f->form_start($_SERVER['PHP_SELF'], array("form_class"));
		}
		$str.= '<tr>'.$this->_n;
		foreach ((array)$this->tr_th as $k=>$v) {
			$str.= '<th width="'.$this->td_width[$k].'">'.$v.'</th>';
		}
		$str.= $this->_n;
		$str.= '</tr>'.$this->_n;
		return $str;
	}
	/**
	 * 输出表格的一行
	 * 当 $this->is_select=1 时，$arr_td[0] 为自增/唯一id
	 * @param array $arr_td 数组中的每个值，分别显示在不同的列中 
	 * @return string 表格的一行<tr>代码</tr>
	 * @author owen 2008-6-12
	 */
	function tr_td_row($arr_td) {
		$id = $arr_td[0];
		if ($this->is_select) {
			array_unshift($arr_td, '<input type="checkbox" name="items[]" value="'.$id.'" />');
		}
		if ($this->is_edit or $this->is_del) {
			$arr_op = array();
			$arr_op[] = $this->is_edit ? '<a href="'.$this->_link_edit($id).'">修改</a>':'';
			$arr_op[] = ($this->is_edit and $this->is_del) ? ' &nbsp; ':'';
			$arr_op[] = $this->is_del ? '<a href="javascript:if(confirm(\'确认要删除吗？\'))document.location.href=\''.$this->_link_del($id).'\'">删除</a>':'';
			$arr_td[] = join('', $arr_op);
		}
		$str = $this->tr_td($arr_td, $this->td_class);
		$this->is_js = 1;
		return $str;
	}
	/**
	 * 输出表格的一行
	 * @param array $arr_td 数组中的每个值，分别显示在不同的列中
	 * @param array $arr_class 每列的样式
	 * @param array $arr_width 每列的宽度
	 * @return string 表格的一行<tr>代码</tr>
	 * @author owen 2008-6-12
	 */
	function tr_td($arr_td, $arr_class=array(), $arr_width=array()){
		if (empty($this->td_sum)){
			$this->_td_sum($arr_td);
		}
		$arr_class = $arr_class ? $arr_class : $this->td_class;
		$arr_width = $arr_width ? $arr_width : $this->td_width;
		$str = '<tr class="tr_row">'.$this->_n;
		foreach ((array)$arr_td as $k=>$v) {
			$str.= '<td'.($arr_class[$k] ? ' class="'.$arr_class[$k].'"':'').($arr_width[$k] ? ' width="'.$arr_width[$k].'"':'').'>';
			$str.= $v;
			$str.= '</td>'.$this->_n;
		}
		$str.= '</tr>'.$this->_n;
		return $str;
	}
	/**
	 * 显示一个通行，此行只有一列
	 * @param string $td_str 要显示在此行中的字符串
	 * @param string $class 此行的样式
	 * @return string 表格的一行，只有一列<tr>代码</tr>
	 * @author owen 2008-6-12
	 */
	function tr_one($td_str, $class='') {
		if ($td_str) {
			$str = '<tr'.($class ? ' class="'.$class.'"' : '').'>'.$this->_n;
			$str.= '<td colspan="'.$this->td_sum.'">';
			$str.= $td_str;
			$str.= '</td>'.$this->_n;
			$str.= '</tr>'.$this->_n;
		}
		return $str;
	}
	/**
	 * 返回功能处理行的代码
	 * @param array $arr_op array('is_pass-0'=>"不通过", 'is_pass-1'=>"通过");
	 * @param array $arr_opm array('cat' => array('0' => '选择分类', 	'1' => '励志类小说', '2' => '　&#9495;藏獒'));
	 * @return string
	 * @author owen 2008-6-12
	 */
	function tr_one_op($arr_op=array(), $arr_opm=array()) {
		$this->is_del ? $arr_op['del'] = '删除':'';
		
		if ($arr_op) {
			$str.= $this->f->label($this->f->checkbox(array(), array('全选')));
			$str.= str_repeat(' &nbsp; ', 5);
			$str.= $this->f->label($this->f->radio(array('item_op'), $arr_op), '选中项');
	
			foreach ((array)$arr_opm as $k=>$v) {
				$str.= $this->f->select(array($k), $v);
			}
			$str.= str_repeat(' &nbsp; ', 5);;
			$str.= $this->f->submit();
		}
		$str.= $this->is_add ? '<a href="'.$this->admin_page.'?a=add" class="op_add">添加记录</a>':'';
		return $str ? $this->tr_one($str, 'tr_one_op'):'';
	}
	/**
	 * 输出表格中的提交按钮
	 * @return string 表格的一行，显示提交按钮
	 * @author owen 2008-6-12
	 */
	function tr_td_submit(){
		$str = $this->f->submit();
		$str.= $this->f->back();
		return $this->tr_one($str, 'tr_one');
	}
	/**
	 * 表格间隔分隔符
	 * @return string <hr/>标签
	 * @author owen 2008-6-12
	 */
	function hr(){
		$str = '<hr style="width:95%;margin:10px auto;"/>';
		return $str;
	}
	/**
	 * 输出 “编辑” 链接
	 * @param int 自增id
	 * @return string  “编辑” 链接
	 * @author owen 2008-6-12
	 */
	private function _link_edit($id){
		return $this->admin_page.'?a=edit&id='.$id.($_SERVER['QUERY_STRING'] ? '&'.$_SERVER['QUERY_STRING'] : '');
	}
	/**
	 * 输出 “删除” 链接
	 * @param int 自增id
	 * @return string  “删除” 链接
	 * @author owen 2008-6-12
	 */
	private function _link_del($id){
		return $_SERVER['PHP_SELF'].'?a=del&id='.$id.($_SERVER['QUERY_STRING'] ? '&'.$_SERVER['QUERY_STRING'] : '');
	}
	/**
	 * 初始化表头的 width、class
	 * 当 $this->td_width 为空时 每列平均分配宽度
	 * @param array $arr_td 数组中的值，显示到每一列中
	 * @return void()
	 * @author owen 2008-6-12
	 */
	private function _th_init($arr_td) {
		$this->_td_sum($arr_td);
		if (empty($this->td_width)) {
			$this->td_width = array_fill(0, ($this->td_sum-1), floor(100/$this->td_sum).'%');
		}
		if (!empty($this->td_class)) {
			foreach ((array)$this->td_class as $k=>$v) {
				$this->td_class[$k] = $v ? $v : '';
			}
		}
	}
	/**
	 * 统计总列数
	 * @param array $arr_td 数组中的值，显示到每一列中
	 * @return int 一共多少数据列
	 * @author owen 2008-6-12
	 */
	private function _td_sum($arr_td){
		$this->td_sum = count($arr_td);
	}
	/**
	 * 输出table所用的js
	 * @return js 代码
	 * @author owen 2008-6-12
	 */
	private function _js() {
		$str = '
			<script type="text/javascript">
			<!--
				$(document).ready(function(){
					$(".t_table tr.tr_row").hover(function(){$(this).addClass("t_tr_over")}, function(){$(this).removeClass("t_tr_over")});

					$(".t_table tr.tr_row", this).each(function(i){
						var css = (i+1) % 2 == 0 ? "t_two" : "";
						$(this).addClass(css);
					});
					$(".t_table tr.tr_row").toggle(
						function(){$(this).addClass("t_tr_click");$("td input:first", this).attr("checked", "checked")},
						function(){$(this).removeClass("t_tr_click");$("td input:first", this).attr("checked", "")}
					);
					$(".t_table tr.tr_row td a").click(
						function(e){if (e){	e.stopPropagation();}else{window.event.cancelBubble = true;}}
					);';
		if ($this->is_edit or $this->is_del or $this->is_select) {
		$str.= '
					$(".t_table tr.tr_one_op td input[type=radio]").parent().dblclick(function(){$(this).children().attr("checked", "");});
					$(".t_table tr.tr_one_op td input[value^=del]").click(function(){if(!confirm("确认选择删除操作？")) {$(this).attr("checked", "");}});
					$(".t_table tr.tr_one_op td input[type=checkbox]").click(
						function(){
							if(this.checked){
								$("input[name=\'items[]\']").each(function(){this.checked=true;});
							}else{
								$("input[name^=\'items[]\']").each(function(){this.checked=false;});
							}
						}
					);
					$("input[type=\'submit\']").addClass("btxp");
			';
		}
		$str.= '
				});
			//-->
			</script>
		';
		return $str;
	}
}
?>