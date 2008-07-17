<?php
	/*-------------			string函数			-------------*/
	/*
	 *	统计UTF-8编码的字符长度
	 *	一个中文，一个英文都为一个字
	 */
	function utf8_strlen($str) {
		return preg_match_all('/[\x00-\x7F\xC0-\xFD]/', $str, $dummy);
	}
	/*
	 *	中文截取函数
	 *	$slh 是否有省略号，$start 从第几个字开始截取
	 *	一个中文，一个英文都为一个字
	 */
	function utf8_substr($string, $len=14, $slh=0, $start=0){
		if ($slh AND utf8_strlen($string)>$len) {
			$str_slh = '…';
		}
		return preg_replace('#^(?:[\x00-\x7F]|[\xC0-\xFF][\x80-\xBF]+){0,'.$start.'}'.
							'((?:[\x00-\x7F]|[\xC0-\xFF][\x80-\xBF]+){0,'.$len.'}).*#s', '$1', $string).$str_slh;
	}
	//编码 用户输入的字符串string ，插入数据库中
	function html_encode($str){
		$arr_sor = array('>', '<', "'", '"', chr(32), "\r\n");
		$arr_rep = array('&gt;', '&lt;', '&#39;', '&#34;', '&nbsp;', '<br/>');
		$str = str_replace($arr_sor, $arr_rep, trim($str));
		return $str;
	}
	//解码数据库中 字符串，显示在textarea中
	function html_decode($str){
		$arr_sor = array('&gt;', '&lt;', '&#39;', '&#34;', '&nbsp;', '<br/>');
		$arr_rep = array('>', '<', "'", '"', chr(32), "\r\n");
		$str = str_replace($arr_sor, $arr_rep, trim($str));
		return $str;
	}
	/*
	 * 过滤，插入数据库，带有html字符串的数据
	 * $str 可以是数组或字符串
	 */
	function addslashes_str($str){
		if (is_array($str)){
			foreach($str AS $id => $value) {
				$str[$id] = addslashes_str($value);
			}
		}else{
			$str = addslashes($str);
		}
		return $str;
	}
	/*
	 * 还原数据，取出数据库，带有html字符串的数据
	 * $str 可以是数组或字符串
	 */
	function stripslashes_str($str){
		if (is_array($str)){
			foreach($str AS $id => $value) {
				$str[$id] = stripslashes_str($value);
			}
		}else{
			$str = stripslashes($str);
		}
		return $str;
	}
	/**
	 * 去掉 QUERY_STRING 中的某个参数的值，或清除一组变量
	 * <code>
	 * 例如：$_SERVER["QUERY_STRING"] a=1&b=1&c=1
	 * $var 为字符串，$var==a, 返回 b=1&c=1&a=
	 * $var 为数组，$var==array(a,b), 返回 c=1
	 * </code>
	 * @param string|array $var 被清除的字符串或数组
	 */
	function clear_url($var){
		$arr_url = array();
		parse_str($_SERVER["QUERY_STRING"], $arr_url);
		if (is_array($var)) {
			foreach ($var as $v) {
				unset($arr_url[$v]);
			}
		}else{
			unset($arr_url[$var]);
		}
		$str = '';
		if (!empty($arr_url)){
			$str = http_build_query($arr_url);
		}
		if (!is_array($var)) {
			$str = ($str ? $str.'&' : '').($var ? $var.'=' : '');
		}
		$str = str_replace('&', '&amp;', $str);
		return $str;
	}
	function clear_html($str){
		$search = array ("'<script[^>]*?>.*?</script>'si",  // 去掉 javascript
						 "'<[\/\!]*?[^<>]*?>'si",           // 去掉 HTML 标记
						 "'([\r\n])[\s]+'"                 // 去掉空白字符
						 );
		$replace = array ("",
						  "",
						  "\\1"
						  );
		$text = preg_replace ($search, $replace, $str);
		return $text;
	}
	/*
	 *	取得最后一个某字符$expstr以前的，$isstr=1 包括此字符
	 *	get_begin_str('smallphp.com.gif', '.', 1) = 'smallphp.com.'
	 */
	function get_begin_str($str, $expstr="/", $isstr=0){
		return substr($str, 0, strrpos($str, $expstr)+$isstr);
	}
	/*
	 *	取得最后一个某字符$expstr以后的，$isstr=1 包括此字符
	 *	get_end_str('smallphp.com.gif', '.', 1) = '.gif'
	 */
	function get_end_str($str, $expstr="/", $isstr=0){
		return substr($str, strrpos($str, $expstr)-strlen($str)+(empty($isstr)?1:0));
	}
	/*
	 * 在每个字符间插入文字(UTF8)
	 * $str:被切分字符，$len:切分字符个数，$what:要插入的文字，$isin:在文字之间插入，还是之后
	 */
	function utf8_wordwrap($str, $len, $what, $isin=1){
		# usage: utf8_wordwrap("text",3,"<br>");
		# by tjomi4`, thanks to SiMM.
		# www.yeap.lv
		$from=0;
		$str_length = preg_match_all('/[\x00-\x7F\xC0-\xFD]/', $str, $var_empty);
		$while_what = $str_length / $len;
		$i = 0;
		while($i < round($while_what)){
			$string = preg_replace('#^(?:[\x00-\x7F]|[\xC0-\xFF][\x80-\xBF]+){0,'.$from.'}'.
								   '((?:[\x00-\x7F]|[\xC0-\xFF][\x80-\xBF]+){0,'.$len.'}).*#s',
								   '$1',$str);
			$arr[$i] = $string;
			$total .= $string.$what;
			$from = $from+$len;
			$i++;
		}
		$str = $isin ? implode($arr , $what) : $total;
		return $str;
	}

	/*-------------			array函数			-------------*/

	/*
	 *	把数组的键、值联合起来，变成字符串
	 *	array('a'=>1, 'b'=>2)	a=1&b=2
	 */
	function arr_join($arr, $join_str="&"){
		$arr_str = array();
		foreach ((array)$arr as $k=>$v){
			$arr_str[] = $k."=".$v;
		}
		return join($join_str, $arr_str);
	}
	/*
	 *	把数组$arr 保存成 $var变量在 $file中
	 *	调用时，include(xxx); print_r($array);
	 */
	function arr_save_to_file($file, $arr){
		file_put_contents($file, '<?php $array = '.preg_replace("/\s/i", "", var_export($arr, TRUE)).';?>');
	}
	/*
	 * 将2维数组变成一维数组
	 */
	function arr_muli_to_one($arr_muli){
		$arr = array();
		foreach ((array)$arr_muli AS $v){
			foreach($v AS $vv){
				$arr[] = $vv;
			}
		}
		return $arr;
	}
	/*
	 * 去掉数组中为空，且重复的值
	 */
	function arr_remove_null($arr){
		$arr = array_unique($arr);
		foreach((array)$arr AS $k=>$v){
			if(empty($v)){
				unset($arr[$k]);
			}
		}
		return $arr;
	}
	/*-------------			文件函数			-------------*/
	/*
	 * 把数据追加到文件
	 */
	function add_str_to_file($file, $str){
		$fb = fopen($file, "a");
		fwrite($fb, $str);
		fclose($fb);
	}

	/*-------------			url函数			-------------*/

	/*
	 * 获取当前地址栏 URL
	 */
	function get_url(){
		return "http://".$_SERVER["HTTP_HOST"].str_replace('&', '&amp;', $_SERVER["REQUEST_URI"]);
	}
	/*	定时跳转
	 *	$url 为空时后退到上一页
	 *	$info 跳转时需要显示的文字
	 *	$time 3秒后跳转
	 */
	function go_url($url="", $info="操作成功！", $time=1){
		$url = $url?$url:$_SERVER["HTTP_REFERER"];
		$str = get_url_html();

		$arr_sor = array('{info}', '{url}', '{time}');
		$arr_rep = array($info, $url, $time);
		echo str_replace($arr_sor, $arr_rep, $str);
		exit;
	}
	function go_url_err($url="", $info="操作失败！", $time=3){
		go_url($url, $info, $time);
	}
	function header_go($url=''){
		$url = $url?$url:$_SERVER["HTTP_REFERER"];
		header("Location: ".$url);
		exit;
	}
	function header_404(){
		header("Status: 404 Not Found");
		header_go("/404.html");
	}
	function get_url_html() {
		$str = '
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta http-equiv="refresh" content="{time};URL={url}">
<title>{time}秒后跳转</title>
<style type="text/css">
	div{
		position: absolute;
		top: 50%;margin-left:-200px;
		left: 50%;margin-top:-60px;
		text-align:center;
		border:1px solid #3399FF;background:#F7FBFF;
		width:400px;height:70px;
		font-size:12px;
	}
	div p{margin:0;float:left;width:100%;line-height:25px;height:25px;}
	div p.info{margin-top:10px;}
	a{color:#000;}
</style>
</head>
<body>
<div>
	<p class="info">{info}</p>
	<p class="link"><a href="{url}">直接跳转</a> &nbsp; &nbsp; <a href="javascript:history.back();">返回</a></p>
</div>
</body>
</html>';
		return $str;
	}
?>