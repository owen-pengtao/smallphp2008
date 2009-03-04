<?php
/**
 * 表单和JS检测	form类
 * @author yytcpt(无影) 2008-6-11
 * @license http://www.d5s.cn/ 无影的博客
 */
class form{
	/**
	 * 是否启用js检测 true启用
	 * @var public boolean
	 */
	public $is_validate;
	public $tab_i;
	
	/**
	 * 是否已经载入js文件 true已经载入过了
	 * @var private boolean
	 */
	private $is_include_js;
	/**
	 * JS验证方法
	 * @var private array
	 */
	private $validate;
	private $_n;
	function __construct(){
		$this->is_validate	= 1;
		
		$this->in_type	= array("text", "password", "radio", "checkbox", "file", "hidden", "reset", "submit", "image", "button");
		$this->is_include_js= 0;
		$this->_n	= "\r\n";
		$this->tab_i = 1;
	}
	/**
	 * 载入js检测文件
	 * 载入后，把$this->is_include_js 置 1
	 * @return void()
	 * @author owen 2008-6-12
	 */
	function get_js_validate() {
		$arr = array();
		if ($this->is_validate AND $this->is_include_js==0) {
			$js = array(
						'jquery.validate.cmxforms.js',
						'jquery.validate.metadata.js',
						'jquery.validate.js',
						'jquery.validate.additional.methods.js'
					);
			foreach ((array)$js as $v) {
				$arr[] = '<script type="text/javascript" src="'.URL_JS_JQUERY.$v.'" charset="utf-8"></script>'.$this->_n;
			}
			$arr[] = '
					<script type="text/javascript">
					<!--
						$.metadata.setType("attr", "validate");
					//-->
					</script>
					';
			$this->is_include_js = 1;
		}
		return join('', $arr);
	}
	/**
	 * 获取JS检测的验证字符串
	 * 自定义检测例子： {custom}rangeValue:[4,12], 以 {custom} 开头，后面的字符串是自定义检测标记
	 * @param string|array $key
	 * @return string js验证字符串
	 * @author owen 2008-6-12
	 */
	function get_validate($key) {
		$this->validate = array(
						'required'	=> 'required:true',
						'email'		=> 'required:true,email:true',
						'password'	=> 'password:true',
						'password_re'	=> 'password:true,equalTo:\'#password\'',
						'number'	=> 'number:true',	//请输入数字。
						'digits'	=> 'digits:true',	//只能输入数字[0-9]。
						'url'		=> 'url:true',
						'dateISO'	=> 'dateISO:true',
					);
		if (is_array($key)) {
			$arr = array();
			$str = '{';
			foreach ((array)$key as $v) {
				if (strstr($v, '{custom}')) {
					$str_custom = $this->_get_custom_validate($v);
					$arr[] = $str_custom;
				}else{
					$arr[] = $this->validate[$v];
				}
			}
			$str = '{'.join(',', $arr).'}';
		}elseif (strstr($key, '{custom}')){
			$str_custom = $this->_get_custom_validate($key);
			$str = $str_custom ? '{'.$str_custom.'}' : '';
		}else{
			$str = '{'.$this->validate[$key].'}';
		}
		return $str;
	}
	function _get_custom_validate($str){
		$arr = explode('{custom}', $str);
		return $arr[1];
	}
	/**
	 * 增加一个form 控件，tabindex 序号+1
	 * @return int tabindex序号
	 * @author owen 2008-6-12
	 */
	private function tab_add() {
		return $this->tab_i++;
	}
	/**
	 * 输出input控件
	 * 0 name, 1 validate, 2 title, 3 class, 4 id, 5 style, 6 disabled
	 * @param array $arr_type array('name', 'validate', 'title', 'class', 'id', 'style', 'disabled')
	 * @param string $type 参考$this->in_type的值
	 * @return string
	 * @author owen 2008-6-12
	 */
	private function _input($arr_type, $type='') {
		return '<input '.$this->att_val($arr_type).' type="'.(in_array($type, $this->in_type)?$type:'text').'"';
	}
	private function __input($str_tmp='', $is_close=1) {
		return ' tabindex="'.$this->tab_add().'" '.$str_tmp.($is_close ? ' />':'');
	}
	/**
	 * 输出label标签
	 * @param string $for 表单控件的id
	 * @param string $value 提示信息
	 * @param string $title title提示信息
	 * @return 
	 * @author owen 2008-6-12
	 */
	private function _label($for, $value='', $title='') {
		return '<label for="'.$for.'"'.($title ? ' title="'.$title.'"' : '').'>'.($value ? $value.'：':'');
	}
	private function __label() {
		return '</label>'.$this->_n;
	}
	/**
	 * 输出<label>标签
	 * @param string $text <label>中的 表单控件，如：<input …， <select …
	 * @param string $value <label>中的 文字提示
	 * @param string $title <label>的title参数
	 * @return 输出<lable>标签
	 * @author owen 2008-6-12
	 */
	function label($text, $value='', $title='') {
		preg_match('/id="(\w+)"/', $text, $arr);
		$for = $arr[1];
		if (empty($value)) {
			$str = $text;
		}else{
			$str = $this->_label($for, $value, $title).$text.$this->__label();
		}
		return $str;
	}
	/**
	 * 输出带有<label>标记的 <input type="text" />控件
	 * @param array $arr_type array('name', 'validate', 'title', 'class', 'id', 'style', 'disabled')
	 * @param array $arr_v array('value默认值', '<label>中的提示信息')
	 * @param array $arr_o array(maxlength, size, readonly)
	 * @param string $str_tmp 其他特别的，要写在<input ……>中的html代码
	 * @return string 带有<label>标记的 <input type="text" />控件
	 * @author owen 2008-6-12
	 */
	function text($arr_type, $arr_v=array(), $arr_o=array(), $str_tmp='') {
		$str_o = $arr_o[0] ? ' maxlength="'.$arr_o[0].'"':'';
		$str_o.= $arr_o[1] ? ' size="'.$arr_o[1].'"':'';
		$str_o.= $arr_o[3] ? ' readonly="readonly"':'';

		$str = $this->_label($this->att_id(&$arr_type, 'text'), $arr_v[1]);
		$str.= $this->_input($arr_type, 'text');
		$str.= ' value="'.$arr_v[0].'"'.$str_o.$this->__input($str_tmp);
		$str.= $this->__label();
		return $str;
	}
	/**
	 * 输出带有<label>标记的 <input type="password" />控件
	 * @param array $arr_type array('name', 'validate', 'title', 'class', 'id', 'style', 'disabled')
	 * @param string $value value默认值
	 * @return 密码表单控件
	 * @author owen 2008-6-12
	 */
	function password($arr_type, $value='') {
		$arr_type[1] = 'password';

		$str = $this->_label($this->att_id(&$arr_type, 'password'), '输入密码');
		$str.= $this->_input($arr_type, 'password');
		$str.= ' value="'.$value.'"'.$this->__input();
		$str.= $this->__label();
		return $str;
	}
	/**
	 * 输出带有<label>标记的 <input type="password" />控件
	 * 重复输出密码表单控件，同$this->password()一起使用
	 * @param array $arr_type array('name', 'validate', 'title', 'class', 'id', 'style', 'disabled')
	 * @param string $value value默认值
	 * @return 密码表单控件
	 * @author owen 2008-6-12
	 */
	function password_re($arr_type, $value='') {
		$arr_type[1] = 'password_re';

		$str = $this->_label($this->att_id(&$arr_type, 'password'), '重复密码');
		$str.= $this->_input($arr_type, 'password');
		$str.= ' value="'.$value.'"'.$this->__input();
		$str.= $this->__label();
		return $str;
	}
	/**
	 * 输出带有<label>标记的 <input type="radio" />控件
	 * @param array $arr_type array('name', 'validate', 'title', 'class', 'id', 'style', 'disabled')
	 * @param array $arr_opt array('is_pass-1' => '通过', 'is_pass-0' => '没通过')
	 * @param int|string $checked 默认被选中的值
	 * @return 单选表单控件
	 * @author owen 2008-6-12
	 */
	function radio($arr_type, $arr_opt, $checked='') {
		$i = 0;
		$str = '';
		foreach ((array)$arr_opt as $k=>$v) {
			$str.= $this->_label($this->att_id(&$arr_type, 'radio'));
			$str.= $this->_input($arr_type, 'radio');
			$str.= ' value="'.$k.'" '.($k===$checked?'checked="checked" ':'').$this->__input();
			$str.= $v.$this->__label();
			$arr_type[4] = '';
		}
		return $str;
	}
	/**
	 * 输出带有<label>标记的 <input type="checkbox" />控件
	 * @param array $arr_type array('name', 'validate', 'title', 'class', 'id', 'style', 'disabled')
	 * @param array $arr_opt array('is_pass-1' => '通过', 'is_pass-0' => '没通过')
	 * @param array $arr_checked 默认被选中的值
	 * @return 复选表单控件
	 * @author owen 2008-6-12
	 */
	function checkbox($arr_type, $arr_opt, $arr_checked=array()) {
		$arr_checked = (array)$arr_checked;
		$i = 0;
		$str = '';
		foreach ((array)$arr_opt as $k=>$v) {
			$str.= $this->_label($this->att_id(&$arr_type, 'checkbox'));
			$str.= $this->_input($arr_type, 'checkbox');
			$str.= ' value="'.$k.'" '.(in_array($k, $arr_checked)?'checked="checked" ':'').$this->__input();
			$str.= $v.$this->__label();
			$arr_type[4] = '';
		}
		return $str;
	}
	/**
	 * 文件上传表单控件
	 * @param array $arr_type array('name', 'validate', 'title', 'class', 'id', 'style', 'disabled')
	 * @return string 文件上传表单控件
	 * @author owen 2008-6-12
	 */
	function file($arr_type) {
		$this->att_id(&$arr_type, 'file');
		$str = $this->_input($arr_type, 'file');
		$str.= $this->__input();
		return $str;
	}
	/**
	 * 隐藏域表单控件
	 * @param string $name name名称
	 * @param string $value value值
	 * @return string 隐藏域表单控件
	 * @author owen 2008-6-12
	 */
	function hidden($name, $value) {
		$str = $this->_input(array($name), 'hidden');
		$str.= ' value="'.$value.'" />'.$this->_n;
		return $str;
	}
	/**
	 * 重置按钮表单控件
	 * @param string $value 按钮上的文字
	 * @return string 重置按钮表单控件
	 * @author owen 2008-6-12
	 */
	function reset($value='重置') {
		$arr_type = array();
		$arr_type[3] = $arr_type[3] ? $arr_type[3] : 'btxp';
		$str = $this->_input($arr_type, 'reset');
		$str.= ' value="'.$value.'"'.$this->__input();
		return $str;
	}
	/**
	 * 返回按钮表单控件
	 * @param string $value 按钮上的文字
	 * @return string 返回按钮表单控件
	 * @author owen 2008-6-12
	 */
	function back($value='返回') {
		$arr_type = array();
		$arr_type[3] = $arr_type[3] ? $arr_type[3] : 'btxp';
		$str = $this->_input($arr_type, 'reset');
		$str.= ' value="'.$value.'"'.$this->__input("onclick='window.history.go(-1);'");
		return $str;
	}
	/**
	 * 提交按钮表单控件
	 * @param string $value 按钮上的文字
	 * @return string 提交按钮表单控件
	 * @author owen 2008-6-12
	 */
	function submit($arr_type=array(), $value='提交') {
		$arr_type[3] = $arr_type[3] ? $arr_type[3] : 'btxp';
		$str = $this->_input($arr_type, 'submit');
		$str.= ' value="'.$value.'"'.$this->__input();
		return $str;
	}
	/**
	 * 图像按钮表单控件
	 * @param array $arr_type array('name', 'validate', 'title', 'class', 'id', 'style', 'disabled')
	 * @param string $src 图片地址
	 * @return string 图像按钮表单控件
	 * @author owen 2008-6-12
	 */
	function image($arr_type, $src) {
		$str = $this->_input($arr_type, 'image');
		$str.= ' src="'.$src.'"'.$this->__input();
		return $str;
	}
	/**
	 * 普通按钮表单控件
	 * @param array $arr_type array('name', 'validate', 'title', 'class', 'id', 'style', 'disabled')
	 * @param string 按钮文字
	 * @return string 普通按钮表单控件
	 * @author owen 2008-6-12
	 */
	function button($arr_type, $value='') {
		$str = $this->_input($arr_type, 'button');
		$str.= ' value="'.$value.'"'.$this->__input();
		return $str;
	}
	/**
	 * 文本框表单控件
	 * @param array $arr_type array('name', 'validate', 'title', 'class', 'id', 'style', 'disabled')
	 * @param string $value 文本框默认值
	 * @param array $arr_o array(cols宽, rows高, readonly)
	 * @param string $str_tmp 其他特别的，要写在<textarea ……>中的html代码
	 * @return string 文本框表单控件
	 * @author owen 2008-6-12
	 */
	function textarea($arr_type, $value='', $arr_o=array(50, 8), $str_tmp='') {
		$str_o = $arr_o[3] ? ' readonly="readonly" ' : '';
		$this->att_id(&$arr_type, 'textarea');

		$str = '<textarea '.$this->att_val($arr_type).' cols="'.$arr_o[0].'" rows="'.$arr_o[1].'"'.$str_o.$this->__input($str_tmp, 0).'>';
		$str.= $value;
		$str.= '</textarea>'.$this->_n;
		return $str;
	}
	/**
	 * 下拉框表单控件
	 * @param array $arr_type array('name', 'validate', 'title', 'class', 'id', 'style', 'disabled')
	 * @param array $str_tmp <option value="键">值</option>
	 * @param array $selected 默认被选择的表单
	 * @param boolean $size 1多选下拉框
	 * @param string $str_tmp 其他特别的，要写在<select ……>中的html代码
	 * @return string 下拉框表单控件
	 * @author owen 2008-6-12
	 */
	function select($arr_type, $arr_opt=array(), $selected='', $size=0, $str_tmp='') {
		$str_o = $size ? ' multiple="multiple" size="'.$size.'"' : '';

		$this->att_id(&$arr_type, 'select');
		$str = '<select '.$this->att_val($arr_type).$str_o.$this->__input($str_tmp, 0).'>'.$this->_n;
		foreach ((array)$arr_opt as $k=>$v) {
			$str.= '<option value="'.$k.'"'.($selected==$k ? ' selected="selected"':'').'>'.$v.'</option>'.$this->_n;
		}
		$str.= '</select>'.$this->_n;
		return $str;
	}
	/*
	 *	$arr_type = array(class, id)
	 */
	/**
	 * 初始化form表单
	 * @param string $action from提交地址
	 * @param array $class_id array('class', 'id')
	 * @param post,get $method 提交方式 POST或GET
	 * @param _blank, _parent, _self, _top $target 提交页面打开方式，默认本页打开
	 * @return string <form ……>
	 * @author owen 2008-6-12
	 */
	function form_start($action='', $class_id=array(), $method='post', $target='', $str_tmp='') {
		$arr_target = array('_blank', '_parent', '_self', '_top');
		$f_class = $class_id[0] ? $class_id[0] : 'f_form';
		$f_id = $class_id[1] ? ' id="'.$class_id[1].'"' : '';
		$str = $this->get_js_validate();
		if ($this->is_validate) {
			$str.= '
					<script type="text/javascript">
					<!--
						$(document).ready(function() {
							$(".'.$f_class.'").validate();
						});
					//-->
					</script>
					';
		}
		$str.= '<form class="'.$f_class.'"'.$f_id.' action="'.$action.'" method="'.$method.'" enctype="'.($method=='post' ? 'multipart/form-data':'application/x-www-form-urlencoded').'"'.(in_array($target, $arr_target)?' target="'.$target.'" ':'').$str_tmp.'>'.$this->_n;
		return $str;
	}
	/**
	 * form表单结束
	 * @return </form>
	 * @author owen 2008-6-12
	 */
	function form_end() {
		return '</form>'.$this->_n;
	}
	/**
	 * 生成表单的id
	 * 当$arr_type[4]为空，表单id为 f_(text|select|radio)_tableindex
	 * @param array $arr_type
	 * @param string $id
	 * @return string $arr_type[4]
	 * @author owen 2008-6-12
	 */
	private function att_id($arr_type, $id) {
		$arr_type[4] = $arr_type[4] ? $arr_type[4] : 'f_'.$id.'_'.$this->tab_i;
		return $arr_type[4];
	}
	/**
	 * 把 $att_val 连接成字符串
	 * @param array $att_val array('name', 'validate', 'title', 'class', 'id', 'style', 'disabled')
	 * @return string
	 * @author owen 2008-6-12
	 */
	private function att_val($att_val) {
		$arr = array();
		$type = array('name', 'validate', 'title', 'class', 'id', 'style', 'disabled');
		foreach ($type as $k=>$v) {
			if (!empty($att_val[$k])) {
				$var = $k==1 ? $this->get_validate($att_val[$k]):$att_val[$k];
				$arr[] = $v.'="'.$var.'"';
			}
		}
		return join(" ", $arr);
	}
}
?>