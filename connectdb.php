<?php

/**
 * Created by PhpStorm.
 * User: wangtong
 * Date: 2017/3/31
 * Time: 下午1:41
 */
class connectdb
{
    function connect($dbadress,$rootnumber,$rootpassword,$dbname){
        $con  = mysqli_connect($dbadress,$rootnumber,$rootpassword);
        mysqli_query($con,"set names 'utf8");
        mysqli_set_charset($con, "utf8");
        if (!$con){
            echo retJson('500','未能连接数据库','');
            die('连接数据库失败'.mysqli_error());
        }
        mysqli_select_db($con,$dbname);
    }
}