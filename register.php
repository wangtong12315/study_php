<?php
/**
 * Created by PhpStorm.
 * User: wangtong
 * Date: 2017/3/27
 * Time: 上午9:23
 */
header('Content-type: text/html;charset=utf-8');
error_reporting(0);

$dbadress = 'localhost';
$dbroot = 'root';
$dbpassword = '';

if (empty($_POST)){
    echo retJson('202','输入参数为空','');
    exit();
}

$nickname = $_POST[nickname];
$phonenumber = $_POST[phonenumber];
$password = $_POST[password];

$con  = mysqli_connect($dbadress,$dbroot,$dbpassword);
mysqli_query("set names 'utf8");
mysqli_set_charset($con, "utf8");
if (!$con){
    echo retJson('500','未能连接数据库','');
    die('连接数据库失败'.mysqli_error());
}
mysqli_select_db($con,'wangtong');

//查询用户是否存在
$searSql = "SELECT * FROM userinfor WHERE phonenumber = '$phonenumber'";
$searchR = mysqli_query($con,$searSql);

if (!mysqli_num_rows($searchR)){//查询不存在的时候
    //新增用户操作
    $timstep = mktime();
    $sql = "INSERT INTO `userinfor` (`userid`, `nickname`, `phonenumber`, `password`) VALUES ('$timstep', '$nickname', '$phonenumber', '$password')";
    if (!mysqli_query($con,$sql)){
        echo retJson('500','数据库新增失败','');
        die('数据库插入失败'.mysqli_error());
    }
//注册返回结果
    echo retJson('200','注册成功','');

}else{
    echo retJson('202','用户已存在','');
    exit();
}


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