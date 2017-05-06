<?php
if (!defined("IN_TWIMI_PHP")) die('{"status":"forbidden access"}');
function TDBConnect() {
	$con = mysql_connect(DB_HOST, DB_USER, DB_PASS);
	if (!$con) {
		die('Could not connect: ' . mysql_error());
	}
	mysql_select_db(DB_NAME, $con);
	mysql_query("set names 'utf8'");
	return $con;
}
function TDBInsert($datas, $tb) {
	$keys = join(',', array_keys($datas));
	$values = "'" . join("','", array_values($datas)) . "'";
	$sql = "insert {$tb}({$keys}) VALUES ({$values})";
	$res = mysql_query($sql);
	if ($res) {
		return mysql_insert_id();
	} else {
		return false;
	}
}
function TDBUpdate($datas, $tb, $where = null) {
	foreach ($datas as $key => $val) {
		$sets.= $key . "='" . $val . "',";
	}
	$sets = rtrim($sets, ',');
	$where = $where == null ? '' : ' WHERE ' . $where;
	$sql = "UPDATE {$tb} SET {$sets} {$where}";
	$res = mysql_query($sql);
	if ($res) {
		return mysql_affected_rows();
	} else {
		return false;
	}
}
function TDBDelete($tb, $where = null) {
	$where = $where == null ? '' : ' WHERE ' . $where;
	$sql = "DELETE FROM {$tb}{$where}";
	$res = mysql_query($sql);
	if ($res) {
		return mysql_affected_rows();
	} else {
		return false;
	}
}
function TDBFetchOne($sql,$result_type=MYSQL_ASSOC) {
	$result = mysql_query($sql);
	if ($result && mysql_num_rows($result) > 0) {
		return mysql_fetch_array($result,$result_type);
	} else {
		return false;
	}
}
function TDBFetchAll($sql,$result_type=MYSQL_ASSOC) {
	$result = mysql_query($sql);
	if ($result && mysql_num_rows($result) > 0) {
		while ($row = mysql_fetch_array($result,$result_type)) {
			$rows[] = $row;
		}
		return $rows;
	} else {
		return false;
	}
}
function TDBFreeResult($result) {
	return mysql_free_result($result);
}
function TDBClose($link = null) {
	return mysql_close($link);
}
?>