<?php
/**
 * 图片缩放、文字水印类
 * 外部调用函数
 * 自动裁缩auto_clip_zoom(), 指定裁缩clip_zoom(), 等比缩放zoom_to_small()<br/>
 * 文字水印create_text_mark(), 图片水印create_img_mark()<br/>
 * 一般情况下目标文件格式和水印位置不需要变化<br/>
 * <code>
 * $pic	= new pic();
 * 
 * $file = 'http://www.google.cn/intl/zh-CN/images/logo_cn.gif';
 * echo '<img src="'.$file.'" />';
 * echo '<hr/>';
 * $new_filename = dirname(__FILE__).'\test_pic';
 * $new_file =  $pic->auto_clip_zoom($file, $new_filename, 143, 55);
 * echo '<img src="'.$new_file.'" />';
 * </code>
 * @license http://www.d5s.cn/ 无影的博客
 * @author yytcpt(无影) 2008-6-16
 */
class pic{
	/**
	 * 图片后缀
	 */
	public $file_extension;
	/**
	 * 原始文件地址（可以为远程地址） http://www.google.cn/intl/zh-CN/images/logo_cn.gif
	 */
	public $filename;
	/**
	 * 水印位置 (默认: 4右下点)
	 * 1为左上点，2为左下点，3为右上点，4为右下点，5为中心点
	 */
	public $mark_position;
	/**
	 * 水印图片路径
	 */
	public $pic_mark;
	/**
	 * 水印字体路径
	 */
	public $pic_font;
	private $error_str;
	private $path;
	/**
	 * 输出文件名称（无后缀）
	 */
	private $output_name;

