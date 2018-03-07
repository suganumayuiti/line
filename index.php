<?php

//Composerでインストールしたライブラリを一括読み込み
require_once __DIR__ . '/vendor/autoload.php';

//アルセストークンを使いCurlHTTPClientをインスタンス化
$httpClient = new \LINE\LINEBot\HTTPClient\CurlHTTPClient(getenv('CHANNEL_ACCESS_TOKEN'));

//CurlHTTPClientとシークレットを使いLINEBotをインスタンス化
$bot = new \LINE\LINEBot($httpClient,['channelSecret' => getenv('CHANNEL_SECRET_TOKEN')]);

//LINE Message APIがリクエストに付与した署名を取得
$signature = $_SERVER["HTTP_".\LINE\LINEBot\Constant\HTTPHeader::LINE_SIGNATURE];

try{
    $events = $bot->parseEventRequest(file_get_contents('php://input'),$signuature);
}catch(\LINE\LINEBot\Exception\InvalidSignatureException $e){
    error_log('parseEventRequest failed. InvalidSignatureException =>'.var_export($e,true));
}catch(\LINE\LINEBot\Exception\UnknownEventTypeException $e){
    error_log('parseEventRequest failed. UnknownEventTypeException =>'.var_export($e,true));
}catch(\LINE\LINEBot\Exception\UnknownMessageTypeException $e){
    error_log('parseEventRequest failed. UnknownMessageTypeException =>'.var_export($e,true));
}catch(\LINE\LINEBot\Exception\InvalidEventRequestException $e){
    error_log('parseEventRequest failed. InvalidEventRequestException =>'.var_export($e,true));
}


//配列に格納された各イベントをループで処理
 foreach($events as $event){
     //MessageEventクラスのインスタンスでなければ処理をスキップ
     if(!($event instanceof \LINE\LINEBot\Event\MessageEvent)){
         error_log('Non message event has come');
         continue;
     }
     //TextMessageクラスのインスタンスでなければ処理をスキップ
     if(!($event instanceof \LINE\LINEBot\Event\MessageEvent\TextMessage)){
        error_log('Non message event has come');
        continue;
    }
    $bot->replyText($event->getReplyToken(),$event->getText());
 }
   //テキストメッセージ
function replyTextMessage($bot,$replyToken,$text){
    $response = $bot->replyMessage($replyToken,new \LINE\LINEBot\MessageBuilder\TextMessageBuilder($text));

    if(!$response->isSucceeded()){
        error_log('Faild!!'.$response->getHTTPStatus .' '.$response->getRawBody());
    }
}
//画像
function replyImageMessage($bot,$replyToken,$originalImageUrl,$previewImageUrl){
    $response = $bot->replyMessage($replyToken,new \LINE\LINEBot\
    MessageBuilder\ImageMessageBuilder($originalImageUrl,$previewImageUrl));
   
    if(!$response->isSucceeded()){
        error_log('Faild!!'.$response->getHTTPStatus .' '.$response->getRawBody());
    }   
}
//位置情報
function replyLocationMessage($bot,$replyToken,$title,$address,$lat,$lon){
    $response=$bot->replyMessage($replyToken,new \LINE\LINEBot\
    MessageBuilder\LocationMessageBuilder(
        $title,$address,$lat,$lon));

    if(!$response->isSucceeded()){
        error_log('Faild!!'.$response->getHTTPStatus .' '.$response->getRawBody());
    }     
}
//スタンプ
function replyStickerMessage($bot,$replyToken,$packageId,$stickerId){
    $response = $bot->replyMessage($replyToken,new \LINE\LINEBot\
    MessageBuilder\StickerMessageBuilder(
        $packageId,$stickerId));

    if(!$response->isSucceeded()){
        error_log('Faild!!'.$response->getHTTPStatus .' '.$response->getRawBody());
    }   
}
//動画
function replyVideoMessage($bot,$replyToken,$originalContentUrl,$previewImageUrl){
    $response = $bot->replyMessage($replyToken,new \LINE\LINEBot\
    MessageBuilder\VideoMessageBuilder(
    $originalContentUrl,$previewImageUrl));
    if(!$response->isSucceeded()){
        error_log('Faild!!'.$response->getHTTPStatus .' '.$response->getRawBody());
    }     
}
//オーディオ
function replyAudioMessage($bot,$replyToken,$originalContentUrl,$audioLength){
    $response = $bot->replyMessage($replyToken,new \LINE\LINEBot\
    MessageBuilder\AudioMessageBuilder(
        $originalContentUrl,$audioLength));

    if(!$response->isSucceeded()){
        error_log('Faild!!'.$response->getHTTPStatus .' '.$response->getRawBody());
    }   
}
//複数のメッセージをまとめて送信
function replyMultiMessage($bot,$replyToken,...$msgs){
    $builder = new \LINE\LINEBot\MessageBuilder\MultiMessageBuilder();
    foreach($msgs as $value){
        $builder->add($value);
    }
    $response = $bot->replyMessage($replyToken, $builder);
    if(!$response->isSucceeded()){
        error_log('Faild!!'.$response->getHTTPStatus .' '.$response->getRawBody());
    }   
}
//Button
function replyButtonsTemplate($bot,$replyToken,$alternativeText,$imageUrl,$title,$text,...$actions){
    $actionArray=array();
    foreach($actions as $value){
        array_push($actionArray,$value);
    }
    $builder = new \LINE\LINEBot\MessageBuilder\TemplateMessageBuilder($alternativeText,new \LINE\LINEBot\MessageBuilder\TemplateBuilder\ButtonTemplateBuilder($title,$text,$imageUrl,$actionArray)
);
$response = $bot->replyMessage($replyToken, $builder);
if(!$response->isSucceeded()){
    error_log('Faild!!'.$response->getHTTPStatus .' '.$response->getRawBody());
    }   
}
//Confirm
function replyConfirmTemplate($bot,$replyToken,$alternativeText,$text,...$actions){
    $actionArray=array();
    foreach($actions as $value){
        array_push($actionArray,$value);
    }
    $builder = new \LINE\LINEBot\MessageBuilder\TemplateMessageBuilder($alternativeText,
    new \LINE\LINEBot\MessageBuilder\TemplateBuilder\ConfirmTemplateBuilder(
    $text,$actionArray)
);
$response = $bot->replyMessage($replyToken, $builder);
if(!$response->isSucceeded()){
    error_log('Faild!!'.$response->getHTTPStatus .' '.$response->getRawBody());
    }   
}
//carouselテンプレートを返信
function replyCarouselTemplate($bot,$replyToken,$alternativeText,$columnArray){
    $builder = new \LINE\LINEBot\MessageBuilder\TemplateMessageBuilder(
        $alternativeText,
        new \LINE\LINEBot\MessageBuilder\TemplateBuilder\CarouselTemplateBuilder(
        $columnArray)
    );
    $response = $bot->replyMessage($replyToken, $builder);
    if(!$response->isSucceeded()){
    error_log('Faild!!'.$response->getHTTPStatus .' '.$response->getRawBody());
    }
}

?>
