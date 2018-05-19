<?php
require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../data/config.php';

use Testlin\Db\Db;

class Tool
{
    protected $db;

    public function __construct($db)
    {
        if (is_null($this->db)) {
            $this->db = $db;
        }
    }

    // 接收历史消息的json并解析后存入数据库
    public function getMsgJson()
    {
        $contnet = trim($_POST['str']);
        $nickname = $_POST['nickname'] ?? '';
        $nickname = trim($nickname);
        $logo = $_POST['logo'] ?? '';
        $logo = trim($logo);

        //先针对url参数进行操作
        parse_str(parse_url(htmlspecialchars_decode(urldecode($_POST['url'])), PHP_URL_QUERY), $query); //解析url地址
        $biz = $query['__biz'] ?? ''; //得到公众号的biz
        //接下来进行以下操作
        $user_id = $this->checkBiz($biz, $nickname, $logo);

        // 文章列表
        $json = json_decode($contnet, true); //首先进行json_decode
        if (!$json) {
            $contnet = urldecode($contnet);
            //如果不成功，就增加一步htmlspecialchars_decode
            $json = json_decode(htmlspecialchars_decode($contnet), true);
        }

        if (!isset($json['list']) || empty($json['list']) || !is_array($json['list'])) {
            file_put_contents('getMsgjson.error', json_encode($contnet));
            exit;
        }

        $add = [];
        foreach ($json['list'] as $k => $v) {
            //type=49代表是图文消息
            if ($v['comm_msg_info']['type'] == 49) {

                $is_multi = $v['app_msg_ext_info']['is_multi']; //是否是多图文消息
                $datetime = $v['comm_msg_info']['datetime']; //图文消息发送时间
                //在这里将图文消息链接地址插入到采集队列库中（队列库将在后文介绍，主要目的是建立一个批量采集队列，另一个程序将根据队列安排下一个采集的公众号或者文章内容）
                //在这里根据$content_url从数据库中判断一下是否重复

                $state = $this->checkArticle($v['app_msg_ext_info']['content_url'], $v['app_msg_ext_info']['title'], $user_id);
                if ($state === false) {
                    echo "跳过 {$v['app_msg_ext_info']['title']} \n";
                    continue;
                }
                $content_url = $state;

                // 存入文章标题表
                /* $add[] = [
                    'biz' => $biz,
                    'field_id' => $v['app_msg_ext_info']['fileid'], //一个微信给的id
                    'title' => $v['app_msg_ext_info']['title'], //文章标题
                    'title_encode' => urlencode(str_replace("&nbsp;", "", $v['app_msg_ext_info']['title'])), // //建议将标题进行编码，这样就可以存储emoji特殊符号了
                    'digest' => $v['app_msg_ext_info']['digest'], //文章摘要
                    'content_url' => $content_url,
                    'source_url' => str_replace("\\", "", htmlspecialchars_decode($v['app_msg_ext_info']['source_url'])), //阅读原文的链接
                    'cover' => str_replace("\\", "", htmlspecialchars_decode($v['app_msg_ext_info']['cover'])), //封面图片
                    'is_multi' => $is_multi,
                    'is_top' => 1,
                    'datetime' => time(),
                ]; */

                $add[] = [
                    'user_id' => $user_id,
                    'title' => $v['app_msg_ext_info']['title'], //文章标题
                    'digest' => $v['app_msg_ext_info']['digest'], //文章摘要
                    'article_img' => str_replace("\\", "", htmlspecialchars_decode($v['app_msg_ext_info']['cover'])), // 文章列表图
                    'source_url' => $content_url,
                    'content' => '',
                    'weixin_article_id' => $v['app_msg_ext_info']['fileid'], //一个微信给的id
                    'datetime' => $v['comm_msg_info']['datetime'],
                ];

                //如果是多图文消息
                if ($is_multi == 1) {
                    foreach ($v['app_msg_ext_info']['multi_app_msg_item_list'] as $kk => $vv) {

                        $state = $this->checkArticle($vv['content_url'], $vv['title'], $user_id);
                        if ($state === false) {
                            echo "跳过 {$vv['title']} \n";
                            continue;
                        }
                        $content_url = $state;

                        //现在存入数据库
                        /* $add[] = [
                            'biz' => $biz,
                            'field_id' => $vv['fileid'], //一个微信给的id
                            'title' => $vv['title'], //文章标题
                            'title_encode' => urlencode(str_replace("&nbsp;", "", $vv['title'])), //建议将标题进行编码，这样就可以存储emoji特殊符号了
                            'digest' => htmlspecialchars($vv['digest']), //文章摘要
                            'content_url' => $content_url,
                            'source_url' => str_replace("\\", "", htmlspecialchars_decode($vv['source_url'])), //阅读原文的链接
                            'cover' => str_replace("\\", "", htmlspecialchars_decode($vv['cover'])), //封面图片
                            'is_multi' => 1,
                            'is_top' => 0,
                            'datetime' => time(),
                        ]; */


                        $add[] = [
                            'user_id' => $user_id,
                            'title' => $vv['title'],
                            'digest' => htmlspecialchars($vv['digest']), //文章摘要
                            'article_img' => str_replace("\\", "", htmlspecialchars_decode($vv['cover'])), //封面图片
                            'source_url' => $content_url,
                            'content' => '',
                            'weixin_article_id' => $vv['fileid'],
                            'datetime' => time(),
                        ];

                    }
                }
            }
        }

        $this->addArticleIndex($add);
    }

