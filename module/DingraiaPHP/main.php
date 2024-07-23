<?php

//导入模块
if ($requireMoudle == "lxyddice") {
    $x = DingraiaPHPLoadMoudlePluginMain($body, $conf);
}

function DingraiaPHPLoadMoudlePluginMain($body, $conf) {
    global $bot_run_as;
    
    if (isset($_GET["cronDie"])) {
        $cronAuth = "lxyddice";
        require_once("module/DingraiaPHP/cron/die.php");
    }
    /*下面是框架功能内，以插件形式载入，请勿动~*/
    if (isset($_GET['action'])) {
        if ($_GET['action'] == "api") {
            /*框架API类*/
            $c["chat_mode"] = "DingraiaPHPApi";
            $r[] = $c;
            return $r;
        }
        if ($_GET['action'] == "admin") {
            /*框架API类*/
            $c["chat_mode"] = "DingraiaPHPAdminHtml";
            $r[] = $c;
            return $r;
        }
    }
    
    return false;
}