<?php

/**
 * 公共引用
 * author:lovefc
 */
 
// 设置跨域头,用于跨域访问
header('Access-Control-Allow-Origin:*');

header('Access-Control-Allow-Methods:PUT,POST,GET,DELETE,OPTIONS');

header('Access-Control-Allow-Headers:x-requested-with,content-type');

header('Content-Type:application/json; charset=utf-8');

// 屏蔽错误信息
error_reporting(0);

date_default_timezone_set('PRC'); // 时区

require_once(PATH . '/class/SQLite.php');

define('DBFILE', PATH . '/db/' . md5('files') . '.db'); // 数据库文件名

define('UP_PATH', dirname(PATH) . '/upload/'); //上传路径目录


/**
 * 获取主域名
 *
 * @return string
 */
function getHostDomain()
{
    return getHttpType() . $_SERVER['SERVER_NAME'];
}
 
/**
 * 获取 HTTPS协议类型
 *
 * @return string
 */
function getHttpType()
{
    return $type = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https')) ? 'https://' : 'http://';
}

// 当前域名
$now_url = getHostDomain().dirname(dirname($_SERVER['PHP_SELF'])).'/upload/';

// 定义当前访问域名
define('UP_URL', $now_url);

$DB = new SQLite(DBFILE);

$sql = "SELECT COUNT(*) as num FROM sqlite_master where type='table' and name='files'"; //检测表是否存在

$count = $DB->fetch($sql);

// 不存在就创建files表
if ($count['num'] == 0) {
	$sql  = 'CREATE TABLE files(';
	$sql .= 'id integer primary key autoincrement,'; // 主键id
	$sql .= 'path varchar(255) NOT NULL,'; // 路径名称
	$sql .= 'type varchar(255) NULL,'; // 文件类型
	$sql .= 'file_md5 varchar(255) NULL,'; // 文件md5
	$sql .= 'file_size int(10) NULL,'; // 文件大小	
	$sql .= 'file_total int(5) NULL,'; // 文件总片数
	$sql .= 'file_index int(5) NULL,'; // 文件当前片数	
	$sql .= 'cdat varchar(20) NOT NULL'; // 当前时间戳
	$sql .= ')';
    $DB->query($sql);
}

// 输出json信息
function jsonMsg($status,$message,$url='',$index=0){
   $arr['status'] = $status;
   $arr['message'] = $message;
   $arr['url'] = $url;
   $arr['file_index'] = $index;
   echo json_encode($arr);
   die();
}
