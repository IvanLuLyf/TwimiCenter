<?php
if (!defined("IN_TWIMI_PHP")) die('{"status":"forbidden access"}');
session_start(); 
unset($_SESSION['accesstoken']);
header('Location: index.php?mod=index');
?>
