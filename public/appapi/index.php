<?php
/**
 * Appapi 统一入口
 */
header("Access-Control-Allow-Origin:*");
header("Access-Control-Allow-Methods:GET, POST");
header("Access-Control-Allow-Headers:DNT,X-Mx-ReqToken,Keep-Alive,User-Agent,X-Requested-With,If-Modified-Since,Cache-Control,Content-Type, Accept-Language, Origin, Accept-Encoding,A-Token");
require_once dirname(__FILE__) . '/../../PhalApi/public/init.php';
//装载你的接口
DI()->loader->addDirs('Appapi');
DI()->loader->addDirs('Pay');
/** ---------------- 响应接口请求 ---------------- **/

define('NICENAME', __DIR__ . '/../nicename/nicename.txt');



$api = new PhalApi();
$rs = $api->response();
$rs->output();

