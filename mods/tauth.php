<?php
if (!defined("IN_TWIMI_PHP")) die('{"status":"forbidden access"}');

function ac_gettoken(){
	$con = TDBConnect();
	$app_key = GetParam("appkey");
	$app_secret = GetParam("appsecret");
	$app_code = GetParam("code");
	
	if($app_key!="" && $app_secret!="" && $approw = TDBFetchOne("SELECT * FROM tp_api WHERE appkey='$app_key' and appsecret='$app_secret'")){
		$app_id=$approw['id'];
		if($app_code!="" && $coderow = TDBFetchOne("SELECT * FROM tp_tauthcode WHERE appid='$app_id' and code='$app_code' and UNIX_TIMESTAMP()-expire < 0")){
			$uid = $coderow['uid'];
			$timeline = time();
			if($tokenrow = TDBFetchOne("SELECT * FROM tp_tauthtoken WHERE appkey='$app_key' and uid='$uid'")){
				if($timeline < intval($tokenrow['expire'])){
					$rsp = array('status' => "ok",'token' => $tokenrow['token'],'expire' => $tokenrow['expire']);
					echo (json_encode($rsp));
				}else{
					$tokenid = $tokenrow['id'];
					$updates = array('token' => md5($uid+$app_key+$timeline), 'expire' => ($timeline+604800));
					TDBUpdate($updates, "tp_tauthtoken", " id='$tokenid' ");
					$rsp = array('status' => "ok",'token' => md5($uid+$app_key+$timeline), 'expire' => ($timeline+604800));
					echo (json_encode($rsp));
				}
			}else{
				$datas = array('uid' => $uid, 'appkey' => $app_key, 'token' => md5($uid+$app_key+$timeline), 'expire' => ($timeline+604800));
				TDBInsert($datas, "tp_tauthtoken");
				$rsp = array('status' => "ok",'token' => md5($uid+$app_key+$timeline), 'expire' => ($timeline+604800));
				echo (json_encode($rsp));
			}
			TDBDelete("tp_tauthcode"," appid='$app_id' and code='$app_code' ");
		}else{
			echo ('{"status":"invalid code"}');
		}
	}else{
		echo ('{"status":"invalid appkey or appsecret"}');
	}
	TDBClose($con);
}

?>