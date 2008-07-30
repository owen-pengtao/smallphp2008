<?php
/**
 * 先导入city_town.sql，引入 jquery.js
 * 输出下拉框：中国的省、市、区、乡镇
 * @author yytcpt(无影) 2008-6-10 <yytcpt@gmail.com>
 * @link http://www.d5s.cn/ 无影的博客
 * @see db::connect()
 */
class city{
	var $province;
	var $tab_city;
	var $tab_town;

	function __construct($db){
		$this->db = $db;
		$this->province = array(1=>'北京市',2=>'上海市',3=>'天津市',4=>'重庆市',5=>'河北省',6=>'山西省',7=>'内蒙古',8=>'辽宁省',9=>'吉林省',10=>'黑龙江',11=>'江苏省',12=>'浙江省',13=>'安徽省',14=>'福建省',15=>'江西省',16=>'山东省',17=>'河南省',18=>'湖北省',19=>'湖南省',20=>'广东省',21=>'广西',22=>'海南省',23=>'四川省',24=>'贵州省',25=>'云南省',26=>'西藏',27=>'陕西省',28=>'甘肃省',29=>'青海省',30=>'宁夏',31=>'新疆',32=>'台湾省',33=>'香港',34=>'澳门');
		$this->tab_city = T."citys";
		$this->tab_town = T."city_towns";
	}
	function __destruct(){
		$this->province = array();
	}
	/**
	 * 中国省份列表
	 * @param int $province_id 省份id
	 * @return string select下拉框
	 * @see class_name::get_province_select()
	 * @author owen 2008-6-16
	 */
	function get_province_select($province_id=0){
		$str = '<select name="province" id="c_province" validate="{required:true}">';
		$str.= '<option value="">请选择省份</option>';
		foreach ($this->province AS $k=>$v){
			$str.= '<option value="'.$k.'"'.($k==$province_id?' selected="selected"':'').'>'.$v.'</option>';
		}
		$str.= "</select>";
		return $str;
	}
	/**
	 * 按省份province_id查到对应的province_name
	 * @param int $province_id 省份id
	 * @return string 省份名称
	 * @author owen 2008-6-16
	 */
	function get_province_by_id($province_id){
		return $this->province[$province_id];
	}
	/**
	 * 按city_id查到对应的city_name
	 * @param int $city_id 城市id
	 * @return string 城市名称
	 * @author owen 2008-6-16
	 */
	function get_city_by_id($city_id){
		$row = $this->db->row_select_one($this->tab_city, "id=".$city_id, "city_name");
		return $row['city_name'];
	}
	/**
	 * 按town_id查到对应的town_name
	 * @param int $town_id 城区乡镇id
	 * @return string 城区乡镇名称
	 * @author owen 2008-6-16
	 */
	function get_town_by_id($town_id){
		$row = $this->db->row_select_one($this->tab_town, "id=".$town_id, "town_name");
		return $row['town_name'];
	}
	/**
	 * 按一组town_id查到对应的一组town_name
	 * @param array $arr_town_id 一组城区乡镇id
	 * @return array 一组城区乡镇名称
	 * @author owen 2008-6-16
	 */
	function get_town_by_arr_id($arr_town_id){
		$row = $this->db->row_select($this->tab_town, 'id IN ('.join(',', $arr_town_id).')', 0, "id, town_name");
		$arr_town = array();
		foreach ($row AS $v){
			$arr_town[$v["id"]] = $v["town_name"];
		}
		return $arr_town;
	}
	/**
	 * 根据省id，得到所有城市名称
	 * @param int $province_id 省id
	 * @param int $city_id 当前城市id
	 * @return string 城市select
	 * @author owen 2008-6-16
	 */
	function get_city_select($province_id, $city_id=0){
		$row = $this->get_city_by_province($province_id);
		$str = '<select name="city" id="c_city">';
		$str.= '<option value="" validate="{required:true}">请选择城市</option>';
		foreach ($row AS $v){
			$str.= '<option value="'.$v["id"].'"'.($v["id"]==$city_id?' selected="selected"':'').'>'.$v["city_name"].'</option>';
		}
		$str.= "</select>";
		return $str;
	}
	/**
	 * 根据省份ID查询所有的城市
	 * @param int $province_id 省份ID
	 * @return array 城市列表
	 * @author owen 2008-6-16
	 */
	function get_city_by_province($province_id){
		return $this->db->row_select($this->tab_city, "province_id=".$province_id);
	}
	/**
	 * 根据城市id，得到所有 城区乡镇
	 * @param int $city_id 城市id
	 * @param int $town_id 乡镇id
	 * @return string 区乡镇select
	 * @author owen 2008-6-16
	 */
	function get_town_select($city_id, $town_id=0){
		$row = $this->get_towns_by_city($city_id);
		$str = '<select name="town" id="c_town" validate="{required:true}">';
		$str.= '<option value="">请选择城区乡镇</option>';
		foreach ($row AS $v){
			$str.= '<option value="'.$v["id"].'"'.($v["id"]==$town_id?' selected="selected"':'').'>'.$v["town_name"].'</option>';
		}
		$str.= "</select>";
		return $str;
	}
	function get_towns_by_city($city_id){
		return $this->db->row_select($this->tab_town, "city_id=".$city_id);
	}
	/**
	 * 初始化省、市、区县
	 * @param int $province_id 省id
	 * @param int $city_id 市id
	 * @param int $town_id 区县id
	 * @return string
	 * @author owen 2008-6-16
	 */
	function city_init($province_id=0, $city_id=0, $town_id=0){
		$str = $this->get_province_select($province_id);
		$str.= $this->get_city_select($province_id, $city_id);
		$str.= $this->get_town_select($city_id, $town_id);
		$str.= $this->_city_js_change();
		return $str;
	}
	/**
	 * 初始化某城市的区县列表
	 * 必须传入 城市city_id
	 * @param int $city_id 城市id
	 * @param int $town_id 区县id
	 * @return string
	 * @author owen 2008-6-16
	 */
	function town_init($city_id, $town_id=0){
		$str = $this->get_town_select($city_id, $town_id);
		return $str;
	}
	private function _city_js_change(){
		$str = '<script type="text/javascript">
				<!--
					$(function(){
						var url = "/ajax.php";
						$("#c_province").change(function (){
							var province_id = $("#c_province option[@selected]").val();
							jQuery.post( url, "a=get_city&province_id="+province_id, function (msg){
								//	msg返回数据是完整的c_city节点，更新c_city节点，清空c_town节点
								$("#c_city").replaceWith(msg);
								$("#c_city").css("background", "#FFCCFF");
								bind_town_change();
								$("#c_town").html("<option value=>请选择城区乡镇</option>");
								$("#c_town").css("background", "#FFCCFF");
							});
						});
						bind_town_change();
						function bind_town_change(){
							$("#c_city").change(function (){
								var city_id = $("#c_city option[@selected]").val();
								jQuery.post( url, "a=get_town&city_id="+city_id, function (msg){
									//	msg返回数据是完整的c_town节点，更新c_town节点
									$("#c_town").replaceWith(msg);
									$("#c_city").css("background", "");
									$("#c_town").css("background", "#FFCCFF");
								});
							});
						};
					});
				//-->
				</script>';
		return $str;
	}
	/*
	 * 服务器端建立 /ajax.php文件，包含如下代码
		if ($a=="get_city"){
			$province_id = intval($_POST["province_id"]);
			$city = new city();
			echo $city->get_city_select($province_id);
			unset($city);
		}
		if ($a=="get_town"){
			$city_id = intval($_POST["city_id"]);
			$city = new city();
			echo $city->get_town_select($city_id);
			unset($city);
		}

		客户端：
		echo $city->city_init(18, 8);	省市区
		echo $city->town_init(8);	区乡镇
	 */
}
?>