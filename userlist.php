<?php
/**
 * Created by PhpStorm.
 * User: wangtong
 * Date: 2017/3/29
 * Time: 下午1:36
 */

require_once ('connectdb.php');
require_once ('ComClass.php');

$connect = new connectdb();
$com = new ComClass();

$connect->connect('localhost','root','','wangtong');

//查询用户是否存在
$searSql = "SELECT * FROM userinfor";
$searchR = mysqli_query($con,$searSql);

$dataArray = array();
while($row = mysqli_fetch_array($searchR)) {
    $oneData = array();
    $oneData['userid'] = $row['userid'];
    $oneData['nickname'] = $row['nickname'];
    $oneData['phonenumber'] = $row['phonenumber'];
    $oneData['password'] = $row['password'];
    $dataArray[] = $oneData;
}

print_r($com->retJson('200','查询成功',$dataArray));
