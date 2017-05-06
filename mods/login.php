<?php
if (!defined("IN_TWIMI_PHP")) die('{"status":"forbidden access"}');
$con = TDBConnect();
$username = GetParam("username");
if ($username == "") die('{"status":"empty username"}');
if ($row = TDBFetchOne("SELECT * FROM tp_user WHERE username='$username'")) {
	if ($row['password'] == md5(GetParam("password"))) {
		$tp_id = $row['id'];
		$tp_token = md5(strtolower($username) . $tp_id);
		$rsp = array('status' => 'ok', 'id' => $tp_id, 'username' => $row['username'], 'email' => $row['email'], 'token' => $tp_token, 'nickname' => $row['nickname']);
		$updates = array('token' => $tp_token);
		TDBUpdate($updates, "tp_user", " id='$tp_id' ");
		echo (json_encode($rsp));
	} else {
		$rsp = array('status' => "password error");
		echo (json_encode($rsp));
	}
} else {
	$rsp = array('status' => "invalid username");
	echo (json_encode($rsp));
}
TDBClose($con);
?>