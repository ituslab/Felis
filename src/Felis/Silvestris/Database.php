<?php

namespace Felis\Silvestris;

use \PDO;

class Database {
  private $dbhost, $query = "", $params = [], $data = [];
  public static $err;

  public function __construct(String $dbh, String $uname, String $pass){
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

  public static function connect(String $database){
    $json = json_decode(file_get_contents(__DIR__.'/../../../config.json'));
    $config = $json->$database;
    return new self($config->dbh, $config->user, $config->password);
  }

  private function execute(){
    try {
      $stmt = $this->dbhost->prepare($this->query);
      if (!empty($this->params)) $stmt->execute($this->params);
      else $stmt->execute();
      return true;
    } catch (\PDOException $e) {
      self::$err = $e->getMessage();
      self::fatal();
    }
    return false;
  }

  public function fetch(Bool $fetchAll = false){
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

  private function setParam(String $column){
    return ':'.str_replace(str_split('\'"`[] '), '', $column);
  }

  private function removeLastString(String $string, String $stringToDelete){
    return substr($string, 0, strrpos($string, $stringToDelete));
  }

  public function query(String $query){
    $this->query = $query;
    return $this;
  }

  public function select(String $table, String $fields="*", String $optionalClauses = ''){
    $this->query = "SELECT {$fields} FROM {$table} {$optionalClauses}";
    return $this;
  }

  public function where(Array $conditions = [], String $optionalClauses = ""){
    $whereClauses = "";
    foreach ($conditions as $column => $clause) {
      $bindKey = $this->setParam($column);
      $this->params[$bindKey] = array_values($clause)[0];
      $operator = array_keys($clause)[0];
      $condition = "{$column} {$operator} {$bindKey} AND ";
      $whereClauses .= $condition;
    }
    $whereClauses = $this->removeLastString($whereClauses, ' AND ');
    $this->query .= " WHERE {$whereClauses} {$optionalClauses}";
    return $this;
  }

  public function insert(String $tabel, Array $fields = []){
    $columns = implode(", ", array_keys($fields));
    foreach ($fields as $column => $value) {
      $bindKey = $this->setParam($column);
      $this->params[$bindKey] = $value;
    }
    $paramStr = implode(", ", array_keys($this->params));
    $this->query = "INSERT INTO {$tabel} ({$columns}) VALUES ({$paramStr});";
    return $this->execute();
  }

  public function delete(String $table, String $column, $value){
    $this->params[$this->setParam($column)] = $value;
    $this->query = "DELETE FROM {$table} WHERE {$column} = '{$value}';";
    return $this->execute();
  }

  public function update(String $table, String $column, $value, Array $set = []){
    $setResult = "";
    foreach($set as $setColumn => $setValue){
      $bindKey = $this->setParam($setColumn);
      $this->params[$bindKey] = $setValue;
      $setString = "{$setColumn} = {$bindKey}, ";
      $setResult .= $setString;
    }
    $setResult = $this->removeLastString($setResult, ', ');
    $this->query = "UPDATE {$table} SET {$setResult} WHERE {$column} = '{$value}';";
    return $this->execute();
  }

  public static function fatal(){
    die(self::$err);
  }

}
?>
