<?php
if (!defined("IN_TWIMI_PHP")) die('{"status":"forbidden access"}');
$con = TDBConnect();

$user = GetUser();

$username = GetParam("username");
$url = GetParam("url");
$ac = GetParam("ac");

if($user!=null){
	if($ac=="oauth" && $url!=""){
		$tp_token = $_SESSION['accesstoken'];
		if(strpos($url,"?"))
			header("Location: $url&token=$tp_token");
		else
			header("Location: $url?token=$tp_token");
	}else{
		if($url=="")
			$tp_error_msg = "URL参数为空";
		include 'template/oauthlogin.html';
	}
}else{
	if ($username == ""){
		include 'template/oauthlogin.html';
	}else{
		if ($row = TDBFetchOne("SELECT * FROM tp_user WHERE username='$username'")) {
			if ($row['password'] == md5(GetParam("password"))) {
				$tp_id = $row['id'];
				$tp_token = md5(strtolower($username) . $tp_id);
				$rsp = array('status' => 'ok', 'id' => $tp_id, 'username' => $row['username'], 'email' => $row['email'], 'token' => $tp_token, 'nickname' => $row['nickname']);
				$updates = array('token' => $tp_token);
				TDBUpdate($updates, "bh_user", " id='$tp_id' ");
				session_start();  
				$_SESSION['accesstoken'] = $tp_token;
				if($url!=""){
					if(strpos($url,"?"))
						header("Location: $url&token=$tp_token");
					else
						header("Location: $url?token=$tp_token");
				}else{
					$tp_error_msg = "URL参数为空";
					include 'template/oauthlogin.html';
				}
			} else {
				$tp_error_msg = "密码错误";
				include 'template/oauthlogin.html';
			}
		} else {
				$tp_error_msg = "用户名不存在";
				include 'template/oauthlogin.html';
		}
	}
}
TDBClose($con);
?>
