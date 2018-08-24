<?php
if (!defined("IN_TWIMI_PHP")) die('{"status":"forbidden access"}');
define("TP_MOD_NAME", "论坛");

function ac_post()
{
    $user = GetUser();
    if ($user != null) {
        if (isset($_POST['title']) && isset($_POST['message'])) {
            $new_data = ['username' => $user["username"], 'title' => $_POST['title'], 'message' => $_POST['message'], 'timeline' => time()];
            $tid = database::getInstance()->insert($new_data, "tp_posts");
            $rsp = array('status' => 'ok', 'tid' => $tid);
            echo(json_encode($rsp));
        } else {
            echo('{"status":"empty arguments"}');
        }
    } else {
        echo('{"status":"invalid token"}');
    }
}

function ac_view()
{
    $page = isset($_REQUEST['page']) ? intval($_REQUEST['page']) : 1;
    $rowCnt = 20 * ($page - 1);
    $posts = database::getInstance()->fetchAll("select tp_posts.*,tp_user.nickname from tp_posts left join tp_user on (tp_posts.username=tp_user.username) order by tid desc LIMIT {$rowCnt},20 ");
    $rsp = array('status' => 'ok', 'page' => $page, 'posts' => $posts);
    echo(json_encode($rsp));
}

function ac_mypost()
{
    $user = GetUser();
    if ($user != null) {
        $page = isset($_REQUEST['page']) ? intval($_REQUEST['page']) : 1;
        $username = $user["username"];
        $rowCnt = 20 * ($page - 1);
        $posts = database::getInstance()->fetchAll("select tp_posts.*,tp_user.nickname from tp_posts left join tp_user on (tp_posts.username=tp_user.username) where username=:username order by tid desc LIMIT {$rowCnt},20 ", [':username' => $username]);
        $rsp = array('status' => 'ok', 'page' => $page, 'posts' => $posts);
        echo(json_encode($rsp));
    } else {
        echo('{"status":"invalid token"}');
    }
}

function ac_viewpost()
{
    if (isset($_REQUEST['tid'])) {
        $tid = intval($_REQUEST['tid']);
        $page = isset($_REQUEST['page']) ? intval($_REQUEST['page']) : 1;
        $rowCnt = 20 * ($page - 1);
        $post = database::getInstance()->fetchOne("select tp_posts.*,tp_user.nickname from tp_posts left join tp_user on (tp_posts.username=tp_user.username) where tid=:tid ", ['tid' => $tid]);
        $comments = database::getInstance()->fetchAll("select tp_comments.*,tp_user.nickname from tp_comments left join tp_user on (tp_comments.username=tp_user.username) where tid=:tid and aid=1 limit {$rowCnt},20 ", ['tid' => $tid]);
        $rsp = array('status' => 'ok', 'post' => $post, 'page' => $page, 'comments' => $comments);
        echo(json_encode($rsp));
    } else {
        echo('{"status":"empty tid"}');
    }
}

function ac_comment()
{
    $user = GetUser();
    if ($user != null) {
        if (isset($_REQUEST['tid'])) {
            $tid = intval($_REQUEST['tid']);
            if (isset($_POST['message'])) {
                $new_data = array('tid' => $tid, 'username' => $user["username"], 'message' => $_POST['message'], 'timeline' => time());
                $cmid = TDBInsert($new_data, "tp_comments");
                $rsp = array('status' => 'ok', 'tid' => $tid, 'cmid' => $cmid);
                echo(json_encode($rsp));
            } else {
                echo('{"status":"empty arguments"}');
            }
        } else {
            echo('{"status":"empty tid"}');
        }
    } else {
        echo('{"status":"invalid token"}');
    }
}

function ac_delete()
{
    $user = GetUser();
    if ($user != null) {
        if (isset($_REQUEST['tid'])) {
            $tid = intval($_REQUEST['tid']);
            $post = database::getInstance()->fetchOne("select * from tp_posts where tid=:tid", [':tid' => $tid]);
            if ($post['username'] == $user['username']) {
                $effect = database::getInstance()->delete("tp_posts", "tid=:tid", [':tid', $tid]);
                database::getInstance()->delete("tp_comments", "tid=:tid", [':tid', $tid]);
                if ($effect > 0) {
                    $rsp = array('status' => 'ok', 'tid' => $tid);
                    echo(json_encode($rsp));
                } else {
                    echo('{"status":"database error"}');
                }
            } else {
                echo('{"status":"invalid access"}');
            }
        } else {
            echo('{"status":"empty tid"}');
        }
    } else {
        echo('{"status":"invalid token"}');
    }
}