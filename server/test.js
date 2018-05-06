module.exports = {
  *beforeSendResponse(requestDetail, responseDetail) {
    var newResponse = responseDetail.response;

    if (newResponse.body.toString() == "") {
      return null;
    }

    if (/mp\/getmasssendmsg/i.test(requestDetail.url)) {
      try {//防止报错退出程序
        var reg = /msgList = (.*?);/;//定义历史消息正则匹配规则
        var ret = reg.exec(newResponse.body.toString());//转换变量为string
        HttpPost(ret[1],requestDetail.url,"/index.php?op=getMsgJson");//这个函数是后文定义的，将匹配到的历史消息json发送到自己的服务器
      } catch(e) {//如果上面的正则没有匹配到，那么这个页面内容可能是公众号历史消息页面向下翻动的第二页，因为历史消息第一页是html格式的，第二页就是json格式的。
        try {
          var json = JSON.parse(newResponse.body.toString());
          if (json.general_msg_list != []) {
            HttpPost(json.general_msg_list,requestDetail.url,"/index.php?op=getMsgJson");//这个函数和上面的一样是后文定义的，将第二页历史消息的json发送到自己的服务器
          }
        }catch(e){
          console.log(e);//错误捕捉
        }
      }
    } else if (/mp\/profile_ext\?action=home/i.test(requestDetail.url)) {
      try {
        var reg = /var msgList = \'(.*?)\';/;//定义历史消息正则匹配规则（和第一种页面形式的正则不同）
        var ret = reg.exec(newResponse.body.toString());//转换变量为string
        HttpPost(ret[1],requestDetail.url,"/index.php?op=getMsgJson");//这个函数是后文定义的，将匹配到的历史消息json发送到自己的服务器
      }catch(e){
        console.log(e);//错误捕捉
      }
    } else if (/mp\/profile_ext\?action=getmsg/i.test(requestDetail.url)) {
      try {
        var json = JSON.parse(newResponse.body.toString());
        if (json.general_msg_list != []) {
          HttpPost(json.general_msg_list,requestDetail.url,"/index.php?op=getMsgJson");//这个函数和上面的一样是后文定义的，将第二页历史消息的json发送到自己的服务器
        }
      }catch(e){
        console.log(e);
      }
    } else {
      return null;
    }
  }
};

function HttpPost(str, url, path) {//将json发送到服务器，str为json内容，url为历史消息页面地址，path是接收程序的路径和文件名
  var http = require('http');
  var data = {
    str: encodeURIComponent(str),
    url: encodeURIComponent(url)
  };
  content = require('querystring').stringify(data);
  var options = {
    method: "POST",
    host: "www.test.me",//注意没有http://，这是服务器的域名。
    port: 80,
    path: path,//接收程序的路径和文件名
    headers: {
      'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8',
      "Content-Length": content.length
    }
  };
  var req = http.request(options, function (res) {
    res.setEncoding('utf8');
    res.on('data', function (chunk) {
      console.log('BODY: ' + chunk);
    });
  });
  req.on('error', function (e) {
    console.log('problem with request: ' + e.message);
  });
  req.write(content);
  req.end();
}
