<?php
/**
 * 缓存类
 * 调用示例：
 * <code>
 * $cache = new cache();
 * $cache->cache_file	= 文件完整路径;
 * $cache->cache_time	= 缓存时间XX秒;
 * $cache->cache_is_str= 1;
 * 
 * if (!$cache->cache_is_valid()){
 * 	$cache->start();	//被缓存的文件 start
 * 	echo 'some html string';
 * 	$cache->end();	//被缓存的文件 end
 * 	
 * 	$arr = array()	//some array
 * 	$cache->save_array($arr);
 * }
 * echo '<pre style="text-align:left">';
 * print_r($cache->cache_content);
 * echo '</pre>';
 * </code>
 * @author yytcpt(无影) 2008-6-10 <yytcpt@gmail.com>
 * @link http://www.d5s.cn/ 无影的博客
 */
class cache{
	/**
	 * 缓存文件全地址路径
	 */
	public $cache_file;
	/**
	 * 缓存文件有效时间
	 */
	public $cache_time;
	/**
	 * 缓存文件是否是字符串，1是字符串，0数组
	 */
	public $cache_is_str;
	/**
	 * 缓存数据
	 */
	private $cache_content;
	function __construct(){
		$this->cache_file	= '';
		$this->cache_time	= 3600*24;
		$this->cache_is_str	= 1;
		$this->cache_content= '';
	}
	function start() {
		ob_start();
	}
	function end() {
		$this->cache_content    = ob_get_contents();
        ob_end_clean();
		$this->_save_cache_string();
	}
	/**
	 * 把数组保存到缓存文件中
	 * @param array $arr 被缓存数组
	 * @return boolean 是否保存成功
	 * @author owen 2008-6-16
	 */
	function save_array($arr) {
		$this->cache_content = $arr;
		return $this->_save_file('<?php $array = '.preg_replace("/\s/i", "", var_export($arr, TRUE)).';?>');
	}
	/**
	 * 缓存是否有效
	 * @return boolean true有效	 false无效
	 */
	function cache_is_valid(){
		if (file_exists($this->cache_file) AND ((time()-filemtime($this->cache_file)) < $this->cache_time)) {	//缓存不过期 且 缓存存在
			$this->_get_cache();
			$bool = true;
		}else{
			$bool = false;
		}
		return $bool;
	}
	/**
	 * 获取缓存文件
	 * @return array 被缓存数据
	 */
	private function _get_cache() {
		if ($this->cache_is_str) {
			$this->cache_content = file_get_contents($this->cache_file);
		}else{
			$array = array();
			include($this->cache_file);
			$this->cache_content = $array;
		}
		return $this->cache_content;
	}
	/**
	 * 保存数据到缓存文件
	 * @return void()
	 */
	private function _save_cache_string() {
		if ($this->cache_file) {
			if($this->_save_file($this->cache_content)==0) {
				exit("缓存错误！");
			}
		}else{
			exit('需指定缓存文件全路径。');
		}
	}
	/**
	 * 保存数据到缓存文件$this->cache_file中
	 * @param string $string 被保存数据
	 * @return boolean 是否被保存成功
	 * @author owen 2008-6-16
	 */
	private function _save_file($string) {
		$this->_mk_dir(dirname($this->cache_file));
		return file_put_contents($this->cache_file, $string);
	}
	/**
	 * 递归创建目录
	 * @param string $path 目录路径
	 * @return void()
	 * @author owen 2008-6-16
	 */
	function _mk_dir($path){
		if (!file_exists($path)){
			$this->_mk_dir(dirname($path));
			mkdir($path, 0777);
			chmod($path, 0777);
		}
	}
}
?>