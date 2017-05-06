<?php
if (!defined("IN_TWIMI_PHP")) die('{"status":"forbidden access"}');
function GetUser() {
	$con = TDBConnect();
	$access_token = GetParam("token");
	if ($access_token == "") {
		return null;
	} else {
		if ($row = TDBFetchOne("SELECT * FROM tp_user WHERE token='$access_token'")) {
			$result = array('id' => $row['id'], 'username' => $row['username'], 'nickname' => $row['nickname'], 'email' => $row['email']);
			return $result;
		} else {
			return null;
		}
	}
}
?>