<?php
/**
 * Created by PhpStorm.
 * User: wangtong
 * Date: 2017/3/29
 * Time: 下午1:36
 */

header('Content-type: text/html;charset=utf-8');
error_reporting(0);

$dbadress = 'localhost';
$dbroot = 'root';
$dbpassword = '';

$con  = mysqli_connect($dbadress,$dbroot,$dbpassword);
mysqli_query("set names 'utf8");
mysqli_set_charset($con, "utf8");
if (!$con){
    echo retJson('500','未能连接数据库','');
    die('连接数据库失败'.mysqli_error());
}
mysqli_select_db($con,'wangtong');
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

echo retJson('200','查询成功',$dataArray);


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