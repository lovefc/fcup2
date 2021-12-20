### demo使用

请使用nginx或者apache把git下来的项目设成主目录

php访问:xxxx/test/index.html

node访问:xxxx/test/node.html (node需要去server/node/目录,运行api.js)


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

- 以post的方式传递到后端
- 后期将会继续添加多个语言的案例代码
- 您也可可以帮助我完善您使用语言的案例代码.

