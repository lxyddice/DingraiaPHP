<?php
/* webhook */
/* 是。收webhook的也有webhook... */
if ($bot_run_as) {
    if (file_exists("config/module/webhook/config.json")) {
        $webhookConfig = read_file_to_array("config/module/webhook/config.json");
        if (isset($webhookConfig["sendWebhook"])) {
            if ($webhookConfig["useSign"]) {
                //使用签名
                $webhookHeader = ["Content-Type" => "application/json", "DingraiaSign"=>hash("sha256",$webhookConfig["sign"].file_get_contents("php://input"))];
            } else {
                $webhookHeader = ["Content-Type" => "application/json"];
            }
            foreach ($webhookConfig["sendWebhook"] as $webhook) {
                if (isset($webhook["sendChatMode"])) {
                    if (in_array($bot_run_as["chat_mode"], $webhook["sendChatMode"])) {
                        $res = requests("POST", $webhook["url"], json_decode(file_get_contents("php://input"), true), $webhookHeader)["body"];
                        if (count($webhook["callback"]) > 0) {
                            foreach ($webhook["callback"] as $callback) {
                                send_message($res, $callback);
                            }
                        }
                    }
                }
            }
        }
    }
}