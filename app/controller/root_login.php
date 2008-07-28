<?php
/*	
 *	users表，必须有字段 id, username, password, grade
 *	当 $row['grade']==9 时，$_SESSION['is_root']=1，管理员合法登录。
 */
class root_login extends root{
	function __construct(){
		$this->tab = T.'users';
	}
	function index() {
	
	}
	function login(){
		$username	= $_POST['username'];
		$password	= $_POST['password'];
		$vcode		= $_POST['vcode'];

		$bool = false;
		session_start();
		if (!$this->_validate_vcode($vcode)){
			$msg = '验证码不正确！';
		}elseif ($this->_check_user($username, $password)){
			$bool = true;
		}else{
			$msg = '用户名或密码不正确！';
		}
		$bool ? go_url("index.php", "登录成功！") : go_url("login.php", $msg);
	}
	function logout() {
		session_start();
		session_destroy();
		header_go('./');
	}
	private function _check_user($username, $password) {
		$bool = false;
		$username = $this->_validate_login($username);
		$row = $this->db->row_select_one($this->tab, 'username="'.$username.'" AND password="'.md5($password).'"');
	
		if ($row AND $row['grade']==9) {
			$_SESSION['user']	= $row;
			$_SESSION['is_root']= 1;
			$bool = true;
		}
		return $bool;
	}
	private function _validate_vcode($vcode) {
		if ($_SESSION["vcode"]!="" AND strtoupper($_SESSION["vcode"])==strtoupper($vcode)){
			return true;
		}else{
			return false;
		}
	}
	/*	
	 *	转化用户名小写、过滤字符
	 */
	private function _validate_login($username) {
		$username = strtolower(trim($username));
		$username = str_replace(array('%', '"', "'"), '', $username);
		return $username;
	}
}
?>