<?php
class category{
	function __construct($db){
		$this->db = $db;
	}
	function get_category($channel){
		$tab = T.$channel.'_categories';
		$cache = new cache();
		$cache->cache_file	= PATH_CACHES.$channel.'_category.php';
		$cache->cache_is_str= 0;
		$cache->cache_time	= 3600*24;

		if (!$cache->cache_is_valid()){
			$row = $this->db->row_select($tab, '', 0, '*', 'ranking DESC, id');
			$arr = array();
			foreach ((array)$row as $v) {
				$arr[$v['id']] = $v;
			}
			$cache->save_array($arr);
		}
		$data = $cache->cache_content;
		unset($cache);
		return $data;
	}
}
?>