    // 获取文章阅读量和点赞量的程序
    public function getMsgExt()
    {
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
        $num = $this->db->getField($sql);

        if ($num) {
            $read_num = $json['appmsgstat']['read_num']; //阅读量
            $like_num = $json['appmsgstat']['like_num']; //点赞量
            //在这里同样根据sn在采集队列表中删除对应的文章，代表这篇文章可以移出采集队列了
            $this->db->delete('tmplist', "content_url like '%{$sn}%'");

            $data = [
                'readNum' => $read_num,
                'likeNum' => $like_num
            ];
            $this->db->update('post', $data, "`biz`='".$biz."' and `content_url` like '%".$sn."%'");

            //然后将阅读量和点赞量更新到文章表中。
            $msg = "阅读量和点赞量更新\n";
            exit($msg); //可以显示在anyproxy的终端里
        }
    }

    public function getWxHis()
    {
        //getWxHis.php 当前页面为公众号历史消息时，读取这个程序
        //在采集队列表中有一个load字段，当值等于1时代表正在被读取
        //首先删除采集队列表中load=1的行
        //然后从队列表中任意select一行
        // $this->db->delete('tmplist', "`load`=1");

        // $sql = "select * from tmplist where `load`=0 limit 1";
        // $tmplist = $this->db->find($sql);
        //$tmplist = array();
        //if (empty($tmplist)) {
            $sql = "SELECT id,biz FROM users WHERE state = 0 LIMIT 1";
            $info = $this->db->find($sql);
            $biz = $info['biz'];
            //队列表如果空了，就从存储公众号biz的表中取得一个biz，这里我在公众号表中设置了一个采集时间的time字段，按照正序排列之后，就得到时间戳最小的一个公众号记录，并取得它的biz
            // $url = "http://mp.weixin.qq.com/mp/getmasssendmsg?__biz=" . $biz . "#wechat_webview_type=1&wechat_redirect"; //拼接公众号历史消息url地址（第一种页面形式）
            $url = "https://mp.weixin.qq.com/mp/profile_ext?action=home&__biz={$biz}&scene=124#wechat_redirect"; //拼接公众号历史消息url地址（第二种页面形式）

            //更新刚才提到的公众号表中的采集时间time字段为当前时间戳。
            $this->db->update('users', ['state' => 1], "id='{$info['id']}'");

            echo "setTimeout(function(){window.location.href='" . $url . "';},2000);"; //将下一个将要跳转的$url变成js脚本，由anyproxy注入到微信页面中。
        //} else {
            //取得当前这一行的content_url字段
            // $url = $tmplist['content_url'];
            //将load字段update为1
            // $this->db->update('tmplist', ['load' => 1], "id='{$tmplist['id']}'");
        //}

    }

##################### 公共部分 ###############################
    protected function url_decode($url)
    {
        return str_replace("\\", "", htmlspecialchars_decode($url));
    }

