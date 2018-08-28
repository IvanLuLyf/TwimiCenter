<?php
if (!defined("IN_TWIMI_PHP")) die('{"status":"forbidden access"}');
define("TP_MOD_NAME", "论坛");

function ac_index()
{
    ac_view();
}

function ac_post()
{
    $user = GetUser();
    if ($user != null) {
        if (isset($_POST['title']) && isset($_POST['message'])) {
            $new_data = ['username' => $user["username"], 'title' => $_POST['title'], 'message' => $_POST['message'], 'timeline' => time()];
            $tid = database::getInstance()->insert($new_data, "tp_posts");
            view::redirect('post', 'viewpost', ['tid' => $tid]);
        } else {
            view::render("addpost.html", ['user' => $user]);
        }
    } else {
        view::redirect('index.php?mod=login&referrer=' . urlencode(view::get_url('post', 'post')));
    }
}

function ac_view()
{
    $user = GetUser();
    view::render("postlist.html", ['user' => $user]);
}

function ac_mypost()
{
    /*
    $user = GetUser();
    if ($user != null) {
        $page = intval(GetParam("page"));
        if ($page == 0) $page = 1;
        $username = $user["username"];
        $rowCnt = 20 * ($page - 1);
        $posts = TDBFetchAll("SELECT * FROM tp_posts WHERE username='{$username}' order by tid desc LIMIT {$rowCnt},20 ");
        $rsp = array('status' => 'ok', 'page' => $page, 'posts' => $posts);
        echo(json_encode($rsp));
    } else {
        echo('{"status":"invalid token"}');
    }
    */
}

function ac_viewpost()
{
    $user = GetUser();
    if (isset($_REQUEST['tid'])) {
        $tid = intval($_REQUEST['tid']);
        $page = isset($_REQUEST['page']) ? intval($_REQUEST['page']) : 1;
        $rowCnt = 20 * ($page - 1);
        $post = database::getInstance()->fetchOne("select tp_posts.*,tp_user.nickname from tp_posts left join tp_user on (tp_posts.username=tp_user.username) where tid=:tid", ['tid' => $tid]);
        $comments = database::getInstance()->fetchAll("select tp_comments.*,tp_user.nickname from tp_comments left join tp_user on (tp_comments.username=tp_user.username) where tid=:tid and aid=1 LIMIT {$rowCnt},20 ", ['tid' => $tid]);
        view::render("viewpost.html", ['user' => $user, 'post' => $post, 'comments' => $comments]);
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
                header('Location: index.php?mod=post&action=viewpost&tid=' . $tid);
            } else {
                echo('{"status":"empty arguments"}');
            }
        } else {
            echo('{"status":"empty tid"}');
        }
    } else {
        view::redirect('index.php?mod=login&referrer=' . urlencode(view::get_url('post', 'viewpost', ['tid' => $_REQUEST['tid']])));
    }
}

function ac_delete()
{
    /*
    $user = GetUser();
    if ($user != null) {
        $tid = GetParam("tid");
        if ($tid != "") {
            $post = TDBFetchOne("SELECT * FROM tp_posts WHERE tid='$tid' ");
            if ($post['username'] == $user['username']) {
                $dtid = TDBDelete("tp_posts", " tid='$tid' ");
                TDBDelete("tp_comments", " tid='$tid' ");
                $rsp = array('status' => 'ok', 'tid' => $dtid);
                echo(json_encode($rsp));
            } else {
                echo('{"status":"invalid access"}');
            }
        } else {
            echo('{"status":"empty tid"}');
        }
    } else {
        echo('{"status":"invalid token"}');
    }
    */
}