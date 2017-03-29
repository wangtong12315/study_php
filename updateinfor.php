<?php
/**
 * Created by PhpStorm.
 * User: wangtong
 * Date: 2017/3/29
 * Time: 下午1:57
 *///UPDATE `userinfor` SET `password`=111111 WHERE phonenumber=13652426051/

header('Content-type: text/html;charset=utf-8');
error_reporting(0);

$dbadress = 'localhost';
$dbroot = 'root';
$dbpassword = '';

if (empty($_POST)){
    echo retJson('202','输入参数为空','');
    exit();
}

$phonenumber = $_POST[phonenumber];
$newpassword = $_POST[newpassword];
$con  = mysqli_connect($dbadress,$dbroot,$dbpassword);
mysqli_query("set names 'utf8");
mysqli_set_charset($con, "utf8");
if (!$con){
    echo retJson('500','未能连接数据库','');
    die('连接数据库失败'.mysqli_error());
}
mysqli_select_db($con,'wangtong');
//查询用户是否存在
$searSql = "UPDATE `userinfor` SET `password`='$newpassword' WHERE phonenumber='$phonenumber'";
if (!mysqli_query($con,$searSql)){
    echo retJson('202','密码更新失败','');
    exit();
}
echo retJson('200','密码更新成功!','');

//返回结果的封装
function retJson($code,$msg,$data){
    $arr = array();
    $arr[code] = $code;
    $arr[msg] = $msg;
    $arr[data] = $data;
    return decodeUnicode(json_encode($arr));
}

function decodeUnicode($str)
{
    return preg_replace_callback('/\\\\u([0-9a-f]{4})/i',
        create_function(
            '$matches',
            'return mb_convert_encoding(pack("H*", $matches[1]), "UTF-8", "UCS-2BE");'
        ),
        $str);
}