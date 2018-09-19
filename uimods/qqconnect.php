<?php
if (!defined("IN_TWIMI_PHP")) die('{"status":"forbidden access"}');

function ac_index()
{
    $user = GetUser();
    $uid = $user['id'];
    if ($bind_row = database::getInstance()->fetchOne("select * from tp_qqbind where uid=:uid", ['uid' => $uid])) {
        $bind_uid = $bind_row['buid'];
        view::render('qqsetting.html');
    }
}

function ac_avatar()
{
    $imgUrl = "images/avatar.jpg";
    if (isset($_REQUEST['uid'])) {
        $uid = $_REQUEST['uid'];
    } else if (($user = GetUser()) != null) {
        $uid = $user['id'];
    } else {
        $uid = 0;
    }
    if ($bind_row = database::getInstance()->fetchOne("select * from tp_qqbind where uid=:uid", ['uid' => $uid])) {
        $bind_uid = $bind_row['buid'];
        $imgUrl = "http://qzapp.qlogo.cn/qzapp/100245390/$bind_uid/100";
    }
    view::redirect($imgUrl);
}

function ac_connect()
{
    if (QQ_LOGIN) {
        view::redirect('https://graph.qq.com/oauth2.0/authorize?response_type=code&client_id=' . QQ_APP_KEY . '&redirect_uri=' . urlencode(QQ_CALLBACK));
    }
}

function ac_callback()
{
    if (isset($_GET['code'])) {
        $token = get_access_token($_GET['code']);
        $open_id = get_open_id($token['access_token']);
    }
    if (isset($token) && isset($open_id)) {
        $bind_uid = $open_id['openid'];
        if ($bind_row = database::getInstance()->fetchOne("select * from tp_qqbind where buid=:buid", ['buid' => $bind_uid])) {
            $uid = $bind_row['uid'];
            $user_row = database::getInstance()->fetchOne("select * from tp_user where id=:uid", ['uid' => $uid]);
            $updates = array('token' => $token['access_token'], 'expire' => "0");
            database::getInstance()->update($updates, "tp_qqbind", "buid=:buid", ['buid' => $bind_uid]);
            session_start();
            $_SESSION['access_token'] = $user_row['token'];
            if (isset($_SESSION['referer'])) {
                $referer = $_SESSION['referer'];
                unset($_SESSION['referer']);
                view::redirect($referer);
            } else {
                view::redirect('index', 'index');
            }
        } else {
            session_start();
            $_SESSION['qq_uid'] = $bind_uid;
            $_SESSION['qq_token'] = $token['access_token'];
            $_SESSION['qq_expire'] = $token['expires_in'];
            $user_info = get_user_info($token['access_token'], $open_id['openid']);
            view::render('oauth_connect.html', ['oauth' => ['nickname' => $user_info['nickname'], 'type' => 'qqconnect', 'name' => 'QQ']]);
        }
    }
}

function ac_bind(UserService $userService)
{
    $bind_type = $_REQUEST['type'];
    if ($bind_type == 'reg') {
        $result = $userService->register($_POST['username'], $_POST['password'], $_POST['email'], $_POST['nickname']);
    } else {
        $result = $userService->login($_POST['username'], $_POST['password']);
    }
    if ($result['ret'] == 0) {
        session_start();
        $_SESSION['access_token'] = $result['token'];
        $bind_data = array('uid' => $result['id'], 'buid' => $_SESSION['qq_uid'], 'token' => $_SESSION['qq_token'], 'expire' => $_SESSION['qq_expire']);
        database::getInstance()->insert($bind_data, "tp_qqbind");
        if (isset($_SESSION['referer'])) {
            $referer = $_SESSION['referer'];
            unset($_SESSION['referer']);
            view::redirect($referer);
        } else {
            view::redirect('index', 'index');
        }
    } else {
        $result['type'] = 'qqconnect';
        $result['name'] = 'QQ';
        view::render('oauth_connect.html', $result);
    }
}

function get_open_id($token)
{
    $str = curl_get_content('https://graph.qq.com/oauth2.0/me?access_token=' . $token);
    if (strpos($str, "callback") !== false) {
        $lpos = strpos($str, "(");
        $rpos = strrpos($str, ")");
        $str = substr($str, $lpos + 1, $rpos - $lpos - 1);
    }
    $user = json_decode($str, TRUE);
    return $user;
}

function get_access_token($code)
{
    $token_url = 'https://graph.qq.com/oauth2.0/token?grant_type=authorization_code&' . 'client_id=' . QQ_APP_KEY . '&redirect_uri=' . urlencode(QQ_CALLBACK) . '&client_secret=' . QQ_APP_SECRET . '&code=' . $code;
    $token = array();
    parse_str(curl_get_content($token_url), $token);
    return $token;
}

function get_user_info($token, $open_id)
{
    $user_info_url = 'https://graph.qq.com/user/get_user_info?' . 'access_token=' . $token . '&oauth_consumer_key=' . QQ_APP_KEY . '&openid=' . $open_id . '&format=json';
    $info = json_decode(curl_get_content($user_info_url), TRUE);
    return $info;
}

function curl_get_content($url)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
    $result = curl_exec($ch);
    curl_close($ch);
    return $result;
}