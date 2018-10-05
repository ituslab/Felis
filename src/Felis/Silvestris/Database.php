<?php

namespace Felis\Silvestris;
use \PDO;

class Database {
  private $dbhost, $query;

  public function __construct($dbh, $uname, $pass){
    try {
      $this->dbhost = new PDO($dbh, $uname, $pass);
      $this->dbhost->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
      die ("CONNECTION FAILED: ".$e->getMessage());
    }
  }

  public function __clone(){ }

  public function __destruct(){ $this->dbhost = null; }

  /* BASIC CRUD */
  public function select($table, $fields="*", $optionalClause = ""){
    try {
      $sql = $this->dbhost->query("SELECT $fields FROM $table $optionalClause;", PDO::FETCH_NUM);
      $result = $sql->fetchAll(PDO::FETCH_ASSOC);
      if (!empty($result)) return $result;
    } catch (PDOException $e) {
      die($e->getMessage());
    }
    return false;
  }

  public function delete($table, $column, $value){
    $param = ":".$column;
    try {
      $sql = $this->dbhost->prepare("DELETE FROM $table WHERE $column = '$value';");
      $sql->bindParam($param, $value);
      $sql->execute();
      return true;
    } catch (PDOException $e) {
      die($e->getMessage());
    }
    return false;
  }

  public function insert($tabel, $fields = array()){
    $value = array(); $paramKey = array(); $i = 0;
    $kolom = implode(",", array_keys($fields));
    foreach ($fields as $key => $val) {
      $paramKey[$i] = ':'.str_replace(str_split('`[] '), '', $key);
      $value[$i] = $val; $i++;
    }
    $params = implode(",", $paramKey); $i = 0;
    try {
      $sql = $this->dbhost->prepare("INSERT INTO $tabel ($kolom) VALUES ($params);");
      foreach ($paramKey as $param) {
        $sql->bindParam($param, $value[$i]); $i++;
      }
      $sql->execute();
      return true;
    } catch (PDOException $e) {
      die($e->getMessage());
    }
    return false;
  }

  public function update($table, $column, $value, $set = array()){
    $paramKey = array(); $result = ""; $i = 0;
    foreach($set as $key => $val){
      $paramKey[$i] = ':'.str_replace(str_split('`[] '), '', $key);
      $setString = $key.'='.$paramKey[$i].',';
      $result .= $setString;
      $i++;
    }
    $result = substr($result, 0, strrpos($result , ',')); $i = 0;
    try {
      $sql = $this->dbhost->prepare("UPDATE $table SET $result WHERE $column = '$value';");
      foreach ($set as $val) {
        $sql->bindValue($paramKey[$i], $val); $i++;
      }
      $sql->execute();
      return true;
    } catch (PDOException $e) {
      die($e->getMessage());
    }
    return false;
  }

  public function get($table, $conditions = [], $fields="*", $fetchAll = false, $optionalClause = ''){
    $paramKey = array(); $resultConditions = ""; $i = 0;
    foreach ($conditions as $column => $value) {
      $paramKey[$i] = ':'.str_replace(str_split('!=`[]<> '), '', $column);
      $conString = $column.$paramKey[$i]." AND ";
      $resultConditions .= $conString;
      $i++;
    }
    $resultConditions = substr($resultConditions, 0, strrpos($resultConditions , ' AND ')); $i = 0;
    try {
      $sql = $this->dbhost->prepare("SELECT $fields FROM $table WHERE $resultConditions $optionalClause;");
      foreach ($conditions as $val) {
        $sql->bindValue($paramKey[$i], $val); $i++;
      }
      $sql->execute();
      if (!$fetchAll) $result = $sql->fetch(PDO::FETCH_ASSOC);
      else $result = $sql->fetchAll(PDO::FETCH_ASSOC);
      if (!empty($result)) return $result;
    } catch (PDOException $e) {
      die($e->getMessage());
    }
    return false;
  }
  /* END BASIC CRUD */

}
?>
