<?php

namespace Felis\Silvestris;

use \PDO;

class Database {
  private $dbhost, $query = "", $params = [], $data = [];
  public static $err;

  public function __construct($dbh, $uname, $pass){
    try {
      $this->dbhost = new PDO($dbh, $uname, $pass);
      $this->dbhost->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (\PDOException $e) {
      self::$err = "CONNECTION FAILED: ".$e->getMessage();
      self::fatal();
    }
  }

  public function __clone(){ }

  public function __destruct(){ $this->dbhost = null; }

  public function execute(){
    try {
      $stmt = $this->dbhost->prepare($this->query);
      $stmt->execute($this->params);
      return true;
    } catch (\PDOException $e) {
      self::$err = $e->getMessage();
      self::fatal();
    }
    return false;
  }

  public function fetch($fetchAll = false){
    try {
      $stmt = $this->dbhost->prepare($this->query);
      if (!empty($this->params)) $stmt->execute($this->params);
      else $stmt->execute();
      if (!$fetchAll) $this->data = $stmt->fetch(PDO::FETCH_OBJ);
      else $this->data = $stmt->fetchAll(PDO::FETCH_OBJ);
    } catch (\PDOException $e) {
      self::$err = $e->getMessage();
      self::fatal();
    }
    return $this;
  }

  public function toJson(){
    $this->data = json_encode($this->data);
    return $this;
  }

  public function get(){
    $data = $this->data;
    if (!empty($data)) return $data;
    else if (is_string($data) && strlen($this->data) != 2) return 'false';
    return false;
  }

  public function setParam($column){
    return ':'.str_replace(str_split('\'"`[] '), '', $column);
  }

  public function select($table, $fields="*"){
    $this->query = "SELECT {$fields} FROM {$table}";
    return $this;
  }

  public function where($conditions = [], $optionalClauses = ""){
    $whereClauses = ""; $i = 0;
    foreach ($conditions as $column => $clause) {
      $bindKey = $this->setParam($column);
      $this->params[$bindKey] = array_values($clause)[$i];
      $condition = $column.array_keys($clause)[0].array_keys($this->params)[$i]." AND ";
      $whereClauses .= $condition;
    }
    $whereClauses = substr($whereClauses, 0, strrpos($whereClauses , ' AND '));
    $this->query .= " WHERE {$whereClauses} {$optionalClauses}";
    return $this;
  }

  public function insert($tabel, $fields = array()){
    $columns = implode(", ", array_keys($fields));
    foreach ($fields as $column => $value) {
      $bindKey = $this->setParam($column);
      $this->params[$bindKey] = $value;
    }
    $paramStr = implode(", ", array_keys($this->params));
    $this->query = "INSERT INTO {$tabel} ({$columns}) VALUES ({$paramStr});";
    return $this->execute();
  }

  public function delete($table, $column, $value){
    $this->params[$this->setParam($column)] = $value;
    $this->query = "DELETE FROM {$table} WHERE {$column} = '{$value}';";
    return $this->execute();
  }

  public function update($table, $column, $value, $set = array()){
    $setResult = "";
    foreach($set as $setColumn => $setValue){
      $bindKey = $this->setParam($setColumn);
      $this->params[$bindKey] = $setValue;
      $setString = $setColumn.' = '.$bindKey.', ';
      $setResult .= $setString;
    }
    $setResult = substr($setResult, 0, strrpos($setResult , ','));
    $this->query = "UPDATE {$table} SET {$setResult} WHERE {$column} = '{$value}';";
    return $this->execute();
  }

  public static function fatal(){
    die(self::$err);
  }
  
}
?>
