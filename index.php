<?php
  require_once __DIR__ .'/vendor/autoload.php';
  use Felis\Silvestris\Database;

  $db = new Database('mysql:host=localhost;dbname=ElogDB', 'root', 'mysql');
  $data = $db->select('logins', 'LoginID');
  print_r($data);
?>
