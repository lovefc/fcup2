/*
 * @Author       : lovefc
 * @Date         : 2021-03-11 15:34:02
 * @LastEditTime : 2021-03-18 15:19:31
 */

const http = require('http');

// 使用fc-body扩展 自己安装一下 npm install fc-body
// 关于使用,请移步到: http://gitee.com/lovefc/fc-body
const fc_body = require('fc-body');

const fs = require("fs");
const path = require("path");

const server = http.createServer(async (req, res) => {

  //设置允许跨域的域名，*代表允许任意域名跨域
  res.setHeader("Access-Control-Allow-Origin", "*");
  //跨域允许的header类型
  res.setHeader("Access-Control-Allow-Headers", "Content-type,Content-Length,Authorization,Accept,X-Requested-Width");
  //跨域允许的请求方式
  res.setHeader("Access-Control-Allow-Methods", "PUT,POST,GET,DELETE,OPTIONS");
  //设置响应头信息
  res.setHeader("X-Powered-By", ' 3.2.1');

  // 上传
  if (req.url === '/app/upload') {
    res.writeHead(200, {
      'content-type': 'application/json'
    });
    let body = new fc_body({
      maxSize: 5, // 最大上传大小
      isAutoSaveFile: false, // 不自动保存,自己来处理
    });
    let post = '';
    let error = '';
    // 注意,在{}里面的都是局部变量
    try {
      post = await body.getBody(req);
      console.log(post)
    } catch (e) {
      console.log(e);
      res.end('{"status":0,"message":"' + e.message +'"}');
      return;
    }

    let dir = path.resolve(__dirname, '..'); // 获取上级目录
    let name = post['file_name'] ? post['file_name'] : ''; // 文件名称
    let file_index = post['file_index'] ? post['file_index'] : 0; // 当前片数 
    let file_total = post['file_total'] ? post['file_total'] : 0; // 总片数 	
    let data = null;
    // 检查有没有数据
    if ("file_data" in post) {
      data = post['file_data']['data'];
    }
    if (!data) {
      res.end('{"status":0,"message":"没有数据"}');
      return;
    }
    let file_dir = dir + "/upload/" + name;

    // 异步追加,你也可以用同步来写
    fs.appendFile(file_dir, data, (error) => {
      if (error) return console.log("追加文件失败" + error.message);
      console.log("追加成功");
    });

    let url = './server/upload/'.name;
    // 片数相等,就返回成功
    if (file_index === file_total) {
      res.end('{"status":2,"message":"上传完成","url":"' + url + '","file_index":"' + file_index + '"}');
      return;
    }
    res.end('{"status":1,"message":"正在上传","url":"' + url + '","file_index":"' + file_index + '"}');
    return;
  }
})

server.listen(3001, () => {
  console.log('Server listening on http://localhost:3001/ ...');
});