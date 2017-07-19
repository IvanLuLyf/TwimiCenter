<?php
if (!defined("IN_TWIMI_PHP")) die('{"status":"forbidden access"}');
$con = TDBConnect();

$TauthCode = GetParam("code");
$TauthSite = GetParam("site");
$username = GetParam("username");

if($TauthSite!="" && $TauthCode!="" && $clientrow = TDBFetchOne("SELECT * FROM tp_tauthclient WHERE siteurl='$TauthSite'")){
	$app_key=$clientrow['appkey'];
	$app_secret=$clientrow['appsecret'];
	$app_id=$clientrow['id'];
	
	$strTokeninfo = do_post_request("http://$TauthSite/api.php?mod=tauth&action=gettoken","appkey=$app_key&appsecret=$app_secret&code=$TauthCode");
	$Tokeninfo=json_decode($strTokeninfo);
	$TauthToken=$Tokeninfo->token;
	$TauthExpire=$Tokeninfo->expire;
	
	$strUserinfo = do_post_request("http://$TauthSite/api.php?mod=user&action=getinfo","appkey=$app_key&token=$TauthToken");
	$TuserInfo=json_decode($strUserinfo);
	
	$buid=$TuserInfo->id;
	
	if($TuserInfo->status=="ok"){
		if($bindrow = TDBFetchOne("SELECT * FROM tp_tauthbind WHERE appid='$app_id' and buid=$buid")){
			$uid=$bindrow['uid'];
			$userrow = TDBFetchOne("SELECT * FROM tp_user WHERE id=$uid");
			$updates = array('token' => $TauthToken, 'expire' => $TauthExpire);
			TDBUpdate($updates, "tp_tauthbind", " appid='$app_id' and buid=$buid ");
			session_start();
			$_SESSION['accesstoken'] = $userrow['token'];
			header('Location: index.php?mod=index');
		}else{
			session_start();
			$_SESSION['tauthappid'] = $app_id;
			$_SESSION['tauthbuid'] = $buid;
			$_SESSION['tauthtoken'] = $TauthToken;
			$_SESSION['tauthexpire'] = $TauthExpire;
			include 'template/tconnect.html';
		}
	}else{
		
	}
}else if($username!=""){
	if ($row = TDBFetchOne("SELECT * FROM tp_user WHERE username='$username'")) {
		if ($row['password'] == md5(GetParam("password"))) {
			$tp_id = $row['id'];
			$tp_token = md5(strtolower($username) . $tp_id);
			$rsp = array('status' => 'ok', 'id' => $tp_id, 'username' => $row['username'], 'email' => $row['email'], 'token' => $tp_token, 'nickname' => $row['nickname']);
			$updates = array('token' => $tp_token);
			TDBUpdate($updates, "tp_user", " id='$tp_id' ");
			session_start();  
			$_SESSION['accesstoken'] = $tp_token;
			$datas = array('appid' => $_SESSION['tauthappid'],'uid' => $tp_id,'buid' => $_SESSION['tauthbuid'], 'token' => $_SESSION['tauthtoken'], 'expire' => $_SESSION['tauthexpire']);
			TDBInsert($datas, "tp_tauthbind");
			
			header('Location: index.php?mod=index');
		} else {
			$tp_error_msg = "密码错误";
			include 'template/tconnect.html';
		}
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
			$datas = array('appid' => $_SESSION['tauthappid'],'uid' => $tp_id,'buid' => $_SESSION['tauthbuid'], 'token' => $_SESSION['tauthtoken'], 'expire' => $_SESSION['tauthexpire']);
			TDBInsert($datas, "tp_tauthbind");
			
			header('Location: index.php?mod=index');
		} else {
			$tp_error_msg = "参数不能为空";
			include 'template/tconnect.html';
		}
	}
}

function do_post_request($url, $data, $optional_headers = null){
	$params = array('http' => array(
	'method' => 'POST',
	'content' => $data
	));
	if ($optional_headers !== null) {
		$params['http']['header'] = $optional_headers;
	}
	$ctx = stream_context_create($params);
	$fp = @fopen($url, 'rb', false, $ctx);
	if (!$fp) {
		throw new Exception("Problem with $url, $php_errormsg");
	}
	$response = @stream_get_contents($fp);
	if ($response === false) {
		throw new Exception("Problem reading data from $url, $php_errormsg");
	}
	return $response;
}


TDBClose($con);
?>
