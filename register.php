<?php
/**
 * Created by PhpStorm.
 * User: wangtong
 * Date: 2017/3/27
 * Time: 上午9:23
 */
require_once ('connectdb.php');
require_once ('ComClass.php');

$comuse = new ComClass();

if (empty($_POST)){
    echo $comuse->retJson('202','输入参数为空','');
    exit();
}

$nickname = $_POST[nickname];
$phonenumber = $_POST[phonenumber];
$password = $_POST[password];

$connect = new connectdb();
$connect->connect('localhost','root','','wangtong');

//查询用户是否存在
$searSql = "SELECT * FROM userinfor WHERE phonenumber = '$phonenumber'";
$searchR = mysqli_query($con,$searSql);

if (!mysqli_num_rows($searchR)){//查询不存在的时候
    //新增用户操作
    $timstep = mktime();
    $sql = "INSERT INTO `userinfor` (`userid`, `nickname`, `phonenumber`, `password`) VALUES ('$timstep', '$nickname', '$phonenumber', '$password')";
    if (!mysqli_query($con,$sql)){
        echo $comuse->retJson('500','数据库新增失败','');
        die('数据库插入失败'.mysqli_error());
    }
//注册返回结果
    echo $comuse->retJson('200','注册成功','');

}else{
    echo $comuse->retJson('202','用户已存在','');
    exit();
}

