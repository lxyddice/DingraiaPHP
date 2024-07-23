<?php
if (isset($bot_run_as)) {
    $pluginData = [
        "data/bot/pluginData.json",
        "data/bot/plugin.json",
    ];

    foreach ($pluginData as $file) {
        if (!file_exists($file)) {
            mkdir(dirname($file), 0777, true);
            file_put_contents($file, json_encode([]));
        }
    }

    if (!file_exists("data/bot/helps/com.lxyddice.plugin.json")) {
        file_put_contents("data/bot/helps/com.lxyddice.plugin.json", json_encode([
            "start" => "plugin", 
            "plugin" => "com.lxyddice.plugin",
            "name" => "你好，世界", 
            "info" => "你好，世界",
            "help" => "初始插件",
            "author" => "lxyddice", 
            "version" => "1.0.0",
        ]));
    }
    
    function versionGetPluginEnableCount($directory) {
        $files = scandir($directory);
        $count = 0;
        foreach ($files as $file) {
            if ($file[0] !== '_' && pathinfo($file, PATHINFO_EXTENSION) === 'php') {
                $count++;
            }
        }
        return $count;
    }

    if ($globalmessage == "--version" || $globalmessage == "-v" || $_GET["t"]) {
        $nv = $bot_run_as["config"]["dingraia_php_version"];
        $msg = "# [DingraiaPHP](https://github.com/lxyddice/DingraiaPHP)";
        $msg .= "\n\n一个PHP钉钉小程序框架...";
        $msg .= "\n\n当前版本->{$nv}";
        $msg .= "\n\n最新版本->offline";
        $msg .= "\n\n新版首页开关->".$bot_run_as["config"]["useDefaultDisplayPage"];
        $msg .= "\n\n定时任务重启->".$bot_run_as["config"]["cron"]["autoRestart"];
        $msg .= "\n\n网页管理开关->".$bot_run_as["config"]["htmlAdmin"]["use"];
        $msg .= "\n\nlogger回调url数量->".count($bot_run_as["config"]["logger"]["urls"]);
        $msg .= "\n\n启用插件数量->".versionGetPluginEnableCount("plugin");
        send_markdown($msg, $webhook, "version");
    }
}