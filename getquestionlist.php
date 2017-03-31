<?php
/**
 * Created by PhpStorm.
 * User: wangtong
 * Date: 2017/3/31
 * Time: 下午5:33
 */

$con  = mysqli_connect('localhost','root','');
mysqli_query($con,"set names 'utf8");
mysqli_set_charset($con, "utf8");
if (!$con){
    echo retJson('500','未能连接数据库','');
    die('连接数据库失败'.mysqli_error());
}

mysqli_select_db($con,'wangtong');

$searSql = "SELECT * FROM question";
$searchR = mysqli_query($con,$searSql);

$resultArray = array();
while($row = mysqli_fetch_array($searchR)) {
    $oneArray = array();
    $oneArray['fID'] = $row['fID'];

    $questionId = $row['fID'];

    $searSql1 = "SELECT * FROM options WHERE fQuestionID = $questionId";
    $searchR1 = mysqli_query($con,$searSql1);
    $questionobj = array();
    while ($row1 = mysqli_fetch_array($searchR1)){
        $oneArray1 = array();
        $oneArray1['fID'] = $row1['fID'];
        $oneArray1['fQuestionID'] = $row1['fQuestionID'];
        $oneArray1['fName'] = $row1['fName'];
        $questionobj[] = $oneArray1;
    }

    $oneArray['question'] = $questionobj;

    $oneArray['fBatch'] = $row['fBatch'];
    $oneArray['fType'] = $row['fType'];
    $resultArray[] = $oneArray;
}

echo retJson('200','success',$resultArray);

//UTF8转Uincode编码
function decodeUnicode($str)
{
    return preg_replace_callback('/\\\\u([0-9a-f]{4})/i',
        create_function(
            '$matches',
            'return mb_convert_encoding(pack("H*", $matches[1]), "UTF-8", "UCS-2BE");'
        ),
        $str);
}

//返回结果的封装
function retJson($code,$msg,$data){
    $arr = array();
    $arr['code'] = $code;
    $arr['msg'] = $msg;
    $arr['data'] = $data;
    return decodeUnicode(json_encode($arr));
}
