<?php
if (!defined("IN_TWIMI_PHP")) die('{"status":"forbidden access"}');
function GetUser()
{
    session_start();
    if (isset($_SESSION['access_token'])) {
        $access_token = $_SESSION['access_token'];
        if ($user_row = database::getInstance()->fetchOne("select * from tp_user where token=:tk", ['tk' => $access_token])) {
            $result = array('id' => $user_row['id'], 'username' => $user_row['username'], 'nickname' => $user_row['nickname'], 'email' => $user_row['email']);
            return $result;
        } else {
            return null;
        }
    } else {
        return null;
    }
}