<?php
if (!defined("IN_TWIMI_PHP")) die('{"status":"forbidden access"}');
$con = TDBConnect();
$user = GetUser();
$access_token = GetParam("token");
$action = GetParam("action");
if ($action == "update") {
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
}else{
	echo ('{"status":"invalid action"}');
}
?>