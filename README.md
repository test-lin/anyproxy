# anyproxy 微信公众号文件收集

## 介绍

利用 anyproxy 截取手机请求的方式收集 **微信公众号文章**。

## 环境

* windows
* php7+
* nodejs
* mysql5.5

## 安装

```cmd

# 安装 anyproxy
npm i -g anyproxy


# 安装 api 依赖
composer install


# 导入数据收集表结构
> mysql -u用户名 -p密码
> use test;
> source data/spider.sql

```

## 运行

```cmd

# 开启 anyproxy
anyproxy -i --rule server/office.js


# 绑定 php 接口访问地址
php -S localhost:80 api/

```

设置代理

以小米5（MUI 9.6）为例子

* 开启手机开发者模式
    1. 设置
    2. 我的设备
    3. 全部参数
    4. MIUI版本 连点开启
* 设置手机网络代理
    1. 设置
    2. WLAN
    3. 连接的WLAN -> 最右边的操作图标进入
    4. 代理
    5. 手机
    6. 设置主机名（电脑的地址）
    7. 端口 8001
    8. 确认

用手机访问电脑的 8002 端口页面

如: 192.168.1.200:8002

下载 https 兼容 CA 证书（RootCA -> Download）

下载好后在手机安装 CA 证书

访问微信公众号和文章接口就会进行收集了

## 参考

[微信公众号内容的批量采集与应用](https://zhuanlan.zhihu.com/c_65943221)

## 许可

The project is open-sourced software licensed under the MIT license.

QQ: 505932384