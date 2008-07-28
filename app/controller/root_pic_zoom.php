<?php
class root_pic_zoom extends root{
	function save(){
		$width	= $_POST['width'];
		$height	= $_POST['height'];
		$path_file	= PATH_UP_IMAGES_SMALL;
		$small_file = $path_file.($_POST['filename'] ? $_POST['filename'] : get_begin_str(basename($_FILES['file']['name']), '.')).'_'.$width.'_'.$height;
		$up_file = $small_file.get_end_str($_FILES['file']['name'], '.', 1);
		if (!file_exists($up_file)) {
			if (move_uploaded_file($_FILES['file']['tmp_name'], $up_file)) {
				$pic = new pic();
				$pic->auto_clip_zoom($up_file, $small_file, $width, $height);
				unset($pic);
				$file_name = $path_file.get_end_str($up_file, DS);
				$this->tpl['small_pic'] = URL_UP_IMAGES_SMLLL.basename($file_name);
			}
		}else{
			$this->tpl['small_pic'] = '目标图片已经存在！';
		}
	}
}
?>