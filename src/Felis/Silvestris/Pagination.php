<?php

namespace Felis\Silvestris;

class Pagination{

    public static function paging(Database $db, $table='', $perPage, $page, $fieldId , $pageElCount){
      $actualPage = ($page * $perPage) - $perPage;
      $total = $db->select($table, "COUNT({$fieldId})")->fetch(true)->get();
      $data = $db->query("SELECT * FROM {$table} LIMIT {$actualPage}, {$perPage}")->fetch(true)->get();
      while ($page % $pageElCount != 0) $page++;
      $startPageEl = $page - ($pageElCount - 1);
      $arrPageEl =  [];
      $isLast = false;
      for($i = 0; $i<$pageElCount; $i++){
        $a = $startPageEl * $perPage;
        $b = $a - ($perPage - 1);
        $newArr = range($b , $a);
        $rIsInclude = self::isInclude($newArr, $total);
        if($rIsInclude){
          array_push($arrPageEl , $startPageEl);
          $isLast= true;
          break;
        }
        array_push($arrPageEl , $startPageEl);
        $startPageEl++;
      }
      if(count($data) <= 0) {
        $arrPageEl = array();
        $isLast = false;
      }
      return [
        'data' => $data,
        'pageEl' => $arrPageEl,
        'isLast' => $isLast
      ];
    }

    private static function isInclude($array, $searchVal){
      $search = gettype(array_search($searchVal, $array));
      if ($search != 'boolean') return true;
      return false;
    }

  }

?>
