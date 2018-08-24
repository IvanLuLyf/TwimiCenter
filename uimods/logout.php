<?php
if (!defined("IN_TWIMI_PHP")) die('{"status":"forbidden access"}');
session_start();
unset($_SESSION['access_token']);
header('Location: index.php?mod=index');