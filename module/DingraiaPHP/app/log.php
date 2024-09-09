<?php
function DingraiaPHPLogDisposeMainFn() {
    $r = read_file_to_array("data/bot/app_data.json");
    $ymd = date('Y-m-d');
    
    // 处理 log
    if (!isset($r['log'])) {
        $r['log'] = ['last_dispose_time' => ''];
    }
    if (!isset($r['callback'])) {
        $r['callback'] = ['last_dispose_time' => ''];
    }
    if (!isset($r['send'])) {
        $r['send'] = ['last_dispose_time' => ''];
    }
    if (!isset($r['group_send'])) {
        $r['group_send'] = ['last_dispose_time' => ''];
    }
    
    if ($ymd != $r['log']['last_dispose_time'] && file_exists("data/get.json")) {
        copy("data/get.json", "data/bot/log/get/{$ymd}.json");
        write_to_file_json('data/get.json', []);
        $r['log']['last_dispose_time'] = $ymd;
    }
    
    // 处理 callback
    if ($ymd != $r['callback']['last_dispose_time'] && file_exists("data/callback.json")) {
        if (!is_dir("data/bot/log/callback")) {
            mkdir("data/bot/log/callback", 0777, true);
        }
        copy("data/callback.json", "data/bot/log/callback/{$ymd}.json");
        write_to_file_json('data/callback.json', []);
        $r['callback']['last_dispose_time'] = $ymd;
        
        $cacheDir = 'data/bot/cache/';
        $logDir = 'data/bot/log/callback/';
        $files = glob($cacheDir . "callback_{$ymd}*.json");  // 修正了路径拼接错误
        usort($files, function($a, $b) {
            return filemtime($a) <=> filemtime($b);
        });
        $logFile = "{$logDir}{$ymd}_Beta.json";
        $logData = [];
        foreach ($files as $file) {
            $jsonData = json_decode(file_get_contents($file), true);
            if ($jsonData) {
                $logData[] = $jsonData;
            }
            unlink($file);
        }
        if (!empty($logData)) {
            file_put_contents($logFile, json_encode($logData));
            DingraiaPHPAddNormalResponse("clearCache", ["type" => "callback", "path" => $logFile], true);  // 移动至循环外
        }
    }
    
    // 处理 send
    if ($ymd != $r['send']['last_dispose_time'] && file_exists("data/send.json")) {
        if (!is_dir("data/bot/log/send")) {
            mkdir("data/bot/log/send", 0777, true);
        }
        copy("data/send.json", "data/bot/log/send/{$ymd}.json");
        write_to_file_json('data/send.json', []);
        $r['send']['last_dispose_time'] = $ymd;
    }
    
    // 处理 group_send
    if ($ymd != $r['group_send']['last_dispose_time'] && file_exists("data/group_send.json")) {
        if (!is_dir("data/bot/log/group_send")) {
            mkdir("data/bot/log/group_send", 0777, true);
        }
        copy("data/group_send.json", "data/bot/log/group_send/{$ymd}.json");
        write_to_file_json('data/group_send.json', []);
        $r['group_send']['last_dispose_time'] = $ymd;
    }
    
    // 统一更新配置文件
    write_to_file_json("data/bot/app_data.json", $r);
}
