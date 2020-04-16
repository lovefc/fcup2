<?php

/**
 * 接受处理上传文件
 * author:lovefc
 */


// 设置跨域头
header('Access-Control-Allow-Origin:*');

header('Access-Control-Allow-Methods:PUT,POST,GET,DELETE,OPTIONS');

header('Access-Control-Allow-Headers:x-requested-with,content-type');

header('Content-Type:application/json; charset=utf-8');

$file = isset($_FILES['file_data']) ? $_FILES['file_data']:null; //分段的文件

$name = isset($_POST['file_name']) ? $_POST['file_name']:null; //要保存的文件名

$total = isset($_POST['file_total']) ? $_POST['file_total']:0; //总片数

$index = isset($_POST['file_index']) ? $_POST['file_index']:0; //当前片数

$md5   = isset($_POST['file_md5']) ? $_POST['file_md5'] : 0; //文件的md5值

$size  = isset($_POST['file_size']) ?  $_POST['file_size'] : null; //文件大小

$chunksize  = isset($_POST['file_chunksize']) ?  $_POST['file_chunksize'] : null; //当前切片的文件大小

$suffix  = isset($_POST['file_suffix']) ?  $_POST['file_suffix'] : null; //当前上传的文件后缀

 
//echo '总片数：'.$total.'当前片数：'.$index;

// 输出json信息
function jsonMsg($status,$message,$url='',$index=0){
   $arr['status'] = $status;
   $arr['message'] = $message;
   $arr['url'] = $url;
   $arr['file_index'] = $index;
   echo json_encode($arr);
   die();
}

// 在实际使用中，用md5来给文件命名，这样可以减少冲突
// 简单的判断文件类型s
$info = pathinfo($name);

// 取得文件后缀

$ext = isset($info['extension'])?$info['extension']:'';

$file_name = $md5.'.'.$ext;

$newfile = '../upload/'.$file_name;

$log_file = '../upload/'.$md5.'.txt';

// 文件可访问的地址
$url = './server/upload/'.$file_name;

// 这里判断有没有上传的文件流
if ($file['error'] == 0) {
	file_put_contents($log_file,$index);	
    // 如果文件不存在，就创建
    if (!file_exists($newfile)) {
        if (!move_uploaded_file($file['tmp_name'], $newfile)) {
            jsonMsg(0,'无法移动文件');
        }
        // 片数相等，等于完成了
        if($index == $total ){  
          jsonMsg(2,'上传完成',$url,$index);
        }        
        jsonMsg(1,'正在上传','',$index);
    }     
    // 如果当前片数小于等于总片数,就在文件后继续添加
    if($index <= $total){
        $content = file_get_contents($file['tmp_name']);
        if (!file_put_contents($newfile, $content, FILE_APPEND)) {
          jsonMsg(0,'无法写入文件');
        }
        // 片数相等，等于完成了
        if($index == $total ){  
          jsonMsg(2,'上传完成',$url,$index);
        }
        jsonMsg(1,'正在上传','',$index);
    }   
} else {
    jsonMsg(0,'没有上传文件');
}
 