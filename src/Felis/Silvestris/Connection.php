<?php
namespace Felis\Silvestris;

use Felis\Silvestris\Database;

class Connection extends Database{

  function __construct($driver, $info = []){
    $driver = strtolower($driver);
    $dbhost = "{$driver}:host={$info['host']};dbname={$info['dbname']}";
    new Database($dbhost, $info['user'], $info['pass']);
  }
}

?>
