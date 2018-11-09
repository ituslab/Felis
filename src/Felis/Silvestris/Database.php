<?php

namespace Felis\Silvestris;

use \PDO;

class Database {
  private $dbh, $query = "", $params = [], $data = [];

  public function __construct($dbh, $uname, $pass){
    try {
      $this->dbh = new PDO($dbh, $uname, $pass);
      $this->dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
      self::fatal("CONNECTION FAILED: ".$e->getMessage());
    }
  }

  public function __clone(){ }

  public function __destruct(){ $this->dbh = null; }

  public static function connect($database){
    $json = json_decode(file_get_contents(__DIR__.'/../../../config.json'));
    $config = $json->$database;
    return new self($config->dbh, $config->user, $config->password);
  }

  public function toJson(){
    $this->data = json_encode($this->data);
    return $this;
  }

  public function get(){
    $data = $this->data;
    if ((is_object($data) || is_array($data)) && !empty($data)) return $data;
    else if (is_string($data)) {
      if ($data !== '[]') return $data;
      return 'false';
    }
    return false;
  }

  public function query($query, $params = []){
    $this->query = $query;
    $this->params = $params;
    return $this;
  }

  public function select($table, $fields="*", $optionalClauses = ''){
    $this->query = "SELECT {$fields} FROM {$table} {$optionalClauses}";
    return $this;
  }

  public function where($conditions = [], $optionalClauses = ""){
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

  public function insert($tabel, $fields = []){
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

  public function update($table, $column, $value, $set = []){
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

  public function execute(){
    try {
      $stmt = $this->dbh->prepare($this->query);
      if (!empty($this->params)) $stmt->execute($this->params);
      else $stmt->execute();
      return true;
    } catch (PDOException $e) {
      self::fatal($e->getMessage());
    }
    return false;
  }

  public function fetch(){
    try {
      $stmt = $this->dbh->prepare($this->query);
      if (!empty($this->params)) $stmt->execute($this->params);
      else $stmt->execute();
      $this->data = $stmt->fetch(PDO::FETCH_OBJ);
    } catch (PDOException $e) {
      self::fatal($e->getMessage());
    }
    return $this;
  }

  public function fetchAll(){
    try {
      $stmt = $this->dbh->prepare($this->query);
      if (!empty($this->params)) $stmt->execute($this->params);
      else $stmt->execute();
      $this->data = $stmt->fetchAll(PDO::FETCH_OBJ);
    } catch (PDOException $e) {
      self::fatal($e->getMessage());
    }
    return $this;
  }

  private function setParam($column){
    return ':'.str_replace(str_split('\'"`[] '), '', $column);
  }

  private function removeLastString($string, $stringToDelete){
    return substr($string, 0, strrpos($string, $stringToDelete));
  }

  private static function fatal($error){
    die($error);
  }

}
?>
