# anyproxy 微信采集

## 安装环境

anyproxy 4.0+

npm install -g anyproxy

php 7+

```
# 窗口 1
anyproxy -i --rule server/test.js

# 窗口 2
php -S localhost:80 api/

```