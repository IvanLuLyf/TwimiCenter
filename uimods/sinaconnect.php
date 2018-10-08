<?php
if (!defined("IN_TWIMI_PHP")) die('{"status":"forbidden access"}');

include_once('sinaweibo/saetv2.ex.class.php');

function ac_connect()
{
    $o = new SaeTOAuthV2(WB_APP_KEY, WB_APP_SECRET);
    $code_url = $o->getAuthorizeURL(WB_CALLBACK);
    view::redirect($code_url);
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
    if ($row = database::getInstance()->fetchOne("select * from tp_sinabind where uid=:uid", ['uid' => $uid])) {
        $o = new SaeTOAuthV2(WB_APP_KEY, WB_APP_SECRET);
        $c = new SaeTClientV2(WB_APP_KEY, WB_APP_SECRET, $row['token']);
        $user_message = $c->show_user_by_id($row['buid']);
        $imgUrl = $user_message['avatar_large'];
    }
    view::redirect($imgUrl);
}

function ac_callback()
{
    session_start();
    $o = new SaeTOAuthV2(WB_APP_KEY, WB_APP_SECRET);
    if (isset($_REQUEST['code'])) {
        $keys = array();
        $keys['code'] = $_REQUEST['code'];
        $keys['redirect_uri'] = WB_CALLBACK;
        try {
            $token = $o->getAccessToken('code', $keys);
        } catch (OAuthException $e) {
        }
    }
    if (isset($token)) {
        $c = new SaeTClientV2(WB_AKEY, WB_SKEY, $token['access_token']);
        $uid_get = $c->get_uid();
        $bind_uid = $uid_get['uid'];
        if ($bind_row = database::getInstance()->fetchOne("select * from tp_sinabind where buid=:buid", ['buid' => $bind_uid])) {
            $uid = $bind_row['uid'];
            $user_row = database::getInstance()->fetchOne("select * from tp_user where id=:uid", ['uid' => $uid]);
            $updates = array('token' => $token['access_token'], 'expire' => "0");
            database::getInstance()->update($updates, "tp_sinabind", "buid=:buid", ['buid' => $bind_uid]);
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
            $_SESSION['wb_uid'] = $bind_uid;
            $_SESSION['wb_token'] = $token['access_token'];
            $_SESSION['wb_expire'] = "0";
            $user_message = $c->show_user_by_id($bind_uid);
            view::render('oauth_connect.html', ['oauth' => ['nickname' => $user_message['screen_name'], 'type' => 'sinaconnect', 'name' => '微博']]);
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
        $bind_data = array('uid' => $result['id'], 'buid' => $_SESSION['wb_uid'], 'token' => $_SESSION['wb_token'], 'expire' => $_SESSION['wb_expire']);
        database::getInstance()->insert($bind_data, "tp_sinabind");
        if (isset($_SESSION['referer'])) {
            $referer = $_SESSION['referer'];
            unset($_SESSION['referer']);
            view::redirect($referer);
        } else {
            view::redirect('index', 'index');
        }
    } else {
        $result['type'] = 'sinaconnect';
        $result['name'] = '微博';
        view::render('oauth_connect.html', $result);
    }
}