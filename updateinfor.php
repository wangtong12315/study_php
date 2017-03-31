<?php
/**
 * Created by PhpStorm.
 * User: wangtong
 * Date: 2017/3/29
 * Time: 下午1:57
 *///UPDATE `userinfor` SET `password`=111111 WHERE phonenumber=13652426051/

require_once ('connectdb.php');
require_once ('ComClass.php');

$comuse = new ComClass();

if (empty($_POST)){
    echo $comuse->retJson('202','输入参数为空','');
    exit();
}

$phonenumber = $_POST[phonenumber];
$newpassword = $_POST[newpassword];

$connect = new connectdb();
$connect->connect('localhost','root','','wangtong');

//查询用户是否存在
$searSql = "UPDATE `userinfor` SET `password`='$newpassword' WHERE phonenumber='$phonenumber'";
if (!mysqli_query($con,$searSql)){
    echo $comuse->retJson('202','密码更新失败','');
    exit();
}
echo $comuse->retJson('200','密码更新成功!','');
