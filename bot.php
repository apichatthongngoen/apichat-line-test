<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once 'vendor/autoload.php';
require_once 'bot_settings.php';

use LINE\LINEBot;
use LINE\LINEBot\HTTPClient\CurlHTTPClient;
//use LINE\LINEBot\Event;
//use LINE\LINEBot\Event\BaseEvent;
//use LINE\LINEBot\Event\MessageEvent;
use LINE\LINEBot\TemplateActionBuilder\UriTemplateActionBuilder;

$httpClient = new CurlHTTPClient(LINE_MESSAGE_ACCESS_TOKEN);
$bot = new LINEBot($httpClient, array('channelSecret' => LINE_MESSAGE_CHANNEL_SECRET));

$content = file_get_contents('php://input');

$events = json_decode($content, true);
$arr_replyData = array();

if (!is_null($events)) {

    $replyToken = $events['events'][0]['replyToken'];
    $userID = $events['events'][0]['source']['userId'];
    $sourceType = $events['events'][0]['source']['type'];
    $is_postback = null;
    $is_message = null;
    if (isset($events['events'][0]) && array_key_exists('message', $events['events'][0])) {
        $is_message = true;
        $typeMessage = $events['events'][0]['message']['type'];
        $userMessage = $events['events'][0]['message']['text'];
        $idMessage = $events['events'][0]['message']['id'];
    }
    if (isset($events['events'][0]) && array_key_exists('postback', $events['events'][0])) {
        $is_postback = true;
        $dataPostback = null;
        parse_str($events['events'][0]['postback']['data'], $dataPostback);
        $paramPostback = null;
        if (array_key_exists('params', $events['events'][0]['postback'])) {
            if (array_key_exists('date', $events['events'][0]['postback']['params'])) {
                $paramPostback = $events['events'][0]['postback']['params']['date'];
            }
            if (array_key_exists('time', $events['events'][0]['postback']['params'])) {
                $paramPostback = $events['events'][0]['postback']['params']['time'];
            }
            if (array_key_exists('datetime', $events['events'][0]['postback']['params'])) {
                $paramPostback = $events['events'][0]['postback']['params']['datetime'];
            }
        }
    }
    $arr_replyData[] = new TextMessageBuilder("sen");

}

$multiMessage = new MultiMessageBuilder;
foreach ($arr_replyData as $arr_Reply) {
    $multiMessage->add($arr_Reply);
}
$replyData = $multiMessage;
$response = $bot->replyMessage($replyToken, $replyData);
if ($response->isSucceeded()) {
    echo 'Succeeded!';
    return;
}

echo $response->getHTTPStatus() . ' ' . $response->getRawBody();