    //从数据库中查询biz是否已经存在，如果不存在则插入，这代表着我们新添加了一个采集目标公众号。
    protected function checkBiz($biz, $nickname, $logo)
    {
        // 公众号
        $sql = "SELECT id,biz,weixin_name,logo FROM users WHERE biz='{$biz}' OR weixin_name = '{$nickname}'";
        $user_info = $this->db->find($sql);
        if ($user_info) {
            if (empty($user_info['biz']) || empty($user_info['weixin_name']) || empty($user_info['logo'])) {
                $state = $this->db->update('users', ['biz' => $biz, 'weixin_name' => $nickname, 'logo' => $logo], "id = '{$user_info['id']}'");
                if (!$state) {
                    echo $this->db->getSql();
                    echo "更新 biz {$biz} 失败\n";
                }
            }

            $user_id = $user_info['id'];
        } else {
            $add = [
                'user_name' => $nickname,
                'logo' => $logo,
                'weixin_name' => $nickname,
                'biz' => $biz,
                'state' => 0
            ];
            $user_id = $this->db->insert('users', $add);
            if ($user_id < 1) {
                echo "add weixin biz {$biz} error\n";
            }
        }

        return $user_id;
    }

    // 检查文章是否抓取过。没有就添加到抓取队列中
    public function checkArticle($content_url, $title, $user_id)
    {
        $content_url = $this->url_decode($content_url);

        $sql = "SELECT COUNT(*) FROM article WHERE source_url='{$content_url}' AND user_id='{$user_id}'";

        $num = $this->db->getField($sql);

        if ($num == 0) {
            $this->db->insert('tmplist', ['content_url' => $content_url]);

            return $content_url;
        } else {
            return false;
        }
    }

    // 添加文章信息
    /* protected function addArticleIndex($data)
    {
        foreach ($data as $add) {
            $title = $add['title'];

            $lastId = $this->db->insert('post', $add);
            if ($lastId) {
                echo "头条标题：" . $title . $lastId . "\n"; //这个echo可以显示在anyproxy的终端里
            } else {
                $error_log = "入库失败2：" . json_encode($add, JSON_UNESCAPED_UNICODE) . "\n";
                file_put_contents('error.log', $error_log, FILE_APPEND | LOCK_EX);
                echo "入库失败：" . $title . "\n"; //这个echo可以显示在anyproxy的终端里
            }
        }
    } */

    protected function addArticleIndex($data)
    {
        foreach ($data as $add) {
            $title = $add['title'];

            $lastId = $this->db->insert('article', $add);
            if ($lastId) {
                echo "头条标题：" . $title . $lastId . "\n"; //这个echo可以显示在anyproxy的终端里
            } else {
                $error_log = "入库失败2：" . json_encode($add, JSON_UNESCAPED_UNICODE) . "\n";
                file_put_contents('error.log', $error_log, FILE_APPEND | LOCK_EX);
                echo "入库失败：" . $title . "\n"; //这个echo可以显示在anyproxy的终端里
            }
        }
    }
}

// 数据库
$db_type = $config['db_type'];
$db = (new Db($db_type))->init($config[$db_type]);

// 操作方法
$action = $_GET['op'] ?? 'index';
$controller = new Tool($db);
$controller->$action();
