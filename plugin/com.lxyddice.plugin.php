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
    
    function getServerUsage() {
        $memoryUsage = getMemoryUsage();
        $diskUsage = getDiskUsage();
    
        return [
            'cpu_usage' => 0,
            'memory_usage' => number_format($memoryUsage, 2),
            'disk_usage' => number_format($diskUsage, 2)
        ];
    }
    
    function getCpuUsage() {
        $load = sys_getloadavg();
        $cpuCoreCount = (int) shell_exec("nproc");
    
        if ($cpuCoreCount > 0) {
            $cpuUsage = ($load[0] / $cpuCoreCount) * 100;
            return $cpuUsage;
        }
        return 0;
    }
    function getMemoryUsage() {
        $memInfo = file_get_contents("/proc/meminfo");
        $memInfo = str_replace(array(" kB", "MemTotal:", "MemAvailable:"), "", $memInfo);
        $memInfo = preg_split("/\s+/", trim($memInfo));
    
        $totalMemory = 0;
        $availableMemory = 0;
    
        foreach ($memInfo as $key => $value) {
            if ($key == 0) {
                $totalMemory = (int)$value;
            }
            if ($key == 2) {
                $availableMemory = (int)$value;
                break;
            }
        }
    
        if ($totalMemory > 0) {
            $usedMemory = $totalMemory - $availableMemory;
            $memoryUsage = ($usedMemory / $totalMemory) * 100;
            return $memoryUsage;
        }
        return 0;
    }
    
    function getDiskUsage() {
        $diskTotal = disk_total_space("/");
        $diskFree = disk_free_space("/");
    
        if ($diskTotal > 0) {
            $diskUsed = $diskTotal - $diskFree;
            $diskUsage = ($diskUsed / $diskTotal) * 100;
            return $diskUsage;
        }
        return 0;
    }
    if (isset($globalmessage)) {
        if ($globalmessage == "--version" || $globalmessage == "-v") {
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
            $su = getServerUsage();
            $msg .= "\n\ncpu|ram|rom->{$su['cpu_usage']} | {$su['memory_usage']} | {$su['disk_usage']}";
            send_markdown($msg, $webhook, "version");
        }
        
        if ($globalmessage == "/bot_start") {
            if (permission_check("bot.start", $guserarr["uid"])) {
                write_to_file_json("data/bot/run.json", ["start" => true]);
                send_message("真是美好的一天呢~\nbot start", $webhook, $staffid);
                exit();
            } else {
                send_message('Access denied', $webhook, $staffid);
                exit();
            }
        }

        if ($globalmessage == "/bot_close") {
            if (permission_check("bot.close", $guserarr["uid"])) {
                write_to_file_json("data/bot/run.json", ["start" => false]);
                send_message("有点...困了...\nbot closed", $webhook, $staffid);
                exit();
            } else {
                send_message('Access denied', $webhook, $staffid);
                exit();
            }
        }
        
        if (strpos($globalmessage, "/plugin") === 0) {
            $pluginDir = 'plugin';
            if (!permission_check("bot.plugin", $guserarr["uid"])) {
                send_message("Access denied", $webhook, $staffid);
                exit();
            }
            $result = stringf($globalmessage);
            $todo = $result["params"][1];
            $f = $result["params"][2];
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
            send_message($r, $webhook);
        }
    }
}