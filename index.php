<?php
  require_once __DIR__ .'/vendor/autoload.php';

  use Felis\Silvestris\Database;

  $db = new Database('mysql:host=localhost;dbname=HaruptDB', 'root', 'mysql');
  $sql = $db->delete('users', 'id', 11);

  var_dump($sql);
?>
