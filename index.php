<?php
	header("Content-Type: text/html; charset=UTF-8");
	define("IN_TWIMI_PHP","True",TRUE);
	include "config.php";
	include "db.php";
	function GetParam($argname){
		$result = empty($_POST[$argname])?"":mysql_escape_string($_POST[$argname]);
		if($result == "") $result = empty($_GET[$argname])?"":mysql_escape_string($_GET[$argname]);
		return $result;
	}
	$mod = GetParam("mod");
	include "auth.php";
	if($mod!=""){
		include "mods/$mod.php";
	}else{
		echo '{"status":"empty mod"}';
	}
?>