<?php
if (!defined("IN_TWIMI_PHP")) die('{"status":"forbidden access"}');
function ac_login(UserService $userService)
{
    if (strtolower($_SERVER['REQUEST_METHOD']) == 'get') {
        if (isset($_REQUEST['referer'])) {
            session_start();
            $referer = $_REQUEST['referer'];
            $_SESSION['referer'] = $referer;
        }
        view::render('login.html', ['referer' => $referer]);
    } else if (isset($_POST["username"]) && isset($_POST["password"])) {
        if ($result = $userService->login($_POST["username"], $_POST["password"])) {
            if ($result['ret'] == 0) {
                session_start();
                $_SESSION['access_token'] = $result['token'];
                if (isset($_SESSION['referer'])) {
                    $referer = $_SESSION['referer'];
                    unset($_SESSION['referer']);
                    view::redirect($referer);
                } else {
                    view::redirect('index', 'index');
                }
            } else {
                view::render('login.html', $result);
            }
        }
    }
}