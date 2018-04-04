<?php

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../data/config.php';

use Testlin\Db\Db;

$db_type = $config['db_type'];
$db = new Db($db_type, $config[$db_type]);

// 获取文章阅读量和点赞量的程序

$str = $_POST['str'];
$url = $_POST['url']; //先获取到两个POST变量
//先针对url参数进行操作
parse_str(parse_url(htmlspecialchars_decode(urldecode($url)), PHP_URL_QUERY), $query); //解析url地址
$biz = $query['__biz']; //得到公众号的biz
$sn = $query['sn'];
//再解析str变量
$json = json_decode($str, true); //进行json_decode

//根据biz和sn找到对应的文章
$sql = "select count(*) from `post` where `biz`='".$biz."' and `content_url` like '%".$sn."%'";
$num = $db->getOne($sql);

if ($num) {
    $read_num = $json['appmsgstat']['read_num']; //阅读量
    $like_num = $json['appmsgstat']['like_num']; //点赞量
    //在这里同样根据sn在采集队列表中删除对应的文章，代表这篇文章可以移出采集队列了
    $db->delete('tmplist', "content_url like '%{$sn}%'");

    $data = [
        'readNum' => $read_num,
        'likeNum' => $like_num
    ];
    $db->update('post', $data, "`biz`='".$biz."' and `content_url` like '%".$sn."%'");

    //然后将阅读量和点赞量更新到文章表中。
    $msg = "阅读量和点赞量更新\n";
    exit($msg); //可以显示在anyproxy的终端里
}

