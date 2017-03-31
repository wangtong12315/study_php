<?php
/**
 * Created by PhpStorm.
 * User: wangtong
 * Date: 2017/3/29
 * Time: 下午2:42
 */

//json解析
require_once ("Person.php");
$pes = new Person();

$name1 = $pes->name;
$sex1 = $pes->sex;
$height1 = $pes->height;

echo $name1 , " " , $sex1 , " " , $height1;
echo '<br>';

echo  $pes->showWelcom();