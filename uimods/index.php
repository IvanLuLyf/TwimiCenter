<?php
if (!defined("IN_TWIMI_PHP")) die('{"status":"forbidden access"}');
$con = TDBConnect();
$user = GetUser();
if($user==null){
	header('Location: index.php?mod=login');
}else{
	header('Location: index.php?mod=post&action=view');
}
TDBClose($con);
?>
