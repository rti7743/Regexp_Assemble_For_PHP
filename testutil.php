<?php
function is($a , $b , $msg){
    if ($a !== $b ) {
          if ( ! (($b === 1 && $a) || ($b === 0 && !$a) ) ) {
              echo "失敗! {$msg}!!  a:{$a} VS b:{$b}\n";
              var_dump($a);
              var_dump($b);
              foreach(debug_backtrace() as $_) { 
                  echo $_['function'] . ":" . $_['line']."\n";
              }
              die;
          }
    }
    return true;
}

function ok($r , $msg) {
    if (!$r) {
          echo "BAD! {$msg}\n";
          foreach(debug_backtrace() as $_) { 
              echo $_['function'] . ":" . $_['line']."\n";
          }
         die();
    }
}
function eq_set($a,$b) {
   if ( is_array($a) && is_array($b) ) {
       return array_diff($a,$b);
   }
   return $a == $b;
}

function super_array_diff(array $a,array $b,$nest = 0) {
   $akeys = array_keys($a);
   $bkeys = array_keys($b);
   sort($akeys);
   sort($bkeys);
   
   $errormsg = '';
   $indent = str_repeat (' ' , $nest);
   
   foreach($akeys as $ak) {
      if ( !  array_key_exists($ak,$b) ) {
          $errormsg .= "{$indent}b has not key:{$ak}\n";
      }
      else if ( is_array($a[$ak]) && is_array($b[$ak])) {
           $newmsg = super_array_diff($a[$ak],$b[$ak] , $nest + 1);
           if ( $newmsg !== TRUE  ){
               $errormsg .= "{$indent}##NEST miss match key:{$ak}\n";
               $errormsg .= "{$indent}---------------------------------\n";
               $errormsg .= "{$newmsg}";
               $errormsg .= "{$indent}---------------------------------\n";
           }
      }
      else if ($a[$ak] !== $b[$ak]) {
           $errormsg .= "{$indent}miss match key:{$ak}\n";
      }
   }
   foreach($bkeys as $bk) {
      if ( !  array_key_exists($bk,$a) ) {
          $errormsg .= "{$indent}a has not key:{$bk}\n";
      }
   }
   
   if ($errormsg === '') {
       return TRUE;
   }
   return $errormsg;
}


function is_deeply(array $a,array $b,$msg){
   $errormsg = super_array_diff($a , $b ,0);
   if ( $errormsg !== TRUE )
   {
          echo "BAD! {$msg}\n";
          echo "BAD! {$errormsg}\n";
          var_dump($a);
          var_dump($b);
          foreach(debug_backtrace() as $_) { 
              echo $_['function'] . ":" . $_['line']."\n";
          }
         die();
  }
}
