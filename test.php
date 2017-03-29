<?php
/**
 * Created by PhpStorm.
 * User: wangtong
 * Date: 2017/3/29
 * Time: 下午2:42
 */

//json解析

$url = 'http://newsapi.sina.cn/?resource=nav&accessToken=&chwm=3023_0001&city=CHXX0120&connectionType=0&deviceId=d2c0a82972c990b5ecaf0c811d7db10f6d91a601&deviceModel=apple-iphone7&from=6061093012&idfa=00000000-0000-0000-0000-000000000000&idfv=B9029B24-D6DE-409A-82C8-51C60D9F1F54&imei=d2c0a82972c990b5ecaf0c811d7db10f6d91a601&location=22.616949%2C114.036346&osVersion=10.2.1&resolution=750x1334&sfaId=9f89c84a559f573636a47ff8daed0d33ff1f819b&token=f43f4af941c765d36789430da08ee3da1ffc2ce66c686b0302fa5626e858087e&ua=apple-iphone7__SinaNews__6.1__iphone__10.2.1&weiboSuid=&weiboUid=&wm=b207&rand=328&urlSign=f37623431b';
$ret = file_get_contents($url);
echo $ret;
