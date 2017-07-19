<?php
if (!defined("IN_TWIMI_PHP")) die('{"status":"forbidden access"}');

function ac_update(){
	$con = TDBConnect();
	$user = GetUser();
	$access_token = GetParam("token");
	if ($user != null) {
		$nickname = GetParam("nickname");
		$row = TDBFetchOne("SELECT * FROM tp_user WHERE token='$access_token'");
		$tp_id = $row['id'];
		if ($nickname != "") {
			$updates = array('nickname' => $nickname);
			TDBUpdate($updates, "tp_user", " id='$tp_id' ");
			$rsp = array('status' => 'ok' , 'nickname' => $nickname);
			echo (json_encode($rsp));
		} else {
			echo ('{"status":"empty arguments"}');
		}
	} else {
		echo ('{"status":"invalid token"}');
	}
	TDBClose($con);
}

function ac_getinfo(){
	$user = GetUser();
	if ($user != null) {
		$rsp = array('status' => 'ok' , 'id' => $user['id'], 'username' => $user['username'], 'nickname' => $user['nickname'], 'email' => $user['email']);
		echo (json_encode($rsp));
	} else {
		echo ('{"status":"invalid token"}');
	}
}

function ac_login(){
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
}

function ac_register(){
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
}

?>