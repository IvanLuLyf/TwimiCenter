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
	include "auth.php";
	if($mod!=""){
		if(file_exists("mods/$mod.php")){
			include "mods/$mod.php";
			if($action!=""){
				if(is_callable($action)){
					call_user_func($action);
				}else{
					echo '{"status":"action not exists"}';
				}
			}else{
				echo '{"status":"action is empty"}';
			}
		}else{
			echo '{"status":"mod not exists"}';
		}
	}else{
		echo '{"status":"mod is empty"}';
	}
?>