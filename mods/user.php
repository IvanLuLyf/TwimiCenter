<?php
if (!defined("IN_TWIMI_PHP")) die('{"status":"forbidden access"}');

function ac_update()
{
    $user = GetUser();
    $access_token = GetParam("token");
    if ($user != null) {
        $nickname = GetParam("nickname");
        $row = TDBFetchOne("SELECT * FROM tp_user WHERE token='$access_token'");
        $tp_id = $row['id'];
        if ($nickname != "") {
            $updates = array('nickname' => $nickname);
            //TDBUpdate($updates, "tp_user", " id='$tp_id' ");
            $rsp = array('status' => 'ok', 'nickname' => $nickname);
            echo(json_encode($rsp));
        } else {
            echo('{"status":"empty arguments"}');
        }
    } else {
        echo('{"status":"invalid token"}');
    }
}

function ac_getinfo()
{
    $user = GetUser();
    if ($user != null) {
        $rsp = array('status' => 'ok', 'id' => $user['id'], 'username' => $user['username'], 'nickname' => $user['nickname'], 'email' => $user['email']);
        echo(json_encode($rsp));
    } else {
        echo('{"status":"invalid token"}');
    }
}

function ac_getavatar()
{
    $img_url = "images/avatar.jpg";
    if (isset($_REQUEST['uid'])) {
        if ($row = database::getInstance()->fetchOne("select * from tp_avatar where uid=:uid", ['uid' => $_REQUEST['uid']])) {
            $img_url = $row['url'];
            echo $img_url;
        }
    } else if (isset($_REQUEST['username'])) {
        if ($uid = database::getInstance()->fetchOne("select * from tp_user where username=:username", ['username' => $_REQUEST['username']])['id']) {
            if ($row = database::getInstance()->fetchOne("select * from tp_avatar where uid=:uid", ['uid' => $uid])) {
                $img_url = $row['url'];
                echo $img_url;
            }
        }
    }
    Header("Location:$img_url");
}

function ac_login()
{
    if (isset($_POST["username"])) {
        $username = $_POST["username"];
        if ($row = database::getInstance()->fetchOne("select * from tp_user where username=:username", ['username' => $username])) {
            if (isset($_POST["password"]) && $row['password'] == md5($_POST["password"])) {
                $tp_id = $row['id'];
                $tp_token = md5(strtolower($username) . $tp_id);
                $rsp = array('status' => 'ok', 'id' => $tp_id, 'username' => $row['username'], 'email' => $row['email'], 'token' => $tp_token, 'nickname' => $row['nickname']);
                $updates = array('token' => $tp_token);
                database::getInstance()->update($updates, "tp_user", "id=:uid", ['uid' => $tp_id]);
                echo(json_encode($rsp));
            } else {
                $rsp = array('status' => "password error");
                echo(json_encode($rsp));
            }
        } else {
            $rsp = array('status' => "invalid username");
            echo(json_encode($rsp));
        }
    } else {
        echo('{"status":"empty username"}');
    }
}

function ac_register()
{
    /*
    if (isset($_POST["username"])) {
        $username = $_POST["username"];
        if (database::getInstance()->fetchOne("select * from tp_user where username=:username", [':username' => $username])) {
            echo '{"status":"username exists"}';
        } else {
            if (isset($_POST['password']) && isset($_POST['email'])) {
                $nickname = isset($_POST['nickname'])?$_POST['nickname']:$_POST['username'];
                $new_data = array('username' => $username, 'email' => $_POST['email'], 'password' => md5($_POST['password']), 'nickname' => $nickname);
                database::getInstance()->insert($new_data, "tp_user");
                $row = TDBFetchOne("SELECT * FROM tp_user WHERE username='$username'");
                $tp_id = $row['id'];
                $tp_token = md5(strtolower($username) . $tp_id);
                $updates = array('token' => $tp_token);
                TDBUpdate($updates, "tp_user", " id='$tp_id' ");
                $rsp = array('status' => 'ok', 'id' => $row['id'], 'username' => $row['username'], 'email' => $row['email'], 'token' => $tp_token, 'nickname' => $row['nickname']);
                echo(json_encode($rsp));
            } else {
                echo('{"status":"empty arguments"}');
            }
        }
    } else {
        echo('{"status":"empty username"}');
    }
    */
}