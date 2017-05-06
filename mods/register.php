<?php
if (!defined("IN_TWIMI_PHP")) die('{"status":"forbidden access"}');
$con = TDBConnect();
$username = GetParam("username");
if ($username != "") {
	if (TDBFetchOne("SELECT * FROM tp_user WHERE username='$username'")) {
		echo '{"status":"username exists"}';
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
			$rsp = array('status' => 'ok', 'id' => $row['id'], 'username' => $row['username'], 'email' => $row['email'], 'token' => $tp_token, 'nickname' => $row['nickname']);
			echo (json_encode($rsp));
		} else {
			echo ('{"status":"empty arguments"}');
		}
	}
} else {
	echo ('{"status":"empty username"}');
}
TDBClose($con);
?>