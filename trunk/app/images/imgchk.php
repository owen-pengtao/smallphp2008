<?php
	############################
	#  MIRCO(PIEA)  
	#  http://www.ioou.com
	#  e-mail:ata@ioou.com
	############################
	header("Content-type:image/jpeg");
	srand((double)microtime()*1000000);
	$im =imagecreate(52,18);
	$black = ImageColorAllocate($im,0,0,0);
	$white = ImageColorAllocate($im, 255,255,255);
	$gray =ImageColorAllocate($im, 220,220,220);
	imagefill($im,0,0,$gray); //imagefill($im,0,0,$gray);  

	for($i=0;$i<200;$i++)
	{
	  $randcolor =ImageColorallocate($im,rand(10,255),rand(10,255),rand(10,255));
	  imagesetpixel($im, rand()%90 , rand()%30 ,$randcolor);
	}

	#---------------------------
	#  WRITE 10 BLACKLINE
	#---------------------------
	for($i=0;$i<4;$i++)
	{
	imageline($im,rand(0,75),rand(0,75),rand(0,75),rand(0,75),$black);
	}

	#---------------------------
	#  WRITE 7 mumber
	#---------------------------
	//while(($authnum=rand()%100000)<10000);
	//$array="0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz";
	if ($_GET["action"]=="cl"){
		$array="3456789ABCDEFGHJKMNPQRSTUVWXYZabcdefghijkmnpqrstuvwxyz";
		$arr_num = 53;
	}else{
		$array="ABCDEFGHJKMNPQRSTUVWXYZ";
		$arr_num = 22;
	}

	for($i=0;$i<4;$i++)
	{
	//substr 62
	//$authnum .=substr($array,rand(0,61),1);
	$authnum .=substr($array,rand(0,$arr_num),1);
	}
	imagestring($im, 6, 10, 0,$authnum,$black);

	imagegif($im);
	imagedestroy($im);
	session_cache_expire(10);
	session_start();
	$_SESSION["vcode"] = $authnum;
?>