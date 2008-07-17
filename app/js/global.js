	$(document).ready(function(){
		$("form input[@type=radio]").dblclick(function (){$(this).attr("checked", false);});
		$("form input[@type=radio]").attr("title", "双击取消选择");
	});
