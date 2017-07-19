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
	$action = "ac_".GetParam("action");
	include "uiauth.php";
	if($mod!=""){
		if(file_exists("uimods/$mod.php")){
			include "uimods/$mod.php";
			if($action!="" && is_callable($action)){
				call_user_func($action);
			}else if($action=="ac_" && is_callable("ac_index")){
				call_user_func("ac_index");
			}
		}else{
			echo '{"status":"mod not exists"}';
		}
	}else{
		header('Location: index.php?mod=index');
	}
?>