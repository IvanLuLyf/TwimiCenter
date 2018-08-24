<?php
	header("Content-Type: text/html; charset=UTF-8");
	define("IN_TWIMI_PHP","True",TRUE);
	define("IN_TWIMI_AJAX","True",TRUE);
	include "config.php";
	include "db.php";
	function GetParam($argname){
		$result = empty($_POST[$argname])?"":$_POST[$argname];
		if($result == "") $result = empty($_GET[$argname])?"":$_GET[$argname];
		return $result;
	}
	$mod = GetParam("mod");
	$action = "ac_".GetParam("action");
	include "uiauth.php";
	if($mod!=""){
		if(file_exists("mods/$mod.php")){
			include "mods/$mod.php";
			if($action!="" && is_callable($action)){
				call_user_func($action);
			}
		}else{
			echo '{"status":"mod not exists"}';
		}
	}else{
		echo '{"status":"mod is empty"}';
	}
?>