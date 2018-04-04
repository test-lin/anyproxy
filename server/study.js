module.exports = {
  summary: 'a rule to hack response',
  *beforeSendResponse(requestDetail, responseDetail) {
    var req = requestDetail._req,
        res = responseDetail._res,
        serverResData = responseDetail.response.body,
        newResponse = Object.assign({}, responseDetail.response),
        http = require('http');
    if(serverResData.toString() !== ""){
      try {//防止报错退出程序
        newResponse.body = serverResData.toString().replace(/(.*)(<script.*?>)(.*)/,'$1$2alert(\'hello world!!!\');$3');
        return {response: newResponse};
        // return new Promise((resolve, reject) => {
        //   resolve({ response: newResponse });
        // });
      } catch(e) {
        console.log(e);
      }
    }
  },
};
