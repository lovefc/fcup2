### 项目介绍

一个轻巧的js类库,用于在网页端上传大文件,大图片,可以设置多个上传参数,提供了多种回调.
可以任意绑定id,自动生成上传表单,可以自定义文件头,其它参数,设置最大上传,最小上传,以及判断上传类型.
现在已经支持断点续传,并且有详细的操作案例

官网地址：http://www.fcup.top

github：https://github.com/lovefc/fcup2

gitee：https://gitee.com/lovefc/fcup2

![](/show.jpg)

### 安装教程

直接下载源码或者使用git克隆

备注:新版本的demo使用了sqlite数据库,注意php环境有没有sqlite扩展,另外上传目录要给读写权限

### 使用方法

```javascript
   // 上传案例
   let up = new fcup({

      id: "upid", // 绑定id

      url: "server/php_db/upload.php", // url地址
	  
	  checkurl: "server/php_db/check.php", // 检查上传url地址

      type: "jpg,png,jpeg,gif", // 限制上传类型，为空不限制

      shardsize: "0.005", // 每次分片大小，单位为M，默认1M

      minsize: '', // 最小文件上传M数，单位为M，默认为无

      maxsize: "2", // 上传文件最大M数，单位为M，默认200M
      
	  // headers: {"version": "fcup-v2.0"}, // 附加的文件头
	  
	  // apped_data: {}, //每次上传的附加数据
	  
      // 定义错误信息
      errormsg: {
         1000: "未找到上传id",
         1001: "类型不允许上传",
         1002: "上传文件过小",
         1003: "上传文件过大",
         1004: "上传请求超时"
      },
      
      // 错误提示
      error: (msg) => {
         alert(msg);
      },      

      // 初始化事件                
      start: () => {
         console.log('上传已准备就绪');
         Progress(0);
      },

      // 等待上传事件，可以用来loading
      beforeSend: () => {
         console.log('等待请求中');
      },

      // 上传进度事件
      progress: (num, other) => {
         Progress(num);
         console.log(num);
         console.log('上传进度' + num);
         console.log("上传类型" + other.type);
         console.log("已经上传" + other.current);
         console.log("剩余上传" + other.surplus);
         console.log("已用时间" + other.usetime);
         console.log("预计时间" + other.totaltime);
      },
	  
      // 检查地址回调,用于判断文件是否存在,类型,当前上传的片数等操作
      checksuccess: (res) => {
	  
         let data = res ? eval('(' + res + ')') : '';
		 
		 let status = data.status;
		 
         let url = data.url;
		 
		 let msg = data.message;
		 
		 // 错误提示
         if (status == 1 ) {
            alert(msg);
            return false;
         }
		 
		 // 已经上传
         if (status == 2) {
            Progress(100);
            $('#pic').attr("src", url);
            $('#pic').show();
			alert('图片已存在');
            return false;
         }
         
		// 如果提供了这个参数,那么将进行断点上传的准备
		if(data.file_index){
           // 起始上传的切片要从1开始
		   let file_index = data.file_index ? parseInt(data.file_index) : 1;
           // 设置上传切片的起始位置		   
		   up.setshard(file_index);
		}
		 
        // 如果接口没有错误，必须要返回true，才不会终止上传
         return true;
      },
	  
      // 上传成功回调，回调会根据切片循环，要终止上传循环，必须要return false，成功的情况下要始终返回true;
      success: (res) => {

         let data = res ? eval('(' + res + ')') : '';

         let url = data.url + "?" + Math.random();
		 
		 let file_index = data.file_index ? parseInt(data.file_index) : 1;

         if (data.status == 2) {
            $('#pic').attr("src", url);
            $('#pic').show();
            alert('上传完成');
         }

         // 如果接口没有错误，必须要返回true，才不会终止上传循环
         return true;
      }
   });	
```

### 前端参数详细

| 参数 |类型| 空 | 默认 | 备注 |
|----    |-------    |--- |---|------      | 
|id | string | 否 | 无 |     dom的id        | 
|url |string | 否 | 无  |   上传到服务器的url  |
|checkurl |string | 否 | 无  |   检查上传url地址  |
|type |string | 是  |  空 |  限制上传类型，多个用,号分割(不区分大小写),为空不限制  |
|shardsize    | int,float | 否   | 2   |     每次分片的大小,单位为M,因为要计算md5,所以如果条件允许,不要设定的太小     |
|minsize    | int,float | 是   | 空   |  上传文件的最小M数   |
|maxsize    | int,float | 是   | 空   |  上传文件的最大M数   |
|headers |object   |是   | 空  |  每次上传附带的文件头  |
|apped_data |object   |是   | 空  |  每次上传附带的其它参数,传递到后台  |
|timeout |int   |否   | 3000 |  ajax超时时间  |
|errormsg |object   |否   | object |  错误提示 | 
|start |function   |是   | fucntion |  实例化类后的开始事件  |
|beforeSend |function   |是   | fucntion |  等待上传事件  |
|progress |function   |是   | fucntion |  上传进度事件  |
|error |function   |是   | fucntion |  内部的错误提示函数  |
|checksuccess |function   |是   | fucntion |  检查地址回调,用于判断文件是否存在,类型,改变当前上传的片数等操作 |
|success |function   |是   | fucntion |  数据成功传递到后端的事件,这是一个循环事件 |

### 常用函数
| 函数名 | 说明 |
|----    |------      | 
|fcup.setshard(file_index)|    设置当前的分片数起始数,用于断点上传时改变       | 
|fcup.cancel()|取消上传事件  |
|fcup.startUpload()|开始上传事件  |

### 后端参数详情

|参数名|注释|
|----    |------  |
|file_data |分段的文件|
|file_name |文件名称|
|file_total |文件的总片数|
|file_index |当前片数|
|file_md5 |文件的md5|
|file_size |文件的总大小|
|file_chunksize |当前切片的文件大小|
|file_suffix |文件的后缀名|
- 备注：以post的方式传递到后端

### 更新日志

2018/1/8 : 添加了对于接口返回结果的回调，添加了对于上传表单id的指定

2018/1/10 : 添加了node.js的上传接口，基于express框架

2018/1/17 : 优化了分片异步处理,队列执行接口,修复细节

2018/5/02 : 添加了文件大小的判断,添加了对于文件md5的计算,添加了终止函数,传值到后台使用,优化细节部分

2019/5/21 : 分离了原来的进度动画，现在用户可以自定义自己的动画和按钮，分别提供了各种回调事件以便处理

2019/8/12 : 修复了获取md5值的bug，感谢Matty的提醒

2019/10/22: 修改了终止事件循环执行的bug

2020/01/05: 重新封装类库,优化性能,改掉了以前的bug 

2020/01/30: 优化了时间计算,添加了可自定义header头的功能

2020/02/01: 多实例化,可以在同一个页面添加多个上传功能

2020/04/16: 分离了文件判断和上传的操作,添加了断点续传功能
