<?php
if (!defined("IN_TWIMI_PHP")) die('{"status":"forbidden access"}');
define("TP_MODNAME","论坛");

function chinesesubstr($str,$start,$len){
	$strlen = $len - $start;
	for($i=0;$i<$strlen;$i++){
		if(ord(substr($str,$i,1))>0xa0){ 
			$tmpstr.=substr($str,$i,3);
			$i+=2;
		}else{
			$tmpstr.=substr($str,$i,1);
		}
	}
	return $tmpstr;
}

function ac_index(){
	ac_view();
}

function ac_post(){
	$con = TDBConnect();
	$user = GetUser();
	if ($user != null) {
		$title = GetParam('title');
		$message = GetParam('message');
		$username = $user["username"];
		$nickname = $user["nickname"];
		$timeline = time();
		if ($title != "" && message != "") {
			$datas = array('username' => $username, 'nickname' => $nickname, 'title' => $title, 'message' => $message, 'timeline' => $timeline);
			$tid = TDBInsert($datas, "tp_posts");
			//$rsp = array('status' => 'ok' , 'tid' => $tid);
			//echo (json_encode($rsp));
			header('Location: index.php?mod=post&action=viewpost&tid='.$tid);
		} else {
			include 'template/addpost.html';
		}
	} else {
		echo ('{"status":"invalid token"}');
	}
	TDBClose($con);
}

function ac_view(){
	$con = TDBConnect();
	$user = GetUser();
	$page = intval(GetParam("page"));
	if($page==0) $page=1;
	$rowCnt = 20 * ($page - 1);
	$posts = TDBFetchAll("SELECT * FROM tp_posts order by tid desc LIMIT {$rowCnt},20 ");
	include 'template/postlist.html';
	TDBClose($con);
}

function ac_mypost(){
	$con = TDBConnect();
	$user = GetUser();
	if ($user != null) {
		$page = intval(GetParam("page"));
		if($page==0) $page=1;		
		$username = $user["username"];
		$rowCnt = 20 * ($page - 1);
		$posts = TDBFetchAll("SELECT * FROM tp_posts WHERE username='{$username}' order by tid desc LIMIT {$rowCnt},20 ");
		$rsp = array('status' => 'ok','page' => $page,'posts' => $posts);
		echo(json_encode($rsp));	
	} else {
		echo ('{"status":"invalid token"}');
	}
	TDBClose($con);
}

function ac_viewpost(){
	$con = TDBConnect();
	$user = GetUser();
	$tid = GetParam("tid");
	if($tid!=""){
		$page = intval(GetParam("page"));
		if($page==0) $page=1;
		$rowCnt = 20 * ($page - 1);
		$post = TDBFetchOne("SELECT * FROM tp_posts WHERE tid='$tid' ");
		$comments = TDBFetchAll("SELECT * FROM tp_comments WHERE tid='$tid' LIMIT {$rowCnt},20 ");
		//$rsp = array('status' => 'ok','post' => $post,'page' => $page,'comments' => $comments);
		//echo(json_encode($rsp));
		include 'template/viewpost.html';
	}else{
		echo ('{"status":"empty tid"}');
	}
	TDBClose($con);
}

function ac_comment(){
	$con = TDBConnect();
	$user = GetUser();
	if ($user != null) {
		$tid = GetParam("tid");
		if($tid!=""){
			$message = GetParam('message');
			$username = $user["username"];
			$nickname = $user["nickname"];
			$timeline = time();
			if (message != "") {
				$datas = array('tid' => $tid,'username' => $username, 'nickname' => $nickname, 'message' => $message, 'timeline' => $timeline);
				$cmid = TDBInsert($datas, "tp_comments");
				//$rsp = array('status' => 'ok' , 'tid' => $tid , 'cmid' => $cmid);
				//echo (json_encode($rsp));
				header('Location: index.php?mod=post&action=viewpost&tid='.$tid);
			} else {
				echo ('{"status":"empty arguments"}');
			}
		}else{
			echo ('{"status":"empty tid"}');
		}
	} else {
		echo ('{"status":"invalid token"}');
	}
	TDBClose($con);
}

function ac_delete(){
	$con = TDBConnect();
	$user = GetUser();
	if ($user != null) {
		$tid = GetParam("tid");
		if($tid!=""){
			$post = TDBFetchOne("SELECT * FROM tp_posts WHERE tid='$tid' ");
			if($post['username']==$user['username']){
				$dtid = TDBDelete("tp_posts"," tid='$tid' ");
				TDBDelete("tp_comments"," tid='$tid' ");
				$rsp = array('status' => 'ok' , 'tid' => $dtid);
				echo (json_encode($rsp));
			}else{
				echo ('{"status":"invalid access"}');
			}
		}else{
			echo ('{"status":"empty tid"}');
		}
	} else {
		echo ('{"status":"invalid token"}');
	}
	TDBClose($con);
}

?>