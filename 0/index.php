<?php

//echo "Welcome to goodbai.duapp";
//define your token
define("TOKEN", "qwerty2345");
$wechatObj = new wechatCallbackapiTest();
//$wechatObj->valid();
$wechatObj->responseMsg();

//echo $wecharObj->baiduDic("你好");
class wechatCallbackapiTest
{
	public function valid()
    {
        $echoStr = $_GET["echostr"];

        //valid signature , option
        if($this->checkSignature()){
        	echo $echoStr;
        	exit;
        }
    }
  
  public function youdaoTran($word){

        $keyfrom = "goodbaiA";    //申请APIKEY时所填表的网站名称的内容
        $apikey = "2122702772";  //从有道申请的APIKEY    
        //有道翻译-json格式
    //$url_youdao = 'http://fanyi.youdao.com/fanyiapi.do?keyfrom='.$keyfrom.'&key='.$apikey.'&type=data&doctype=json&version=1.1&q='.$word; 
    	$url_youdao = 'http://fanyi.youdao.com/openapi.do?keyfrom='.$keyfrom.'&key='.$apikey.'&type=data&doctype=json&version=1.1&q='.$word; 
    $result = json_decode($this->getdata4URL($url_youdao));
        $errorCode = $result['errorCode'];        
        $trans = '';
        if(isset($errorCode)){
            switch ($errorCode){
                case 0:
                    $trans = $result['translation']['0'];
                    break;
                case 20:
                    $trans = '要翻译的文本过长';
                    break;
                case 30:
                    $trans = '无法进行有效的翻译';
                    break;
                case 40:
                    $trans = '不支持的语言类型';
                    break;
                case 50:
                    $trans = '无效的key';
                    break;
                default:
                    $trans = '出现异常';
                    break;
            }
        }
    	$trans =  "youdao";
        return $trans;  
    }
  
 	public function baiduTran($word,$from="auto",$to="auto")
    {
      //echo "My tran function.";
      //$word = "你好";
        $appid = "ANGEgE28iVZYfWqOY80ih0Az";
		$word_code=urlencode($word);
		$baidu_url = "http://openapi.baidu.com/public/2.0/bmt/translate?client_id=".$appid."&q=".$word_code."&from=".$from."&to=".$to;
        $text=json_decode($this->getdata4URL($baidu_url));
        $text = $text->trans_result;
        $src = $text[0]->src;
        $dst = $text[0]->dst;
        $outstr = "[".$src."]: ".$dst;
        return $outstr;
      
     /* $trans = '';
        $errorCode = $result['error_code'];  
 		if(isset($errorCode)){
            $trans = '出现异常';
        }else{
       		$trans1 = $result['trans_result']['0'];
          	$trans = $trans1->dst;
        	return $trans;
        }
*/
    }
        
    //百度翻译-获取目标URL所打印的内容
    public function getdata4URL($url){
        if(!function_exists('file_get_contents')){
            $file_contents = file_get_contents($url);
        }else{              
            //初始化一个cURL对象
            $ch = curl_init();
            $timeout = 5;
            //设置需要抓取的URL
            curl_setopt ($ch, CURLOPT_URL, $url);
            //设置cURL 参数，要求结果保存到字符串中还是输出到屏幕上
            curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
            //在发起连接前等待的时间，如果设置为0，则无限等待
            curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
            //运行cURL，请求网页
            $file_contents = curl_exec($ch);
            //关闭URL请求
            curl_close($ch);
        }
        return $file_contents;
    }

    public function responseMsg()
    {
		//get post data, May be due to the different environments
		$postStr = $GLOBALS["HTTP_RAW_POST_DATA"];

      	//extract post data
		if (!empty($postStr)){
                
              	$postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
                $fromUsername = $postObj->FromUserName;
                $toUsername = $postObj->ToUserName;
                $keyword = trim($postObj->Content);
                $time = time();
                $textTpl = "<xml>
							<ToUserName><![CDATA[%s]]></ToUserName>
							<FromUserName><![CDATA[%s]]></FromUserName>
							<CreateTime>%s</CreateTime>
							<MsgType><![CDATA[%s]]></MsgType>
							<Content><![CDATA[%s]]></Content>
							<FuncFlag>0</FuncFlag>
							</xml>";             
				if(!empty( $keyword ))
                {
              		$msgType = "text";
                  //$contentStr = "Welcome to wechat world!";
                  //$contentStr = $keyword;
                  //  $contentStr = $this->baiduTran($keyword);
                    $contentStr = $this->youdaoTran($keyword);
                	$resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
                	echo $resultStr;
                }else{
                	echo "Input something...";
                }

        }else {
        	echo "";
        	exit;
        }
    }
		
	private function checkSignature()
	{
        $signature = $_GET["signature"];
        $timestamp = $_GET["timestamp"];
        $nonce = $_GET["nonce"];	
        		
		$token = TOKEN;
		$tmpArr = array($token, $timestamp, $nonce);
		sort($tmpArr);
		$tmpStr = implode( $tmpArr );
		$tmpStr = sha1( $tmpStr );
		
		if( $tmpStr == $signature ){
			return true;
		}else{
			return false;
		}
	}
}

?>