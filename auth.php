<?php
if (!defined("IN_TWIMI_PHP")) die('{"status":"forbidden access"}');
function GetUser() {
	$con = TDBConnect();
	$access_token = GetParam("token");
	$app_key = GetParam("appkey");	
	if ($access_token == "") {
		return null;
	} else {
		if ($app_key!=""){
			if ($tokenrow = TDBFetchOne("SELECT * FROM tp_tauthtoken WHERE appkey='$app_key' and token='$access_token' and UNIX_TIMESTAMP()-expire < 0")) {
				$uid = $tokenrow['uid'];
				$row = TDBFetchOne("SELECT * FROM tp_user WHERE id=$uid");
				$result = array('id' => $row['id'], 'username' => $row['username'], 'nickname' => $row['nickname'], 'email' => $row['email']);
				return $result;
			} else {
				return null;
			}
		}else{
			if ($row = TDBFetchOne("SELECT * FROM tp_user WHERE token='$access_token'")) {
				$result = array('id' => $row['id'], 'username' => $row['username'], 'nickname' => $row['nickname'], 'email' => $row['email']);
				return $result;
			} else {
				return null;
			}
		}
	}
}
?>