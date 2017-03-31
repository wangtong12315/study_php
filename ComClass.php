<?php

/**
 * Created by PhpStorm.
 * User: wangtong
 * Date: 2017/3/31
 * Time: 下午2:05
 */
class ComClass
{
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
        $arr[code] = $code;
        $arr[msg] = $msg;
        $arr[data] = $data;
        return decodeUnicode(json_encode($arr));
    }



}