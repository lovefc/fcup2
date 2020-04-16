<?php
/***
 * //创建实例
 * $DB=new SQLite('ceshi.db'); //这个数据库文件名字任意
 * //创建数据库表。
 * $DB->query("create table test(id integer primary key,title varchar(50))");
 * //接下来添加数据
 * $DB->query("insert into test(title) values('泡菜')");
 * //读取数据
 * print_r($DB->getlist('select * from test order by id desc'));
 * //更新数据
 * $DB->query('update test set title = "三大" where id = 9');
 ***/

class SQLite
{
    function __construct($file)
    {
        try {
            $this->connection = new PDO('sqlite:' . $file);
        } catch (PDOException $e) {
            try {
                $this->connection = new PDO('sqlite2:' . $file);
            } catch (PDOException $e) {
                exit('error!');
            }
        }
    }

    function __destruct()
    {
        $this->connection = null;
    }

    // 直接运行SQL，可用于更新、删除数据
    function query($sql) 
    {
        return $this->connection->query($sql);
    }

     //取得记录列表
    function getlist($sql)
    {
        $recordlist = array();
        foreach ($this->query($sql) as $rstmp) {
            $recordlist[] = $rstmp;
        }
        return $recordlist;
    }

    // 获取一条数据
    function fetch($sql)
    {
        return $this->query($sql)->fetch(PDO::FETCH_ASSOC);
    }

    // 读全部数据
    function fetchall($sql)
    {
        return $this->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    // 获取最后id
    function lastid()
    {
        return $this->connection->lastInsertId();
    }
}