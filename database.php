<?php

class database
{
    private $conn;
    private static $instance;

    private function __construct()
    {
        $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
        $this->conn = new PDO($dsn, DB_USER, DB_PASS);
    }

    public static function getInstance(): database
    {
        if (self::$instance == null) {
            self::$instance = new database();
        }
        return self::$instance;
    }

    public function insert(array $data, $table)
    {
        $keys = implode(',', array_keys($data));
        $values = implode(',:', array_keys($data));
        $sql = "insert into {$table} ({$keys}) values(:{$values})";
        $pst = $this->conn->prepare($sql);
        foreach ($data as $k => $v) {
            $pst->bindParam(':' . $k, $value);
        }
        $pst->execute();
        return $this->conn->lastInsertId();
    }

    public function update(array $data, $table, $where = null, $condition = [])
    {
        $sets = [];
        foreach ($data as $key => $value) {
            $sets[] = "`{$key}` = :{$key}";
        }
        $updates = implode(',', $sets);
        $where = $where == null ? '' : ' WHERE ' . $where;
        $sql = "update {$table} set {$updates} {$where}";
        $pst = $this->conn->prepare($sql);
        foreach ($data as $k => $v) {
            $pst->bindParam(':' . $k, $value);
        }
        foreach ($condition as $k => $v) {
            $pst->bindParam(':' . $k, $value);
        }
        $pst->execute();
        return $pst->rowCount();
    }

    public function delete($table, $where = null, $condition = [])
    {
        $where = $where == null ? '' : ' WHERE ' . $where;
        $sql = "delete from {$table} {$where}";
        $pst = $this->conn->prepare($sql);
        foreach ($condition as $k => $v) {
            $pst->bindParam(':' . $k, $value);
        }
        $pst->execute();
        return $pst->rowCount();
    }

    public function fetchOne($sql, $condition = [])
    {
        $pst = $this->conn->prepare($sql);
        foreach ($condition as $k => $v) {
            $pst->bindParam(':' . $k, $value);
        }
        $pst->execute();
        return $pst->fetch();
    }

    public function fetchAll($sql, $condition = [])
    {
        $pst = $this->conn->prepare($sql);
        foreach ($condition as $k => $v) {
            $pst->bindParam(':' . $k, $value);
        }
        $pst->execute();
        return $pst->fetchAll();
    }
}