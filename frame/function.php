<?php
	function get_microtime() {
		list($usec, $sec) = explode(' ', microtime());
		return ((float)$usec + (float)$sec);
	}
	$TIME_START = get_microtime();
	/*
	 *	自动加载类文件
	 *	$obj：类文件名		php5以上支持
	 */
	function __autoload($obj) {
		$class_file = PATH_CLASS.$obj.'.php';
		if (file_exists($class_file)){
			include_once($class_file);
		}elseif(file_exists(PATH_CONTROLLER.$obj.'.php')){
			include_once(PATH_CONTROLLER.$obj.'.php');
		}
	}
	function pr($arr){
		echo '<pre style="text-align:left">';
		print_r($arr);
		echo '</pre>';
	}
	function vr($arr){
		echo '<pre style="text-align:left">';
		var_dump($arr);
		echo '</pre>';
	}
	function get_ip(){
		if (getenv('HTTP_CLIENT_IP')){
			$ip = getenv('HTTP_CLIENT_IP');
		}elseif (getenv('HTTP_X_FORWARDED_FOR')){
			$ip = getenv('HTTP_X_FORWARDED_FOR');
		}elseif (getenv('HTTP_X_FORWARDED')){
			$ip = getenv('HTTP_X_FORWARDED');
		}elseif (getenv('HTTP_FORWARDED_FOR')){
			$ip = getenv('HTTP_FORWARDED_FOR');
		}elseif (getenv('HTTP_FORWARDED')){
			$ip = getenv('HTTP_FORWARDED');
		}else{
			$ip = $_SERVER['REMOTE_ADDR'];
		}
		return $ip;
	}
	/*	保存远程图片到本地
	 *	$url 图片URL，$filename 本地图片路径
	 *	保存成功，返回true，失败返回false
	 */
	function save_image($url, $filename=""){
		$ext = strtolower(strrchr($url, "."));
		if($ext!=".gif"  &&  $ext!=".jpg"  &&  $ext!=".png"  &&  $ext!=".jpeg"){
			$bool = false;
		}else{
			if($filename=="")  {
				$filename = date("YmdHis").$ext;
			}
			$img  =  file_get_contents($url);
			$size  = strlen($img);
			$fp2=fopen($filename, "a");
			if ($fp2){
				fwrite($fp2,$img);
				fclose($fp2);
				$bool = true;
			}
		}
		return $bool;
	}
	//$num 例子：21 = 2^4 + 2^2 + 2^1, return $arr = array(4, 2, 1)
	function num_to_pow($num){
		$bit_num = base_convert($num, 10, 2);
		settype($bit_num, "string");
		$len_num = strlen($bit_num);
		$arr = array();
		for ($i=0; $i<$len_num; $i++){
			$n = substr($bit_num, $len_num-$i-1, 1);
			($n=="1" ? $arr[] = pow(2, $i):0);
		}
		return $arr;
	}
	/*
	 * 根据id生成文件目录，$id <= $max的3次方
	 * 数量级小于10亿，此算法有效
	 */
	function get_id_path($id, $ds='', $max=1000){
		$dir = array();
		$dir[] = ceil($id/pow($max, 2));
		if ($id<=pow($max, 2)){
			$dir[] = ceil($id/$max);
		}else{
			$num = $id%pow($max, 2);
			if ($num>=$max){
				$dir[] = ceil($num/$max);
			}else{
				$dir[] = $num?$num:$max;
			}
		}
		$ds = $ds ? $ds : DS;
		return join($ds, $dir);
	}
?>