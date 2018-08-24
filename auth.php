<?php
if (!defined("IN_TWIMI_PHP")) die('{"status":"forbidden access"}');
function GetUser()
{
    if (!isset($_POST['token'])) {
        return null;
    } else {
        $access_token = $_POST['token'];
        if (isset($_POST['appkey'])) {
            $app_key = $_POST['appkey'];
            if ($token_row = database::getInstance()->fetchOne("select * from tp_tauthtoken where appkey=:ak and token=:tk and UNIX_TIMESTAMP()-expire < 0", [':ak' => $app_key, ':tk' => $access_token])) {
                $uid = $token_row['uid'];
                $user_row = database::getInstance()->fetchOne("select * from tp_user where id=:uid", [':uid' => $uid]);
                $result = array('id' => $user_row['id'], 'username' => $user_row['username'], 'nickname' => $user_row['nickname'], 'email' => $user_row['email']);
                return $result;
            } else {
                return null;
            }
        } else {
            if ($user_row = database::getInstance()->fetchOne("select * from tp_user where token=:tk", [':tk' => $access_token])) {
                $result = array('id' => $user_row['id'], 'username' => $user_row['username'], 'nickname' => $user_row['nickname'], 'email' => $user_row['email']);
                return $result;
            } else {
                return null;
            }
        }
    }
}