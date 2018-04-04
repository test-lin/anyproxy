<?php
// 接收历史消息的json并解析后存入数据库
require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../data/config.php';

use Testlin\Db\Db;

$db_type = $config['db_type'];
$db = new Db($db_type, $config[$db_type]);

$str = trim($_POST['str']);
$url = $_POST['url'];

//先针对url参数进行操作
parse_str(parse_url(htmlspecialchars_decode(urldecode($url)), PHP_URL_QUERY), $query); //解析url地址
$biz = $query['__biz']; //得到公众号的biz
//接下来进行以下操作
//从数据库中查询biz是否已经存在，如果不存在则插入，这代表着我们新添加了一个采集目标公众号。

// 公众号
$sql = "select count(*) from weixin where biz='{$biz}'";
$biz_num = $db->getOne($sql);
if ($biz_num == 0) {
    $state = $db->insert('weixin', ['biz' => $biz]);
    if (!$state) {
        echo "add weixin biz {$biz} error\n";
    }
}

// 文章列表
$json = json_decode($str, true); //首先进行json_decode
if (!$json) {
    $str = urldecode($str);
    $json = json_decode(htmlspecialchars_decode($str), true); //如果不成功，就增加一步htmlspecialchars_decode
}

if ($json['list']) {
    foreach ($json['list'] as $k => $v) {
        $type = $v['comm_msg_info']['type'];
        //type=49代表是图文消息
        if ($type == 49) {
            $content_url = str_replace("\\", "", htmlspecialchars_decode($v['app_msg_ext_info']['content_url'])); //获得图文消息的链接地址
            $is_multi = $v['app_msg_ext_info']['is_multi']; //是否是多图文消息
            $datetime = $v['comm_msg_info']['datetime']; //图文消息发送时间
            //在这里将图文消息链接地址插入到采集队列库中（队列库将在后文介绍，主要目的是建立一个批量采集队列，另一个程序将根据队列安排下一个采集的公众号或者文章内容）
            //在这里根据$content_url从数据库中判断一下是否重复
            $sql = "select count(*) from post where content_url='{$content_url}'";
            if ($db->getOne($sql) == 0) {
                // 文章内容收集队列
                $db->insert('tmplist', ['content_url' => $content_url]);

                $fileid = $v['app_msg_ext_info']['fileid']; //一个微信给的id
                $title = $v['app_msg_ext_info']['title']; //文章标题
                $title_encode = urlencode(str_replace("&nbsp;", "", $title)); //建议将标题进行编码，这样就可以存储emoji特殊符号了
                $digest = $v['app_msg_ext_info']['digest']; //文章摘要
                $source_url = str_replace("\\", "", htmlspecialchars_decode($v['app_msg_ext_info']['source_url'])); //阅读原文的链接
                $cover = str_replace("\\", "", htmlspecialchars_decode($v['app_msg_ext_info']['cover'])); //封面图片

                $is_top = 1; //标记一下是头条内容
                // 存入文章标题表
                $add = [
                    'biz' => $biz,
                    'field_id' => $fileid,
                    'title' => $title,
                    'title_encode' => $title_encode,
                    'digest' => $digest,
                    'content_url' => $content_url,
                    'source_url' => $source_url,
                    'cover' => $cover,
                    'is_multi' => $is_multi,
                    'is_top' => $is_top,
                    'datetime' => time()
                ];
                $lastId = $db->insert('post', $add);
                if ($lastId) {
                    echo "头条标题：" . $title . $lastId . "\n"; //这个echo可以显示在anyproxy的终端里
                } else {
                    $error_log = "入库失败1：".$db->getSql()."\n";
                    file_put_contents('error.log', $error_log, FILE_APPEND | LOCK_EX);
                    echo "入库失败：" . $title . "\n"; //这个echo可以显示在anyproxy的终端里
                }
            }
            if ($is_multi == 1) {
                //如果是多图文消息
                foreach ($v['app_msg_ext_info']['multi_app_msg_item_list'] as $kk => $vv) { //循环后面的图文消息
                    $content_url = str_replace("\\", "", htmlspecialchars_decode($vv['content_url'])); //图文消息链接地址
                    //这里再次根据$content_url判断一下数据库中是否重复以免出错
                    $sql = "select count(*) from post where content_url='{$content_url}'";
                    if ($db->getOne($sql) <= 0) {
                        //在这里将图文消息链接地址插入到采集队列库中（队列库将在后文介绍，主要目的是建立一个批量采集队列，另一个程序将根据队列安排下一个采集的公众号或者文章内容）
                        $title = $vv['title']; //文章标题
                        $fileid = $vv['fileid']; //一个微信给的id
                        $title_encode = urlencode(str_replace("&nbsp;", "", $title)); //建议将标题进行编码，这样就可以存储emoji特殊符号了
                        $digest = htmlspecialchars($vv['digest']); //文章摘要
                        $source_url = str_replace("\\", "", htmlspecialchars_decode($vv['source_url'])); //阅读原文的链接
                        //$cover = getCover(str_replace("\\","",htmlspecialchars_decode($vv['cover'])));
                        $cover = str_replace("\\", "", htmlspecialchars_decode($vv['cover'])); //封面图片
                        //现在存入数据库
                        $add = [
                            'biz' => $biz,
                            'field_id' => $fileid,
                            'title' => $title,
                            'title_encode' => $title_encode,
                            'digest' => $digest,
                            'content_url' => $content_url,
                            'source_url' => $source_url,
                            'cover' => $cover,
                            'is_multi' => 1,
                            'is_top' => 0,
                            'datetime' => time()
                        ];
                        $lastId = $db->insert('post', $add);
                        if ($lastId) {
                            echo "头条标题：" . $title . $lastId . "\n"; //这个echo可以显示在anyproxy的终端里
                        } else {
                            $error_log = "入库失败2：".json_encode($add, JSON_UNESCAPED_UNICODE)."\n";
                            file_put_contents('error.log', $error_log, FILE_APPEND | LOCK_EX);
                            echo "入库失败：" . $title . "\n"; //这个echo可以显示在anyproxy的终端里
                        }
                    }

                }
            }
        }
    }
} else {
    file_put_contents('getMsgjson.error', json_encode($str));
}
