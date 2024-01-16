<?php

//导入外置模块

if ($requireMoudle == "lxyddice") {
    $x = DingraiaPHPLoadMoudlePluginMain($body, $conf);
}

function DingraiaPHPLoadMoudlePluginMain($body, $conf) {
    global $bot_run_as;
    if (isset($_GET["tgbot"])) {
        require_once("module/DingraiaPHP/plugin/tgbot.php");
        $c = DingraiaPHPMoudleGetTelegramBot($body, $conf);
        if ($c) {
            $c['chat_mode'] = 'tgbot';
            $r[] = $c;
            return $r;
        } else {
            DingraiaPHPResponseExit(403, "AuthKey is error");
        }
    }
    if (isset($_GET["MAAArknightsGetTask"]) || isset($_GET['MAAArknightsReportStatus'])) {
        require_once("module/DingraiaPHP/plugin/MAAArknights.php");
        $c = DingraiaPHPMaaArknightsMain($body, $conf);
        if ($c) {
            $c["chat_mode"] = "MAAArknights";
            $r[] = $c;
            return $r;
        }
        return false;
    }
    return false;
}