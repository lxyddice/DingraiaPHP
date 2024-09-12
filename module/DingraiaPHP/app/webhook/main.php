<?php

if ($bot) {
    if (file_exists("config/module/webhook/config.json")) {
        $webhookConfig = read_file_to_array("config/module/webhook/config.json");
        if (isset($webhookConfig["sendWebhook"])) {
            if (isset($webhookConfig["useSign"]) && $webhookConfig["useSign"]) {
                //使用签名
                $webhookHeader = ["Content-Type" => "application/json", "DingraiaSign"=>hash("sha256",$webhookConfig["sign"].file_get_contents("php://input"))];
            } else {
                $webhookHeader = ["Content-Type" => "application/json"];
            }
            foreach ($webhookConfig["sendWebhook"] as $webhook) {
                if (isset($webhook["sendChatMode"])) {
                    if (in_array($bot["chat_mode"], $webhook["sendChatMode"])) {
                        $res = requests("POST", $webhook["url"], json_decode(file_get_contents("php://input"), true), $webhookHeader, $webhook["timeout"]);
                        if (count($webhook["callback"]) > 0) {
                            foreach ($webhook["callback"] as $callback) {
                                if (isset($res["body"])) {
                                    send_message($res["body"], $callback);
                                } else {
                                    send_message("Webhook发送时无法获取回调响应body", $callback);
                                }
                            }
                        }
                    }
                }
            }
        }
    }
}