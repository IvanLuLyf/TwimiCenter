<?php
header("Content-Type: text/html; charset=UTF-8");
define("IN_TWIMI_PHP", "True", TRUE);
define("IN_TWIMI_API", "True", TRUE);
include "config.php";
include "database.php";

include "auth.php";
if (isset($_REQUEST['mod'])) {
    $mod = $_REQUEST['mod'];
    if (file_exists("mods/$mod.php")) {
        include "mods/$mod.php";
        $action = isset($_REQUEST['action']) ? "ac_" . $_REQUEST['action'] : null;
        if ($action != null && is_callable($action)) {
            call_user_func($action);
        }
    } else {
        echo '{"status":"mod not exists"}';
    }
} else {
    echo '{"status":"mod is empty"}';
}