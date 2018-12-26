<?php

namespace Common\Library\Cli;


class  Process{


    const ROOT ='/www/huangju';


     public  static  function setShell($url){
         $cd = self::ROOT;
         shell_exec("   php index.php {$url}  > /dev/null 2>&1 &");
     }

    public  static  function is_pid($pid){
        $pid_linxu = shell_exec("ps -ef | grep $pid  | grep -v grep | awk '{print $2}'");
        return $pid_linxu;
    }




}