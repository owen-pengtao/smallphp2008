<?php
/**
 * head 类
 * @see meta::__construct()
 * @license http://www.d5s.cn/ 无影的博客
 * @author yytcpt(无影) 2008-6-16
 */
class head{
	/**
	 * head标签中的信息
	 */
	public $meta;
	/**
	 * 页面编码
	 */
	public $charset;
	function __construct($meta){
		$this->meta		= $meta;
		$this->_n		= "\r\n";
		$arr = headers_list();
		$this->charset	= substr(strstr($arr[1], '='), 1);
	}
	function head_start() {
		$str = $this->_head_start();
		$str.= $this->_css();
		$str.= $this->_js();
		$str.= $this->meta->meta_str;
		$str.= $this->_title();
		$str.= $this->_head_end();
		return $str;
	}
	function head_end() {
		$str = $this->_n.'</body>'.$this->_n;
		$str.= '</html>';
		return $str;
	}
	private function _head_start() {
		$str = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">'.$this->_n;
		$str.= '<html xmlns="http://www.w3.org/1999/xhtml">'.$this->_n;
		$str.= '<head>'.$this->_n;
		$str.= '<meta http-equiv="content-Type" content="text/html;charset='.$this->charset.'" />'.$this->_n;
		$str.= '<meta http-equiv="pragma" content="no-cache" />'.$this->_n;
		$str.= '<meta http-equiv="cache-control" content="no-cache" />'.$this->_n;
		$str.= '<meta http-equiv="expires" content="0" />'.$this->_n;
		$str.= '<meta http-equiv="content-language" content="zh-cn" />'.$this->_n;
		$str.= '<meta name="robots" content="all" />'.$this->_n;
		$str.= '<meta name="author" content="'.$this->meta->author.'" />'.$this->_n;
		$str.= '<meta name="copyright" content="'.$this->meta->copyright.'" />'.$this->_n;
		$str.= '<meta name="keywords" content="'.$this->meta->keywords.'" />'.$this->_n;
		$str.= '<meta name="description" content="'.$this->meta->description.'" />'.$this->_n;
		$str.= '<link rel="shortcut icon" type="image/ico" href="'.URL_IMAGES.'favicon.ico" />'.$this->_n;
		$str.= '<link rel="apple-touch-icon" href="'.URL_IMAGES.'apple_logo.png" />'.$this->_n;
		return $str;
	}
	private function _head_end() {
		$str = '</head>'.$this->_n;
		$str.= '<body>'.$this->_n;
		return $str;
	}
	private function _title() {
		$str = '<title>'.$this->meta->title.'</title>'.$this->_n;
		return $str;
	}
	private function _css() {
		$str = '';
		foreach ((array)$this->meta->css as $v) {
			$str.= '<link href="'.URL_CSS.$v.'" rel="stylesheet" type="text/css" media="screen" charset="'.$this->charset.'" />'.$this->_n;
		}
		return $str;
	}
	private function _js() {
		$str = '';
		foreach ((array)$this->meta->js as $v) {
			$str.= '<script src="'.URL_JS.$v.'" type="text/javascript" charset="'.$this->charset.'"></script>'.$this->_n;
		}
		return $str;
	}
}
?>

