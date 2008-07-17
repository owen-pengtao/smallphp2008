<?php
class meta{
	public $author;
	public $copyright;
	public $meta_str;

	public $site_title;
	public $title;
	public $keywords;
	public $description;

	public $css;
	public $js;

	function __construct() {
		$this->author		= '无影(yytcpt)';
		$this->author_url	= 'http://www.d5s.cn';
		$this->copyright	= 'www.smallphp.cn';
		$this->site_title	= 'SmallPHP,小型PHP';
		$this->title		= $this->site_title.'';
		$this->keywords		= 'Small PHP Framework,smallphp.';
		$this->description	= '让程序变得简单，让PHP变得更简单，小型PHP框架。Let simple procedure, PHP become more simple, Small PHP Framework.';
		$this->css	= array('sp_global.css');			//默认的 CSS 文件。
		$this->js	= array('jquery.js', 'global.js');	//默认的 JS 文件。
	}
	function set_css($var){
		if (is_array($var)){
			$this->css = array_merge($this->css, $var);
		}else{
			$this->css[] = $var;
		}
	}
	function set_js($var){
		if (is_array($var)){
			$this->js = array_merge($this->js, $var);
		}else{
			$this->js[] = $var;
		}
	}
	function set_title($title){
		$this->title = ($title ? $title.' - ' : '').$this->title;
	}
	function set_keywords($keywords){
		$this->keywords = ($keywords ? $keywords.'. ' : '').$this->keywords;
	}
	function set_description($description){
		$this->description = ($description ? $description.'. ' : '').$this->description;
	}
	function set_str($str){
		$this->meta_str = $str;
	}
}
?>