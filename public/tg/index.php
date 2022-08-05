<?php

use Telegram\Bot\Api;

require '../../vendor/autoload.php';

const TOKEN = '5402169870:AAGd1B4gqLVz_1F6pLWVjh5fBVXuOadRqgw';

define( 'CHAT_ID', '316341641' ); // @name_chat
define( 'API_URL', 'https://api.telegram.org/bot' . TOKEN . '/' );

function request($method, $params = array()) {
    if (!empty($params) ) {
        $url = API_URL . $method . "?" . http_build_query($params);
    } else {
        $url = API_URL . $method;
    }

    return json_decode(file_get_contents($url), JSON_OBJECT_AS_ARRAY);
}

$keyboard = array(
    array(
        array('text'=>':like:','callback_data'=>'{"action":"like","count":0,"text":":like:"}'),
        array('text'=>':joy:','callback_data'=>'{"action":"joy","count":0,"text":":joy:"}'),
        array('text'=>':hushed:','callback_data'=>'{"action":"hushed","count":0,"text":":hushed:"}'),
        array('text'=>':cry:','callback_data'=>'{"action":"cry","count":0,"text":":cry:"}'),
        array('text'=>':rage:','callback_data'=>'{"action":"rage","count":0,"text":":rage:"}')
    )
);

request("sendMessage", array(
    'chat_id' => CHAT_ID,
    'text' => "hello world!",
    'disable_web_page_preview' => false,
    'reply_markup' => json_encode(array('inline_keyboard' => $keyboard))
));
