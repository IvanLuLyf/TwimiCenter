<?php
if (!defined("IN_TWIMI_PHP")) die('{"status":"forbidden access"}');
if (isset($_REQUEST['referrer'])) {
    session_start();
    $referer = $_REQUEST['referrer'];
    $_SESSION['referer'] = $referer;
}
if (strtolower($_SERVER['REQUEST_METHOD']) == 'get') {
    include 'template/login.html';
} else if (isset($_POST["username"])) {
    $username = $_POST["username"];
    if ($row = database::getInstance()->fetchOne("select * from tp_user where username=:username", ['username' => $username])) {
        if (isset($_POST["password"]) && $row['password'] == md5($_POST["password"])) {
            $tp_id = $row['id'];
            $tp_token = md5(strtolower($username) . $tp_id);
            $rsp = ['status' => 'ok', 'id' => $tp_id, 'username' => $row['username'], 'email' => $row['email'], 'token' => $tp_token, 'nickname' => $row['nickname']];
            $updates = ['token' => $tp_token];
            database::getInstance()->update($updates, "tp_user", "id=:uid", ['uid' => $tp_id]);
            session_start();
            $_SESSION['access_token'] = $tp_token;
            header('Location: index.php?mod=index');
        } else {
            $tp_error_msg = "密码错误";
            include 'template/login.html';
        }
    } else {
        $tp_error_msg = "用户名不存在";
        include 'template/login.html';
    }
}