<?php
if (!defined("IN_TWIMI_PHP")) die('{"status":"forbidden access"}');
$con = TDBConnect();

$TauthType = GetParam("type");
$TauthToken = GetParam("token");

if($TauthType=="bh"){
	$strUserinfo = do_post_request("http://bh.twimi.cn/api.php?mod=user&action=getinfo","token=$TauthToken");
	$TuserInfo=json_decode($strUserinfo); 
	$strUserinfo = do_post_request("http://bh.twimi.cn/api.php?mod=post&action=comment","token=$TauthToken&tid=90&message=From+Twimi Center TAuth");
	include 'template/ttest.html';
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
