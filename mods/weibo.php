<?php
if (!defined("IN_TWIMI_PHP")) die('{"status":"forbidden access"}');
function ac_send(){
	$con = TDBConnect();
	$user = GetUser();
	if ($user != null) {
		$message = GetParam('message');
		$source = GetParam('source');
		$username = $user["username"];
		$nickname = $user["nickname"];
		$timeline = time();
		if (message != "") {
			$datas = array('username' => $username, 'nickname' => $nickname, 'source' => $source, 'message' => $message, 'timeline' => $timeline);
			$tid = TDBInsert($datas, "tp_weibos");
			$rsp = array('status' => 'ok' , 'tid' => $tid);
			echo (json_encode($rsp));
		} else {
			echo ('{"status":"empty arguments"}');
		}
	} else {
		echo ('{"status":"invalid token"}');
	}
	TDBClose($con);
}

function ac_view(){
	$con = TDBConnect();
	$user = GetUser();
	$page = intval(GetParam("page"));
	if($page==0) $page=1;
	$rowCnt = 20 * ($page - 1);
	$weibos = TDBFetchAll("SELECT * FROM tp_weibos order by tid desc LIMIT {$rowCnt},20 ");
	$rsp = array('status' => 'ok','page' => $page,'weibos' => $weibos);
	echo(json_encode($rsp));
	TDBClose($con);
}

?>