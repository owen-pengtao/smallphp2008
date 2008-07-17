<?php
/**
 * 文件，目录操作 file类
 * @author yytcpt(无影) 2008-6-11
 * @license http://www.d5s.cn/ 无影的博客
 */
class file{
	/**
	 * 写文件
	 * @param string $f 被写入文件
	 * @param string $c 被写入字符
	 * @param string $m 打开方式
	 * @return void()
	 */
	public function f_write($f, $c, $m='wb'){
		$d=@dirname($f);
		$this->f_make_dir($d);
		file_put_contents($f, $c);
		@chmod($f,0777);
	}
	/**
	 * 读取文件内容
	 * @param string $f 文件路径
	 * @param string $m 打开方式
	 * @return string 文件内容
	 */
	public function f_read($f, $m='rb'){
		return file_get_contents($f);
	}
	/**
	 * 移动文件
	 * @param string $s 源文件路径
	 * @param string $d 目标文件路径
	 * @return boolean 是否移动成功
	 * @author owen 2008-6-16
	 */
	public function f_move($s, $d){
		$bool = false;
		if(file_exists($s)){
			$f=@dirname($d);
			$this->f_make_dir($f);
			@copy($s,$d);
			@unlink($s);
			$bool = true;
		}
		return $bool;
	}
	/**
	 * 重命名一文件
	 * @param string $s 源文件（全路径）
	 * @param string $d 新文件（全路径）
	 * @return boolean 是否重命名成功
	 */
	public function f_rename($s, $d){
		$bool = false;
		if(file_exists($s) and @rename($s,$d)){
			$bool = true;
		}
		return $bool;
	}
	/**
	 * 递归删除目录（此函数慎用，以免误删）
	 * @param string $file_path 被删除目录
	 * @return boolean 是否删除成功
	 */
	public function f_delete($file_path){
		$bool = false;
		if (@is_dir($file_path)){
			$array=@scandir($file_path);
			foreach($array as $v){
				if($v=='.' || $v=='..'){
					continue;
				}
				$newPath=$file_path.'/'.$v;
				if(@is_file($newPath)){
					@unlink($newPath);
				}else{
					$this->f_delete($newPath);
				}
			}
			@rmdir($file_path);
			$bool = true;
		}
		return $bool;
	}
	/**
	 * 递归创建多级目录
	 * @param string $dir 目标路径
	 * @return boolean 是否创建成功
	 */
	public function f_make_dir($dir){
		$bool = false;
		if(!@is_dir($dir) and !@file_exists($dir)){
			$this->f_make_dir(@dirname($dir));
			@mkdir($dir,0777);
			@chmod($dir,0777);
			$bool = true;
		}
		return $bool;
	}
}
?>