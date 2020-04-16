<?php

/**
 * 检查上传文件
 * author:lovefc
 */


// 设置跨域头
header('Access-Control-Allow-Origin:*');

header('Access-Control-Allow-Methods:PUT,POST,GET,DELETE,OPTIONS');

header('Access-Control-Allow-Headers:x-requested-with,content-type');

header('Content-Type:application/json; charset=utf-8');


$name  = isset($_POST['file_name']) ? $_POST['file_name']:null; // 文件名

$md5   = isset($_POST['file_md5']) ? $_POST['file_md5'] :''; //文件的md5值

$size   = isset($_POST['file_size']) ? $_POST['file_size'] :''; //文件大小

// 输出json信息
function jsonMsg($status,$message,$url='',$index=1){
   $arr['status'] = $status;
   $arr['message'] = $message;
   $arr['url'] = $url;
   $arr['file_index'] = $index;
   echo json_encode($arr);
   die();
}

//jsonMsg(0,'','',201);  


if(!$md5){
	jsonMsg(1,'没有文件');
}

// 简单的判断文件类型s
$info = pathinfo($name);

// 取得文件后缀
$ext = isset($info['extension'])?$info['extension']:'';

/* 判断文件类型 */
$imgarr = array('jpeg','jpg','png','gif');
if(!in_array($ext,$imgarr)){
    jsonMsg(1,'文件类型出错');
}

// 在实际使用中，用md5来给文件命名，这样可以减少冲突

$file_name = $md5.'.'.$ext;

$newfile = '../upload/'.$file_name;

$log_file = '../upload/'.$md5.'.txt';

// 文件可访问的地址
$url = './server/upload/'.$file_name;

/** 判断是否重复上传 **/

// 清除文件状态
clearstatcache($newfile);

// 文件大小一样的，说明已经上传过了
if(is_file($newfile) && ($size == filesize($newfile))){
   jsonMsg(2,'已经上传过了',$url);          
}
if(is_file($log_file)){
   // 读取当前片数的时候要向前偏移1个
   $index = file_get_contents($log_file);
   $index = $index + 1;
}else{
   $index = 1;
}
jsonMsg(0,'','',$index);   
