<?php

/**
 * 上传文件
 * author:lovefc
  * time:2020/04/16 14:00
 */

set_time_limit(600);

define('PATH', dirname(__FILE__));

require(PATH . '/public.php');

// 新增数据
function add($data)
{
  global $DB;
  list($path, $type, $size, $md5, $total, $index, $time) = $data;
  $sql = "INSERT INTO files(path,type,file_md5,file_size,file_total,file_index,cdat)VALUES('{$path}','{$type}','{$md5}','{$size}','{$total}','{$index}','{$time}')";
  return $DB->query($sql);
}

// 更新数据
function update($md5, $index)
{
  global $DB;
  $sql = "UPDATE files SET file_index={$index} WHERE file_md5='{$md5}'"; // 更新数据库中的片数
  return $DB->query($sql);
}

$file = isset($_FILES['file_data']) ? $_FILES['file_data'] : null; //分段的文件

$name = isset($_POST['file_name']) ? $_POST['file_name'] : null; //要保存的文件名

$total = isset($_POST['file_total']) ? $_POST['file_total'] : 0; //总片数

$index = isset($_POST['file_index']) ? $_POST['file_index'] : 0; //当前片数

$md5   = isset($_POST['file_md5']) ? $_POST['file_md5'] : 0; //文件的md5值

$size  = isset($_POST['file_size']) ?  $_POST['file_size'] : null; //文件大小

$chunksize  = isset($_POST['file_chunksize']) ?  $_POST['file_chunksize'] : null; //当前切片的文件大小

$suffix  = isset($_POST['file_suffix']) ?  $_POST['file_suffix'] : null; //当前上传的文件后缀

// 这里判断有没有上传的文件流
if (!$md5 || $file['error'] != 0) {
  jsonMsg(0, '没有上传文件');
}


// 在实际使用中，用md5来给文件命名，这样可以减少冲突
// 简单的判断文件类型s
$info = pathinfo($name);
// 取得文件后缀

$ext = isset($info['extension']) ? $info['extension'] : '';

$file_name = $md5 . '.' . $ext;

$newfile = UP_PATH . $file_name;

// 文件可访问的地址
$url =  UP_URL . $file_name;


// 定义要插入数据库的数据
$time = time();
$datas = array(
    $file_name,
	$ext,
	$size,
	$md5,
	$total,
	$index,
	$time
);

// 检查文件是否存在在数据库中
$sql = "select * from files where file_md5 = '{$md5}' limit 1";
$re = $DB->fetch($sql);
if ($re) {
  // 文件可访问的地址
  $id = $re['id'];
  $file_name = $re['path'];
  $url =  UP_URL . $file_name;
  $path = UP_PATH . $file_name;
  $file_size = $re['file_size'];
  $file_index = $re['file_index'];
  $file_total = $re['file_tatal'];
  // 片数对比,如果一样,说明已经上传过了
  if ($file_index == $file_total) {
    jsonMsg(2, '已经上传过了', $url);
  } else {
    $content = file_get_contents($file['tmp_name']);
    if (!file_put_contents($path, $content, FILE_APPEND)) {
      jsonMsg(0, '无法写入文件');
    }
	update($md5, $index);
    // 片数相等，等于完成了
    if ($index == $total) {
      jsonMsg(2, '上传完成', $url, $index);
    }
    jsonMsg(1, '正在上传', '', $index);
  }
}else{
	// 开始写入数据库
	add($datas);
}

// 如果文件不存在，就创建
if (!file_exists($newfile)) {
  if (!move_uploaded_file($file['tmp_name'], $newfile)) {
    jsonMsg(0, '无法移动文件');
  }
  add($datas); // 添加到数据库
  // 片数相等，等于完成了
  if ($index == $total) {
    jsonMsg(2, '上传完成', $url, $index);
  }
  jsonMsg(1, '正在上传', '', $index);
}
// 如果当前片数小于等于总片数,就在文件后继续添加
if ($index <= $total) {
  $content = file_get_contents($file['tmp_name']);
  if (!file_put_contents($newfile, $content, FILE_APPEND)) {
    jsonMsg(0, '无法写入文件');
  }
  update($md5, $index); // 更新片数
  // 片数相等，等于完成了
  if ($index == $total) {
    jsonMsg(2, '上传完成', $url, $index);
  }
  jsonMsg(1, '正在上传', '', $index);
}
