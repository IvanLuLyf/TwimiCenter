<?php
/**
 * Created by PhpStorm.
 * User: IvanLu
 * Date: 2018/9/16
 * Time: 20:02
 */

class UserService
{
    public function login($username, $password)
    {
        if ($row = database::getInstance()->fetchOne("select * from tp_user where username=:username", ['username' => $username])) {
            if ($row['password'] == md5($password)) {
                $tp_id = $row['id'];
                $tp_token = md5(strtolower($username) . $tp_id);
                $rsp = ['ret' => 0, 'status' => 'ok', 'id' => $tp_id, 'username' => $row['username'], 'email' => $row['email'], 'token' => $tp_token, 'nickname' => $row['nickname']];
                $updates = ['token' => $tp_token];
                database::getInstance()->update($updates, "tp_user", "id=:uid", ['uid' => $tp_id]);
                return $rsp;
            } else {
                return ['ret' => 1001, 'status' => "password error", 'tp_error_msg' => "密码错误"];
            }
        } else {
            return ['ret' => 1002, 'status' => "user not exists", 'tp_error_msg' => "用户名不存在"];
        }
    }
}