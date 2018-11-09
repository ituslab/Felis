<?php
  /**
   *
   */
  namespace Felis\Silvestris;

  session_start();

  class Session {

    public static function set($set, $value = 0){
      if (is_array($set)) {
        foreach ($set as $key => $val) $_SESSION[$key] = $val;
        return;
      }
      $_SESSION[$set] = $value;
    }

    public static function get($key){
      if (isset($_SESSION[$key])) {
        return $_SESSION[$key];
      }
      return false;
    }

    public static function list(){
      return $_SESSION;
    }

    public static function unset($key){
      if (isset($_SESSION[$key])) {
        unset($_SESSION[$key]);
        return true;
      }
      return false;
    }

    public static function destroy(){
      return session_destroy();
    }

  }

?>
