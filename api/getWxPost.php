<?php

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../data/config.php';

use Testlin\Db\Db;

$db_type = $config['db_type'];
$db = new Db($db_type, $config[$db_type]);

//getWxPost.php 当前页面为公众号文章页面时，读取这个程序
//首先删除采集队列表中load=1的行
$db->delete('tmplist', "`load`=1");

//然后从队列表中按照“order by id asc”选择多行(注意这一行和上面的程序不一样)
$sql = "select * from tmplist where `load`=0 order by id asc";
$tmplist = $db->getAll($sql);
if (!empty($tmplist) && count($tmplist) > 1) {
//(注意这一行和上面的程序不一样)
    //取得第0行的content_url字段
    $info = $tmplist[0];
    $url = $info['content_url'];
    //将第0行的load字段update为1
    $db->update('tmplist', ['`load`' => 1], "id='{$info['id']}'");
} else {
    //队列表还剩下最后一条时，就从存储公众号biz的表中取得一个biz，这里我在公众号表中设置了一个采集时间的time字段，按照正序排列之后，就得到时间戳最小的一个公众号记录，并取得它的biz
    $sql = "SELECT id,biz FROM weixin ORDER BY collect ASC LIMIT 1";
    $info = $db->getRow($sql);
    $biz = $info['biz'];
    // $url = "http://mp.weixin.qq.com/mp/getmasssendmsg?__biz=" . $biz . "#wechat_webview_type=1&wechat_redirect"; //拼接公众号历史消息url地址（第一种页面形式）
    $url = "https://mp.weixin.qq.com/mp/profile_ext?action=home&__biz=" . $biz . "&scene=124#wechat_redirect"; //拼接公众号历史消息url地址（第二种页面形式）
    //更新刚才提到的公众号表中的采集时间time字段为当前时间戳。
    $db->update('weixin', ['collect' => time()], "id='{$info['id']}'");
}
echo "setTimeout(function(){window.location.href='" . $url . "';},2000);"; //将下一个将要跳转的$url变成js脚本，由anyproxy注入到微信页面中。
