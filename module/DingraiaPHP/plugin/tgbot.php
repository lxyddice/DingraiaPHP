<?php

function DingraiaPHPMoudleGetTelegramBot($body, $conf) {
    if (isset($body['dingraiaAuthArray'])) {
        $timestamp = $body['dingraiaAuthArray']['timestamp'];
        $chatId = $body['dingraiaAuthArray']['messageId'];
        $key = $conf['dingraiaAuthKey'];
        $hash = hash('sha256', $key.$timestamp.$chatId);
        if ($hash == $body['dingraiaAuthArray']['dingraiaAuth']) {
            return $body;
        }
    }
    return false;
}

function telegramBotSendMessage($text, $chatId, $url) {
    global $bot_run_as;
    $dingraiaAuthKey = $bot_run_as['config']['dingraiaAuthKey'];
    $timestamp = time();
    $hash = hash('sha256', $dingraiaAuthKey.$timestamp.$chatId);
    $res = requests("POST", $url, ["text"=>$text, "chatId"=>$chatId, "dingraiaAuth"=>$hash, "timestamp"=>$timestamp],['Content-Type' => 'application/json']);
    return $res;
}