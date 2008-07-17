<?php
/**
 * 长文章分页类
 * @author yytcpt(无影) 2008-6-10 <yytcpt@gmail.com>
 * @link http://www.d5s.cn/ 无影的博客
 */
class cutpage{
	var $pagestr;		//被切分的内容
	var $pagearr;		//被切分文字的数组格式
	var $sum_word;		//总字数(UTF-8格式的中文字符也包括)
	var $sum_page;		//总页数
	var $page_word;		//一页多少字
	var $cut_tag;		//自动分页符
	var $cut_custom;	//手动分页符
	var $ipage;			//当前切分的页数，第几页
	var $url;
	function __construct(){
		$this->page_word = 1500*3;
		$this->cut_tag = array("</table>", "</div>", "</p>", "<br/>", "”。", "。", ".", "！", "……", "？", ",");
		$this->cut_custom = "<!-- pagebreak -->";
		$tmp_page = intval(trim($_GET["ipage"]));
		$this->ipage = $tmp_page>1?$tmp_page:1;
	}
	/**
	 * 统计被截取的字符串总字数
	 * @return int 字符串长度
	 */
	function get_page_word(){
		$this->sum_word = $this->strlen_utf8($this->pagestr);
		return $this->sum_word;
	}
	/**
	 * 执行字符串截取，并返回某段字符串
	 * @param int $ipage 第几页
	 * @return string
	 * @see cut_str()
	 * @author owen 2008-6-16
	 */
	function show_cpage($ipage=0){
		$this->cut_str();
		$ipage = $ipage ? $ipage:$this->ipage;
		return $this->pagearr[$ipage];
	}
	function show_page(){
		$str = '';
		if ($this->sum_page>1){
			$str.= '<div class="ipage">';
			$str.= $this->show_prv_next();
			$str.= $this->show_page_select();
			$str.= '</div>';
		}
		return $str;
	}
	/**
	 * 截取字符串
	 */
	private function cut_str(){
		$str_len_word = strlen($this->pagestr);		//获取使用strlen得到的字符总数
		$i = 0;
		if ($str_len_word<=$this->page_word){	//如果总字数小于一页显示字数
			$page_arr[$i] = $this->pagestr;
		}else{
			if (strpos($this->pagestr, $this->cut_custom)){
				$page_arr = explode($this->cut_custom, $this->pagestr);
			}else{
				$str_first = substr($this->pagestr, 0, $this->page_word);	//0-page_word个文字
				foreach ($this->cut_tag as $v){
					$cut_start = strrpos($str_first, $v);		//逆向查找第一个分页符的位置
					if ($cut_start){
						$page_arr[$i++] = substr($this->pagestr, 0, $cut_start).$v;
						$cut_start = $cut_start + strlen($v);
						break;
					}
				}
				if (($cut_start+$this->page_word)>=$str_len_word){	//如果超过总字数
					$page_arr[$i++] = substr($this->pagestr, $cut_start, $this->page_word);
				}else{
					while (($cut_start+$this->page_word)<$str_len_word){
						foreach ($this->cut_tag as $v){
							$str_tmp = substr($this->pagestr, $cut_start, $this->page_word);		//取第cut_start个字后的page_word个字符
							$cut_tmp = strrpos($str_tmp, $v);		//找出从第cut_start个字之后，page_word个字之间，逆向查找第一个分页符的位置
							if ($cut_tmp){
								$page_arr[$i++] = substr($str_tmp, 0, $cut_tmp).$v;
								$cut_start = $cut_start + $cut_tmp + strlen($v);
								break;
							}
						}
					}
					if (($cut_start+$this->page_word)>$str_len_word){
						$page_arr[$i++] = substr($this->pagestr, $cut_start, $this->page_word);
					}
				}
			}
		}
		$this->sum_page = count($page_arr);		//总页数
		$this->pagearr = $page_arr;
	}
	/**
	 * 显示上一条，下一条
	 * @return string
	 */
	private function show_prv_next(){
		$this->_set_url();
		if ($this->sum_page>1 and $this->ipage<$this->sum_page){
			$str = "<a href='".$this->url.($this->ipage+1)."'>下一页</a>　";
		}
		if ($this->sum_page>1 and $this->ipage>1){
			$str.= "<a href='".$this->url.($this->ipage-1)."'>上一页</a>";
		}
		return $str;
	}
	/**
	 * 长文章分页链接
	 * @return string
	 */
	private function show_page_select(){
		if ($this->sum_page>1){
			$str = " &nbsp; <select onchange=\"location.href=this.options[this.selectedIndex].value\">";
			for ($i=1; $i<=$this->sum_page; $i++){
				$str.= "<option value='".$this->url.$i."' ".(($this->ipage)==$i ? " selected='selected'":"").">第".$i."页</option>";
			}
			$str.= "</select>";
		}
		return $str;
	}
	/**
	 * 长文章WAP分页链接
	 * @return string
	 */
	function show_page_select_wap(){
		if ($this->sum_page>1){
			$str = "<select ivalue='".($this->ipage-1)."'>";
			for ($i=1; $i<=$this->sum_page; $i++){
				$str.= "<option onpick='".$this->url.$i."'>第".$i."节</option>";
			}
			$str.= "</select>";
		}
		return $str;
	}
	private function _set_url(){
		$arr_url = array();
		parse_str($_SERVER["QUERY_STRING"], $arr_url);
		unset($arr_url["ipage"]);
		if (empty($arr_url)){
			$str = "ipage=";
		}else{
			$str = http_build_query($arr_url)."&ipage=";
		}
		$this->url = "http://".$_SERVER["HTTP_HOST"].$_SERVER["PHP_SELF"]."?".$str;
	}
	/**
	 * 统计UTF-8编码的字符长度
	 * 一个中文，一个英文都为一个字
	 * @param string $str
	 * @return int 字符串长度
	 * @author owen 2008-6-16
	 */
	private function strlen_utf8($str){
	   $i = 0;
	   $count = 0;
	   $len = strlen ($str);
	   while ($i < $len){
		   $chr = ord ($str[$i]);
		   $count++;
		   $i++;
		   if ($i >= $len)
			   break;

		   if ($chr & 0x80){
			   $chr <<= 1;
			   while ($chr & 0x80) {
				   $i++;
				   $chr <<= 1;
			   }
		   }
	   }
	   return $count;
	}
}
?>