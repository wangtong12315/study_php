<?php
/**
 * Created by PhpStorm.
 * User: wangtong
 * Date: 2017/3/29
 * Time: 下午1:20
 */
require_once ('connectdb.php');
require_once ('ComClass.php');

$comclass = new ComClass();

if (empty($_POST)){
    echo $comclass->retJson('202','输入参数为空','');
    exit();
}

$phonenumber = $_POST[phonenumber];
$password = $_POST[password];

$connectd = new connectdb();
$connectd->connect('localhost','root','','wangtong');

//查询用户是否存在
$searSql = "SELECT * FROM userinfor WHERE phonenumber = '$phonenumber'";
$searchR = mysqli_query($con,$searSql);

if (!mysqli_num_rows($searchR)){//查询不存在的时候
    $comclass->retJson('202','用户不存在','');
    exit();
}else{
    while($row = mysqli_fetch_array($searchR)) {
        if ($row['phonenumber'] == $phonenumber){
            if ($row['password'] == $password){
                //需要返回用户信息
                $inforArray = array();
                $inforArray['phonenumber'] = $row['phonenumber'];
                $inforArray['nickname'] = $row['nickname'];
                $inforArray['userid'] = $row['userid'];
                echo $comclass->retJson('200','登录成功',$inforArray);
                exit();
            }else{
                echo $comclass->retJson('202','密码错误','');
                exit();
            }
        }
    }
}