	function __construct(){
		$this->file_extension	= "jpg";
		$this->mark_position	= 4;	//4	水印右下点
		$this->error_str		= "";
		$this->path			= str_replace('\\', '/', dirname(__FILE__));
		$this->pic_mark		= $this->path."/pic_mark.png";
		$this->pic_font		= $this->path."/simsun.ttc";
	}
	/**
	 * 自动裁切，缩放图片
	 * @param string $file 源文件
	 * @param string $new_filename 目标文件(不带后缀名)
	 * @param int $width 最终图片宽度
	 * @param int $height 最终图片高度
	 * @param 0,1,2,3,4 $crop_position 1左上, 2右下, 3距左上1/3, 4距左上2/3, 0中心
	 * @return string 缩略图图片路径
	 * @author owen 2008-6-16
	 */
	function auto_clip_zoom($file, $new_filename, $width, $height, $crop_position=0){
		$this->get_file_info($file);
		$this->thumb_width	= $width;
		$this->thumb_height	= $height;
		$this->set_output_name($new_filename);
		$arr_clip = $this->scale_image();
		$this->crop_image($arr_clip["img_width"], $arr_clip["img_height"], $crop_position);
		$this->create_pic();

		$this->zoom_to_small($this->output_name, $new_filename, $width, $height);
		return $this->output_name;
	}
	/**
	 * 指定坐标，裁_缩图片，先选择图片区域剪裁，再缩小图片
	 * $width/$height = $src_width/$src_height
	 * @param strng $file 源文件
	 * @param strng $new_filename 目标文件(不带后缀名)
	 * @param int $width 最终图片宽度
	 * @param int $height 最终图片高度
	 * @param int $src_width 剪裁宽度
	 * @param int $src_height 剪裁高度
	 * @param int $src_x 剪裁横坐标
	 * @param int $src_y 剪裁纵坐标
	 * @return @return string 缩略图图片路径
	 * @author owen 2008-6-16
	 */
	function clip_zoom($file, $new_filename, $width, $height, $src_width, $src_height, $src_x, $src_y){
		$this->get_file_info($file);
		$this->thumb_width	= $width;
		$this->thumb_height	= $height;
		$this->set_output_name($new_filename);
		$this->crop_image_by_xy($src_width, $src_height, $src_x, $src_y);
		$this->create_pic();

		$this->zoom_to_small($this->output_name, $new_filename, $width, $height);
		return $this->output_name;
	}
	/**
	 * 等比缩放图片
	 * @param string $file 源文件
	 * @param string $new_filename 目标文件(不带后缀名)
	 * @param int $width 最终图片宽度
	 * @param int $height 最终图片高度
	 * @return string|boolean 缩略图图片路径，出错时返回false
	 * @author owen 2008-6-16
	 */
	function zoom_to_small($file, $new_filename, $width, $height){
		$this->get_file_info($file);
		$this->thumb_width	= $width;
		$this->thumb_height	= $height;
		if ($this->is_equal()){
			$this->set_output_name($new_filename);
			$this->equal_zoom($width, $height);
			return $this->output_name;
		}else{
			$this->error_str = "源图宽高比与目标图宽高比不一致。";
			return false;
		}
	}
	/**
	 * 转化图片格式为jpg
	 * @param string $file 源图片路径
	 * @return string 目标图片路径
	 * @author owen 2008-6-16
	 */
	function change_format($file){
		$this->get_file_info($file);
		$arr = pathinfo($file);
		$this->set_output_name($arr['dirname'].DS.$arr['filename']);
		$this->crop_image_by_xy($this->file_size[0], $this->file_size[1], 0, 0);
		$this->file_extension = 'jpg';
		$this->create_pic();
		return $this->output_name;
	}
	/**
	 * 创建文字水印
	 * @param string $file 源文件
	 * @param string $new_filename 目标文件
	 * @param string $text 水印文字
	 * @param int $size 文字大小， 9相当于12px
	 * @return string 目标图片路径
	 * @author owen 2008-6-16
	 */
	function create_text_mark($file, $new_filename, $text, $size=9){
		$this->get_file_info($file);
		$this->set_output_name($new_filename);
		$this->create_text_watermark($text, $size);
		$this->create_pic();
		return $this->output_name;
	}
	/**
	 * 创建图片水印
	 * @param string $file 源文件
	 * @param string $new_filename 目标文件
	 * @param string $mark_img 水印图片，默认位置和类文件pic.php同目录
	 * @return string 目标图片路径
	 * @author owen 2008-6-16
	 */
	function create_img_mark($file, $new_filename, $mark_img=""){
		$this->get_file_info($file);
		$this->set_output_name($new_filename);
		$this->create_img_watermark($mark_img);
		$this->create_pic();
		return $this->output_name;
	}
	/**
	 * 等比缩放图片，此函数不能单独使用
	 * $this->file_size[0]/$this->file_size[1] / $width/$height 比例必须相等
	 * @param int $width 目标宽度
	 * @param int $height 目标高度
	 * @return void()
	 * @author owen 2008-6-16
	 */
	private function equal_zoom($width, $height){
		$thumb = imagecreatetruecolor($width, $height);
		imagecopyresampled($thumb, $this->tmp_pic, 0, 0, 0, 0, $width, $height, $this->file_size[0], $this->file_size[1]);
		$this->create_pic($thumb);
	}
	/**
	 * 新图片比例 和 源图片比例 是否相等
	 * @return boolean 比例是否相等
	 * @author owen 2008-6-16
	 */
	private function is_equal(){
		if (floor($this->file_size[0]/$this->file_size[1])==floor($this->thumb_width/$this->thumb_height)){
			return true;
		}else{
			return false;
		}
	}
	/**
	 * 获取原始图片信息
	 * @param string $filename 源图片地址，可以是url
	 * @return array|boolean 原始图片信息 或 false
	 * @author owen 2008-6-16
	 */
	private function get_file_info($filename){
		$this->filename = $filename;
		$this->file_size = GetImageSize($this->filename);		// 获取原始文件大小
		$this->file_extension = strtolower(str_replace('.', '', substr($this->filename, strrpos($this->filename, '.'))));	//获取原始文件后缀
		if(!$this->file_size[0] OR !$this->file_size[1]){
			$this->error_str = "获取源图尺寸出错。";
			$bool = false;
		}elseif(!in_array($this->file_extension, array("jpeg", "jpg", "png", "gif", "bmp") )){
			$this->error_str = "源图片文件格式不合法。";
			$bool = false;
		}elseif($this->file_extension=='jpeg' OR $this->file_extension=='jpg') {
			if (function_exists('imagecreatefromjpeg')) {
				$this->tmp_pic = imagecreatefromjpeg($this->filename);
			} else {
				$this->error_str = "请把源 jpg 图片，使用图像处理工具另存为。";
				$bool = false;
			}
		}elseif($this->file_extension=='png') {
			if (function_exists('imagecreatefrompng')) {
				$this->tmp_pic = imagecreatefrompng($this->filename);
			} else {
				$this->error_str = "请把源 png 图片，使用图像处理工具另存为。";
				$bool = false;
			}
		}elseif($this->file_extension=='gif') {
			if (function_exists('imagecreatefromgif')) {
				$this->tmp_pic = imagecreatefromgif($this->filename);
			} else {
				$this->error_str = "请把源 gif 图片，使用图像处理工具另存为。";
				$bool = false;
			}
		}elseif($this->file_extension=='bmp') {
			include("bmp.php");
			$this->tmp_pic = imagecreatefrombmp($this->filename);
			$this->error_str = "请把源 bmp 图片，使用图像处理工具另存为。";
			$bool = false;
		}else{
			$bool = $this->file_size;
		}
		return $bool;
	}
	/**
	 * 设置目标文件名(不带文件后缀)
	 * @param string $output_name 不带文件后缀
	 * @return boolean 是否设置成功
	 * @author owen 2008-6-16
	 */
	private function set_output_name($output_name){
		$this->file_extension = "jpg";
		if ($this->file_size[0]>=$this->thumb_width and $this->file_size[1]>=$this->thumb_height){
			$path_arr = pathinfo($this->filename);
			$this->output_name = $output_name.".".$this->file_extension;
			if (file_exists($this->output_name)){
				@unlink($this->output_name);
			}
			$bool = true;
		}else{
			$this->error_str = "目标图片的宽、高不能大于源图片宽、高。";
			$bool = false;
		}
		return $bool;
	}
	/**
	 * 创建文字水印
	 * @param string $text 文字水印，即把文字作为为水印，支持ASCII码，不支持中文
	 * @param int $size 文字大小，XX像素
	 * @param string $color 文字颜色，值为十六进制颜色值，默认为#FF0000(红色)
	 * @param int $angle 角度制表示的角度，0 度为从左向右读的文本。更高数值表示逆时针旋转。例如 90 度表示从下向上读的文本。
	 * @return void()
	 * @author owen 2008-6-16
	 */
	private function create_text_watermark($text, $size=12, $color='#000000', $angle=0){
		if($text) {
			if (strstr(PHP_OS, "WIN")){
				$font = "c:/windows/fonts/simsun.ttc";
			}else{
				$font = $this->pic_font;
			}

			$arr_ttf	= imagettfbbox($size, $angle, $font, $text);
			$marksize	= array(abs($arr_ttf[2]-$arr_ttf[0]), abs($arr_ttf[5]-$arr_ttf[3]));
			if ($this->file_size[0] < ($marksize[0]*2) OR $this->file_size[1] < ($marksize[1]*2)) {
				return false;
			}
			$text = $this->foxy_utf8_to_nce($text);

			imagealphablending($this->tmp_pic, true);
			$R = hexdec(substr($color, 1, 2));
            $G = hexdec(substr($color, 3, 2));
            $B = hexdec(substr($color, 5));

            $pos = $this->get_mark_coordinate($marksize, 1);
			imagettftext($this->tmp_pic, $size, $angle, $pos["x"], $pos["y"], imagecolorallocate($this->tmp_pic, $R, $G, $B), $font, $text);
		}
	}
	/**
	 * 创建图片水印
	 * @param obj $wate_img 水印图片，即作为水印的图片	全路径/文件名
	 * @return void()
	 * @author owen 2008-6-16
	 */
	private function create_img_watermark($wate_img=""){
		$wate_img = $wate_img ? $wate_img : $this->pic_mark;
		if($imgmark = imagecreatefrompng($wate_img)) {
			$marksize = GetImageSize($wate_img);
			if ($this->file_size[0] < ($marksize[0]*2) OR $this->file_size[1] < ($marksize[1]*2)) {
				return false;
			}
			imagealphablending($this->tmp_pic, true);
			$pos = $this->get_mark_coordinate($marksize, 0);
			imagecopy($this->tmp_pic, $imgmark, $pos["x"], $pos["y"], 0, 0, $marksize[0], $marksize[1]);
		}
	}
	/**
	 * 得到水印在底片中的坐标位置
	 * $mark_position	水印位置
	 *					1为左上点，2为左下点，3为右上点，4为右下点，5为中心点
	 *					默认: 4右下点
	 * @param array $marksize 水印图片的 GetImageSize()值, $marksize[0] 水印宽度， $marksize[1] 水印高度
	 * @param boolean $is_text 1文字水印
	 * @return array 水印坐标位置
	 * @author owen 2008-6-16
	 */
	private function get_mark_coordinate($marksize, $is_text=1){
		switch ($this->mark_position) {
			case '1': // 1: 左上点
				$pos_x = 0;
				$pos_y = $is_text ? $marksize[1]:0;
				break;
			case '2': // 2: 左下点
				$pos_x = 0;
				$pos_y = $this->file_size[1] -  $marksize[1];
				break;
			case '3': // 3: 右上点
				$pos_x = $this->file_size[0] -  $marksize[0];
				$pos_y = $is_text ? $marksize[1]:0;
				break;
			case '4': // 4: 右下点
				$pos_x = $this->file_size[0] -  $marksize[0];
				$pos_y = $this->file_size[1] -  $marksize[1];
				break;
			case '5': // 5: 中心点
				$pos_x = ($this->file_size[0] / 2) -  ($marksize[0] / 2);
				$pos_y = ($this->file_size[1] / 2) -  ($marksize[1] / 2);
				break;
			default: // 默认: 右下点
				$pos_x = $this->file_size[0] -  $marksize[0];
				$pos_y = $this->file_size[1] -  $marksize[1];
				break;
		}
		$pos = array("x" => $pos_x, "y" => $pos_y);
		return $pos;
	}
	/**
	 * 自动从原图中截取一片区域
	 * @param int $src_width 目标图片宽
	 * @param int $src_height 目标图片高
	 * @param 0,1,2,3,4 $crop_position 大致范围,  1左上, 2右下, 3距左上1/3, 4距左上2/3, 0中心
	 * @return obj 返回截图后的数据
	 * @author owen 2008-6-16
	 */
	private function crop_image($src_width, $src_height, $crop_position){
		//源图片 宽 > 高 cut_y=0 y坐标为0
		if ($this->file_size[0]>$this->file_size[1]){
			switch ($crop_position){
				case '1':	//沿左边对齐
					$cut_x = 0;
					break;
				case '2':	//沿右边对齐
					$cut_x = floor($this->file_size[0]-$src_width);
					break;
				case '3':	//多余宽度，距离左边1/3
					$cut_x = floor(($this->file_size[0]-$src_width)/3);
					break;
				case '4':	//多余宽度，距离左边2/3
					$cut_x = floor(($this->file_size[0]-$src_width)*2/3);
					break;
				case '5':	//中心点裁图
					$cut_x = floor(($this->file_size[0]-$src_width)/2);
					break;
				default:	//默认同5
					$cut_x = floor(($this->file_size[0]-$src_width)/2);
					break;
			}
			$cut_y = 0;
		}
		//源图片 宽 < 高 cut_x=0 x坐标为0
		if ($this->file_size[0]<$this->file_size[1]){
			$cut_x = 0;
			switch ($crop_position){
				case '1':	//沿上边对齐
					$cut_y = 0;
					break;
				case '2':	//沿下边对齐
					$cut_y = floor($this->file_size[1]-$src_height);
					break;
				case '3':	//多余高度，距离上边1/3
					$cut_y = floor(($this->file_size[1]-$src_height)/3);
					break;
				case '4':	//多余高度，距离下边2/3
					$cut_y = floor(($this->file_size[1]-$src_height)*2/3);
					break;
				case '5':	//中心点裁图
					$cut_y = floor(($this->file_size[1]-$src_height)/2);
					break;
				default:	//默认同3
					$cut_y = floor(($this->file_size[1]-$src_height)/3);
					break;
			}
		}
		$im = @imagecreatetruecolor($src_width, $src_height);
		imagecopy($im, $this->tmp_pic, 0, 0, $cut_x, $cut_y, $src_width, $src_height);
		$this->tmp_pic = $im;
	}
	/**
	 * 根据xy坐标，从原图中截取一片区域
	 * @param int $src_width 目标图片宽度
	 * @param int $src_height 目标图片高度
	 * @param int $cut_x 起始X坐标
	 * @param int $cut_y 起始Y坐标
	 * @return obj 返回截图后的数据
	 * @see crop_image_by_xy()
	 * @author owen 2008-6-16
	 */
	private function crop_image_by_xy($src_width, $src_height, $cut_x, $cut_y){
		$im = @imagecreatetruecolor($src_width, $src_height);
		imagecopy($im, $this->tmp_pic, 0, 0, $cut_x, $cut_y, $src_width, $src_height);
		$this->tmp_pic = $im;
	}
	/**
	 * 最终创建新的图片
	 * 根据图片类型，选择合适的函数进行处理
	 * @param obj $pic 图片内存数据
	 * @return void()
	 * @see unsharp_mask()
	 * @author owen 2008-6-16
	 */
	private function create_pic($pic=""){
		$this->tmp_pic = $pic ? $pic : $this->tmp_pic;
		//$this->unsharp_mask($this->tmp_pic);
		if (function_exists('imagejpeg') AND ($this->file_extension=='jpeg' OR $this->file_extension=='jpg')){
			@imagejpeg($this->tmp_pic, $this->output_name, 100);
		}elseif (function_exists('imagepng') AND $this->file_extension=='png'){
			@imagepng($this->tmp_pic, $this->output_name);
		}elseif ($this->file_extension=='bmp') {
			imagebmp($this->tmp_pic, $this->output_name);
		}elseif (function_exists('imagegif') AND $this->file_extension=='gif'){
			@imagegif($this->tmp_pic, $this->output_name);
		}else{
			$this->error_str = "创建目标图片出错。";
		}
		@imagedestroy($this->tmp_pic);
	}
	/**
	 * 根据目标图片的宽、高比例，计算被挖图片的宽、高
	 * @param enclosing_method_type enclosing_method_arguments description
	 * @return array 返回 被挖图片的宽、高
	 * @author owen 2008-6-16
	 */
	private function scale_image(){
		$w = $this->file_size[0];
		$h = $this->file_size[1];

		$max = $w>$h ? $h : $w;
		$arr_x = array();
		$arr_y = array();
		for ($i=$max; $i>0; $i--){
			if ($w>$h){
				$y = $i;
				$x = floor($y*$this->thumb_width/$this->thumb_height);
			}else{
				$x = $i;
				$y = floor($this->thumb_height*$x/$this->thumb_width);
			}
			if ($x<=$w and $y<=$h){
				$arr_x[] = $x;
				$arr_y[] = $y;
			}
		}
		$ret = array(
					'img_width' => $arr_x[0],
					'img_height' => $arr_y[0],
				);
		return $ret;
	}
	/**
	 * 提高图片的清晰度，锐化图片
	 * @param string $img 图片内存数据
	 * @param int $amount 
	 * @param int $radius 
	 * @param int $threshold 
	 * @return void()
	 * @author owen 2008-6-16
	 */
	private function unsharp_mask($img, $amount = 100, $radius = .5, $threshold = 3){
		$amount = min($amount, 500);
		$amount = $amount * 0.016;
		if ($amount == 0) return true;
		$radius = min($radius, 50);
		$radius = $radius * 2;
		$threshold = min($threshold, 255);
		$radius = abs(round($radius));
		if ($radius == 0) return true;
		$w = ImageSX($img);
		$h = ImageSY($img);
		$imgCanvas  = ImageCreateTrueColor($w, $h);
		$imgCanvas2 = ImageCreateTrueColor($w, $h);
		$imgBlur    = ImageCreateTrueColor($w, $h);
		$imgBlur2   = ImageCreateTrueColor($w, $h);
		ImageCopy($imgCanvas,  $img, 0, 0, 0, 0, $w, $h);
		ImageCopy($imgCanvas2, $img, 0, 0, 0, 0, $w, $h);
		for ($i = 0; $i < $radius; $i++)	{
			ImageCopy($imgBlur, $imgCanvas, 0, 0, 1, 1, $w - 1, $h - 1);
			ImageCopyMerge($imgBlur, $imgCanvas, 1, 1, 0, 0, $w, $h, 50);
			ImageCopyMerge($imgBlur, $imgCanvas, 0, 1, 1, 0, $w - 1, $h, 33.33333);
			ImageCopyMerge($imgBlur, $imgCanvas, 1, 0, 0, 1, $w, $h - 1, 25);
			ImageCopyMerge($imgBlur, $imgCanvas, 0, 0, 1, 0, $w - 1, $h, 33.33333);
			ImageCopyMerge($imgBlur, $imgCanvas, 1, 0, 0, 0, $w, $h, 25);
			ImageCopyMerge($imgBlur, $imgCanvas, 0, 0, 0, 1, $w, $h - 1, 20 );
			ImageCopyMerge($imgBlur, $imgCanvas, 0, 1, 0, 0, $w, $h, 16.666667); // dow
			ImageCopyMerge($imgBlur, $imgCanvas, 0, 0, 0, 0, $w, $h, 50);
			ImageCopy($imgCanvas, $imgBlur, 0, 0, 0, 0, $w, $h);
			ImageCopy($imgBlur2, $imgCanvas2, 0, 0, 0, 0, $w, $h);
			ImageCopyMerge($imgBlur2, $imgCanvas2, 0, 0, 0, 0, $w, $h, 50);
			ImageCopyMerge($imgBlur2, $imgCanvas2, 0, 0, 0, 0, $w, $h, 33.33333);
			ImageCopyMerge($imgBlur2, $imgCanvas2, 0, 0, 0, 0, $w, $h, 25);
			ImageCopyMerge($imgBlur2, $imgCanvas2, 0, 0, 0, 0, $w, $h, 33.33333);
			ImageCopyMerge($imgBlur2, $imgCanvas2, 0, 0, 0, 0, $w, $h, 25);
			ImageCopyMerge($imgBlur2, $imgCanvas2, 0, 0, 0, 0, $w, $h, 20 );
			ImageCopyMerge($imgBlur2, $imgCanvas2, 0, 0, 0, 0, $w, $h, 16.666667);
			ImageCopyMerge($imgBlur2, $imgCanvas2, 0, 0, 0, 0, $w, $h, 50);
			ImageCopy($imgCanvas2, $imgBlur2, 0, 0, 0, 0, $w, $h);
		}
		for ($x = 0; $x < $w; $x++)	{
			for ($y = 0; $y < $h; $y++)	{
				$rgbOrig = ImageColorAt($imgCanvas2, $x, $y);
				$rOrig = (($rgbOrig >> 16) & 0xFF);
				$gOrig = (($rgbOrig >>  8) & 0xFF);
				$bOrig =  ($rgbOrig        & 0xFF);
				$rgbBlur = ImageColorAt($imgCanvas, $x, $y);
				$rBlur = (($rgbBlur >> 16) & 0xFF);
				$gBlur = (($rgbBlur >>  8) & 0xFF);
				$bBlur =  ($rgbBlur        & 0xFF);
				$rNew = (abs($rOrig - $rBlur) >= $threshold) ? max(0, min(255, ($amount * ($rOrig - $rBlur)) + $rOrig)) : $rOrig;
				$gNew = (abs($gOrig - $gBlur) >= $threshold) ? max(0, min(255, ($amount * ($gOrig - $gBlur)) + $gOrig)) : $gOrig;
				$bNew = (abs($bOrig - $bBlur) >= $threshold) ? max(0, min(255, ($amount * ($bOrig - $bBlur)) + $bOrig)) : $bOrig;
				if (($rOrig != $rNew) || ($gOrig != $gNew) || ($bOrig != $bNew)) {
					$pixCol = ImageColorAllocate($img, $rNew, $gNew, $bNew);
					ImageSetPixel($img, $x, $y, $pixCol);
				}
			}
		}
		ImageDestroy($imgCanvas);
		ImageDestroy($imgCanvas2);
		ImageDestroy($imgBlur);
		ImageDestroy($imgBlur2);
	}
	/**
	 * 转换字符为unicode， 支持中文文字水印 (php手册中 回帖中的函数)
	 * @param string $utf 被转换字符串
	 * @return string 转换后的字符串
	 * @author owen 2008-6-16
	 */
	private function foxy_utf8_to_nce($utf) {
		$max_count = 5; // flag-bits in $max_mark ( 1111 1000 == 5 times 1)
		$max_mark = 248; // marker for a (theoretical ;-)) 5-byte-char and mask for a 4-byte-char;

		$html = "";
		for($str_pos = 0; $str_pos < strlen($utf); $str_pos++) {
			$old_chr = $utf{$str_pos};
			$old_val = ord( $utf{$str_pos} );
			$new_val = 0;

			$utf8_marker = 0;

			// skip non-utf-8-chars
			if( $old_val > 127 ) {
				$mark = $max_mark;
				for($byte_ctr = $max_count; $byte_ctr > 2; $byte_ctr--) {
					// actual byte is utf-8-marker?
					if( ( $old_val & $mark  ) == ( ($mark << 1) & 255 ) ) {
						$utf8_marker = $byte_ctr - 1;
						break;
					}
					$mark = ($mark << 1) & 255;
				}
			}

			// marker found: collect following bytes
			if($utf8_marker > 1 and isset( $utf{$str_pos + 1} ) ) {
				$str_off = 0;
				$new_val = $old_val & (127 >> $utf8_marker);
				for($byte_ctr = $utf8_marker; $byte_ctr > 1; $byte_ctr--) {
					// check if following chars are UTF8 additional data blocks
					// UTF8 and ord() > 127
					if( (ord($utf{$str_pos + 1}) & 192) == 128 ) {
						$new_val = $new_val << 6;
						$str_off++;
						// no need for Addition, bitwise OR is sufficient
						// 63: more UTF8-bytes; 0011 1111
						$new_val = $new_val | ( ord( $utf{$str_pos + $str_off} ) & 63 );
					}
					// no UTF8, but ord() > 127
					// nevertheless convert first char to NCE
					else {
						$new_val = $old_val;
					}
				}
				// build NCE-Code
				$html .= '&#'.$new_val.';';
				// Skip additional UTF-8-Bytes
				$str_pos = $str_pos + $str_off;
			}else {
				$html .= chr($old_val);
				$new_val = $old_val;
			}
		}
		return $html;
	}
}
?>