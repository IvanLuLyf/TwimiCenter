<?php
if (!defined("IN_TWIMI_PHP")) die('{"status":"forbidden access"}');
$con = TDBConnect();
$username = GetParam("username");
if ($username != "") {
	if (TDBFetchOne("SELECT * FROM tp_user WHERE username='$username'")) {
		$tp_error_msg = "用户名已存在";
		include 'template/register.html';
	} else {
		$password = GetParam('password');
		$email = GetParam('email');
		$nickname = GetParam('nickname');
		if ($nickname == "") $nickname = $username;
		if ($password != "" && $email != "") {
			$datas = array('username' => $username, 'email' => $email, 'password' => md5($password), 'nickname' => $nickname);
			TDBInsert($datas, "tp_user");
			$row = TDBFetchOne("SELECT * FROM tp_user WHERE username='$username'");
			$tp_id = $row['id'];
			$tp_token = md5(strtolower($username) . $tp_id);
			$updates = array('token' => $tp_token);
			TDBUpdate($updates, "tp_user", " id='$tp_id' ");
			session_start();  
			$_SESSION['accesstoken'] = $tp_token;  
			header('Location: index.php?mod=index');
		} else {
			$tp_error_msg = "参数不能为空";
			include 'template/register.html';
		}
	}
} else {
	include 'template/register.html';
}
TDBClose($con);
?>