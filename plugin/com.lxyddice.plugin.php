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
        $cpuUsage = getCpuUsage();
        $memoryUsage = getMemoryUsage();
        $diskUsage = getDiskUsage();
    
        return [
            'cpu_usage' => number_format($cpuUsage, 2),
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
        $su = getServerUsage();
        $msg .= "\n\ncpu|ram|rom->{$su['cpu_usage']} | {$su['memory_usage']} | {$su['disk_usage']}";
        send_markdown($msg, $webhook, "version");
    }
}