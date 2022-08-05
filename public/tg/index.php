<?php

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
$data = json_decode(file_get_contents('php://input'), TRUE);
file_put_contents(__DIR__ . '/message.txt', print_r($data, true));

if (isset($data['callback_query'])) {
    $data = $data['callback_query'];
    $data = isset($data['data']) ? json_decode($data['data']) : [];
    $message = mb_strtolower($data['action']);
} else {
    $data = $data['message'];
    $message = mb_strtolower(($data['text'] ? $data['text'] : $data['data']));
}
file_put_contents(__DIR__ . '/message2.txt', print_r($message, true));


switch ($message) {
    case '/start':
        $keyboard = array(
            array(
                array('text'=>'Мне подходит вариант 1','callback_data'=>'{"action":"variant_1","count":0,"text":":like:"}'),
                array('text'=>'Мне подходит вариант 2','callback_data'=>'{"action":"joy","count":0,"text":":joy:"}'),
                array('text'=>'Связаться с менеджером','callback_data'=>'{"action":"hushed","count":0,"text":":hushed:"}'),
                array('text'=>'Частые вопросы','callback_data'=>'{"action":"cry","count":0,"text":":cry:"}'),
                array('text'=>'Отзывы','callback_data'=>'{"action":"rage","count":0,"text":":rage:"}'),
                array('text'=>'Акция','callback_data'=>'{"action":"rage","count":0,"text":":rage:"}'),
                array('text'=>'Договор оферта','callback_data'=>'{"action":"rage","count":0,"text":":rage:"}')
            )
        );

        request("sendMessage", array(
            'chat_id' => CHAT_ID,
            'text' => "first message",
            'disable_web_page_preview' => false,
            'reply_markup' => json_encode(array('inline_keyboard' => $keyboard))
        ));
        break;
    case 'variant_1':
        $keyboard = array(
            array(
                array('text'=>'Может и вариант 2','callback_data'=>'{"action":"like","count":0,"text":":like:"}'),
            )
        );

        request("sendMessage", array(
            'chat_id' => CHAT_ID,
            'text' => "second message",
            'disable_web_page_preview' => false,
            'reply_markup' => json_encode(array('inline_keyboard' => $keyboard))
        ));
        break;
    default:
        $keyboard = array(
            array(
                array('text'=>'повторить','callback_data'=>'{"action":"like","count":0,"text":":like:"}'),
            )
        );

        request("sendMessage", array(
            'chat_id' => CHAT_ID,
            'text' => "Команда не найдена. Повторите попытку",
            'disable_web_page_preview' => false,
            'reply_markup' => json_encode(array('inline_keyboard' => $keyboard))
        ));
}

