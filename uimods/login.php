<?php
if (!defined("IN_TWIMI_PHP")) die('{"status":"forbidden access"}');
$con = TDBConnect();
$username = GetParam("username");
if ($username == "")
{
	include 'template/login.html';
}else{
	if ($row = TDBFetchOne("SELECT * FROM tp_user WHERE username='$username'")) {
		if ($row['password'] == md5(GetParam("password"))) {
			$tp_id = $row['id'];
			$tp_token = md5(strtolower($username) . $tp_id);
			$rsp = array('status' => 'ok', 'id' => $tp_id, 'username' => $row['username'], 'email' => $row['email'], 'token' => $tp_token, 'nickname' => $row['nickname']);
			$updates = array('token' => $tp_token);
			TDBUpdate($updates, "tp_user", " id='$tp_id' ");
			session_start();  
			$_SESSION['accesstoken'] = $tp_token;  
			header('Location: index.php?mod=index');
		} else {
			$tp_error_msg = "密码错误";
			include 'template/login.html';
		}
	} else {
			$tp_error_msg = "用户名不存在";
			include 'template/login.html';
	}
}
TDBClose($con);
?>
