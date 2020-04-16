<?php
/**
 * 检查文件
 * author:lovefc
 * time:2020/04/16 13:48
 */

set_time_limit(600);  

define('PATH', dirname(__FILE__));

require(PATH . '/public.php');

$name  = isset($_POST['file_name']) ? $_POST['file_name']:null; // 文件名

$md5   = isset($_POST['file_md5']) ? $_POST['file_md5'] :''; //文件的md5值

$size   = isset($_POST['file_size']) ? $_POST['file_size'] :''; //文件大小


if(!$md5){
	jsonMsg(1,'没有文件');
}

// 检查文件是否存在在数据库中
$sql = "select * from files where file_md5 = '{$md5}' limit 1";
$re = $DB->fetch($sql);
if($re){
   // 文件可访问的地址
   $path = $re['path'];
   $file_size = $re['file_size'];
   $file_index = $re['file_index'];
   $file_total = $re['file_total'];
   // 片数对比,如果一样,说明已经上传过了
   if($file_index == $file_total){
       $url =  UP_URL.$path;
	   jsonMsg(2,'已经上传过了',$url);
   }else{
	   // 片数不对等,那么继续上传
	   jsonMsg(0,'','',$file_index+1);  
   }
   
}


/** 也可以用文件大小来对比,缺点是每次来进行文件大小计算,效率不高

// 清除文件状态
clearstatcache($newfile);
// 文件大小一样的，说明已经上传过了
if(is_file($newfile) && ($size == filesize($newfile))){
   jsonMsg(2,'已经上传过了',$url);          
}
**/

// 简单的判断文件类型s
$info = pathinfo($name);

// 取得文件后缀
$ext = isset($info['extension'])?$info['extension']:'';

/* 判断文件类型 */
$imgarr = array('jpeg','jpg','png','gif');
if(!in_array($ext,$imgarr)){
    jsonMsg(1,'文件类型出错');
}

jsonMsg(0,'','',1);   
