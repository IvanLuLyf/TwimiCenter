<?php
if (!defined("IN_TWIMI_PHP")) die('{"status":"forbidden access"}');
$con = TDBConnect();

$user = GetUser();
$siteurl = constant("TP_SITEURL");
$username = GetParam("username");
$url = GetParam("url");
$ac = GetParam("ac");
$app_key = GetParam("appkey");

if($app_key!="" && $approw = TDBFetchOne("SELECT * FROM tp_api WHERE appkey='$app_key'")){
	if(strstr($url,$approw['appurl'])!=FALSE){
		if($user!=null && $ac =="tauth"){
			$timeline = time();
			$code = md5($user['id']+$app_key+$timeline);
			$datas = array('uid' => $user['id'], 'appid' => $approw['id'], 'code' => $code, 'expire' => ($timeline+604800));
			TDBInsert($datas, "tp_tauthcode");
			if(strpos($url,"?"))
				header("Location: $url&code=$code&site=$siteurl");
			else
				header("Location: $url?code=$code&site=$siteurl");
		}else{
			if ($username == ""){
				include 'template/tauthlogin.html';
			}else{
				if ($row = TDBFetchOne("SELECT * FROM tp_user WHERE username='$username'")) {
					if ($row['password'] == md5(GetParam("password"))) {
						$tp_id = $row['id'];
						$tp_token = md5(strtolower($username) . $tp_id);
						$updates = array('token' => $tp_token);
						TDBUpdate($updates, "tp_user", " id='$tp_id' ");
						session_start();  
						$_SESSION['accesstoken'] = $tp_token;
						
						$timeline = time();
						$code = md5($tp_id+$app_key+$timeline);
						$datas = array('uid' => $tp_id, 'appid' => $approw['id'], 'code' => $code, 'expire' => ($timeline+604800));
						TDBInsert($datas, "tp_tauthcode");
			
						if(strpos($url,"?"))
							header("Location: $url&code=$code&site=$siteurl");
						else
							header("Location: $url?code=$code&site=$siteurl");
					} else {
						$tp_error_msg = "密码错误";
						include 'template/tauthlogin.html';
					}
				} else {
						$tp_error_msg = "用户名不存在";
						include 'template/tauthlogin.html';
				}
			}
		}
		
	}else{
		$tp_show = "s";
		$tp_error_msg = "非法的URL";
		include 'template/tauthlogin.html';
	}
}else{
	$tp_show = "s";
	$tp_error_msg = "非法的AppKey";
	include 'template/tauthlogin.html';
}

TDBClose($con);
?>
