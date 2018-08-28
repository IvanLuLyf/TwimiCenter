<?php
header("Content-Type: text/html; charset=UTF-8");
define("IN_TWIMI_PHP", "True", TRUE);
include "config.php";
include "database.php";
include "view.php";

include "uiauth.php";
if (isset($_REQUEST['mod'])) {
    $mod = $_REQUEST['mod'];
    if (file_exists("uimods/$mod.php")) {
        include "uimods/$mod.php";
        $action = isset($_REQUEST['action']) ? "ac_" . $_REQUEST['action'] : null;
        if ($action != null && is_callable($action)) {
            call_user_func($action);
        } else if ($action == null && is_callable("ac_index")) {
            call_user_func("ac_index");
        }
    } else {
        echo '{"status":"mod not exists"}';
    }
} else {
    header('Location: index.php?mod=index');
}