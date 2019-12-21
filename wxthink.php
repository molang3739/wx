<?php
/**
  * wechat php test
  */

//define your token
//error_reporting(E_ALL);
define("TOKEN", "dahuanggou");
$wechatObj = new wechatCallbackapiTest();
//$wechatObj->valid();
$wechatObj->responseMsg();

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

    public function responseMsg()
    {
        //get post data, May be due to the different environments
        //$postStr = $GLOBALS["HTTP_RAW_POST_DATA"];
        $postStr = isset($GLOBALS['HTTP_RAW_POST_DATA']) ? $GLOBALS['HTTP_RAW_POST_DATA'] : file_get_contents("php://input");

        //extract post data
        if (!empty($postStr)){
                /* libxml_disable_entity_loader is to prevent XML eXternal Entity Injection,
                   the best way is to check the validity of xml by yourself */
                libxml_disable_entity_loader(true);
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
                    $contentStr = "Welcome to wechat world!";

                    if($keyword == '1'){
                        $contentStr = "你输入的是1";
                    }elseif($keyword == '1'){
                        $contentStr = "你输入的是2";
                    }


                    $url = 'http://www.tuling123.com/openapi/api?key=93b251aca9fa7c919ddd80384a3feb60&info=';
                    $url ='https://api.ownthink.com/bot?appid=45993efdc5ea614dd5e0dbddc664d283&userid=user&spoken=';
                    $info = urlencode($keyword);
                    file_put_contents('log.txt', $keyword.'\r\n;',FILE_APPEND);
                    $res = file_get_contents($url.$info);
                    //echo $res;
                    $data = json_decode($res);
                    //echo $data->text;
                    $contentStr =$data->data->info->text;
		    //$contentStr='abcd';

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
        // you must define TOKEN by yourself
        if (!defined("TOKEN")) {
            throw new Exception('TOKEN is not defined!');
        }

        $signature = $_GET["signature"];
        $timestamp = $_GET["timestamp"];
        $nonce = $_GET["nonce"];

        $token = TOKEN;
        $tmpArr = array($token, $timestamp, $nonce);
        // use SORT_STRING rule
        sort($tmpArr, SORT_STRING);
        $tmpStr = implode( $tmpArr );
        $tmpStr = sha1( $tmpStr );
        //echo $tmpStr;
        if( $tmpStr == $signature ){
            return true;
        }else{
            return false;
        }
    }
}

?>
