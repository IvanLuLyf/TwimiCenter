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

    public function register($username, $password, $email, $nickname = '')
    {
        if (isset($password) && isset($email)) {
            if (preg_match('/^[A-Za-z0-9_]+$/u', $username) && strlen($username) >= 4) {
                if ($row = database::getInstance()->fetchOne("select * from tp_user where username=:username", ['username' => $username])) {
                    return ['ret' => 1003, 'status' => "user exists", 'tp_error_msg' => "用户名已存在"];
                } else {
                    $nickname = $nickname == '' ? $username : $nickname;
                    $timestamp = time();
                    $tp_token = md5($password . strtolower($username) . $timestamp);
                    $user_data = ['username' => $username, 'email' => $email, 'password' => md5($password), 'nickname' => $nickname, 'token' => $tp_token, 'expire' => $timestamp + 604800];
                    if ($uid = database::getInstance()->insert($user_data, 'tp_user')) {
                        return ['ret' => 0, 'status' => 'ok', 'id' => $uid, 'username' => $username, 'email' => $email, 'token' => $tp_token, 'nickname' => $nickname];
                    } else {
                        return ['ret' => 1006, 'status' => "database error", 'tp_error_msg' => "数据库内部出错"];
                    }
                }
            } else {
                return ['ret' => 1005, 'status' => "invalid username", 'tp_error_msg' => "用户名仅能为字母数字且长度大于4"];
            }
        } else {
            return ['ret' => 1004, 'status' => "empty arguments", 'tp_error_msg' => "参数不能为空"];
        }
    }
}