<?php
if (isset($bot)) {
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
    
    if (isset($globalmessage)) {

        function versionGetPluginEnableCount($dir) {
            $count = 0;
            $files = scandir($dir);
            foreach ($files as $file) {
                if (strpos($file, '.') !== 0 && pathinfo($file, PATHINFO_EXTENSION) === 'php') {
                    $filePath = $dir . DIRECTORY_SEPARATOR . $file;
                    if (strpos($file, '_') !== 0) {
                        $count++;
                    }
                }
            }
            return $count;
        }
        if ($globalmessage == "--version" || $globalmessage == "-v") {
            $nv = $bot["config"]["dingraia_php_version"];
            $msg = "# [DingraiaPHP](https://github.com/lxyddice/DingraiaPHP)";
            $msg .= "\n\n一个PHP钉钉小程序框架...";
            $msg .= "\n\n当前版本->{$nv}";
            $msg .= "\n\n最新版本->offline";
            $msg .= "\n\n新版首页开关->".$bot["config"]["useDefaultDisplayPage"];
            $msg .= "\n\n定时任务重启->".$bot["config"]["cron"]["autoRestart"];
            $msg .= "\n\n网页管理开关->".$bot["config"]["htmlAdmin"]["use"];
            $msg .= "\n\nlogger回调url数量->".count($bot["config"]["logger"]["urls"]);
            $msg .= "\n\n启用插件数量->".versionGetPluginEnableCount("plugin");
            send_markdown($msg, $webhook, "version");
        }
        
        if (strpos($globalmessage, "/plugin") === 0) {
            $pluginDir = 'plugin';
            if (!permission_check("bot.plugin", $guserarr["uid"])) {
                send_message("Access denied", $webhook, $staffid);
                exit();
            }
            $result = stringf($globalmessage);
            $todo = $result["params"][1];
            $f = isset($result["params"][2]) ?? false;
            if (strpos($f, '_') !== false) {
                $r = "插件操作失败";
                send_message($r, $webhook);
                exit();
            }
            if ($todo == "enable") {
                $oldFileName = "_" . $f . ".php";
                $newFileName = $f . ".php";
                $oldFilePath = $pluginDir . DIRECTORY_SEPARATOR . $oldFileName;
                $newFilePath = $pluginDir . DIRECTORY_SEPARATOR . $newFileName;
                if (file_exists($oldFilePath)) {
                    if (rename($oldFilePath, $newFilePath)) {
                        $r = "插件启用成功";
                        tool_log(1, "$oldFilePath 插件禁用");
                    } else {
                        $r = "插件启用失败";
                        tool_log(1, "$oldFilePath 插件启用");
                    }
                } else {
                    $r = "不存在此插件";
                }
            } elseif ($todo == "disable") {
                $oldFileName = $f . ".php";
                $newFileName = "_" . $f . ".php";
                $oldFilePath = $pluginDir . DIRECTORY_SEPARATOR . $oldFileName;
                $newFilePath = $pluginDir . DIRECTORY_SEPARATOR . $newFileName;
                if (file_exists($oldFilePath)) {
                    if (rename($oldFilePath, $newFilePath)) {
                        $r = "插件禁用成功";
                    } else {
                        $r = "插件禁用失败";
                    }
                } else {
                    $r = "不存在此插件";
                }
            } elseif ($todo == "list") {
                if (is_dir($pluginDir)) {
                    $files = scandir($pluginDir);
                    $pluginarr = array('未启用的插件' => array(), '已启用的插件' => array());

                    foreach ($files as $file) {
                        if (strpos($file, '.') !== 0 && pathinfo($file, PATHINFO_EXTENSION) === 'php') {
                            $filePath = $pluginDir . DIRECTORY_SEPARATOR . $file;

                            if (strpos($file, '_') === 0) {
                                $pluginarr['未启用的插件'][] = $file;
                            } else {
                                $pluginarr['已启用的插件'][] = $file;
                            }
                        }
                    }

                    $jsonResult = json_encode($pluginarr, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
                    send_message($jsonResult, $webhook);
                } else {
                    $jsonResult = json_encode(array('error' => 'Invalid plugin folder path: ' . $pluginDir));
                    send_message($jsonResult, $webhook, $staffid);
                }
            }
        }
    }
}