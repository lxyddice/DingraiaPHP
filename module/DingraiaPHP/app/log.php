<?php
function DingraiaPHPLogDisposeMainFn() {
    $r = read_file_to_array("data/bot/app_data.json");
    $ymd = date('Y-m-d');
    
    // 处理 log
    if ($ymd != $r['log']['last_dispose_time'] && file_exists("data/get.json")) {
        rename("data/get.json", "data/bot/log/get/{$ymd}.json");
        write_to_file_json('data/get.json', []);
        $r['log']['last_dispose_time'] = $ymd;
    }
    
    // 处理 callback
    if ($ymd != $r['callback']['last_dispose_time'] && file_exists("data/callback.json")) {
        if (!is_dir("data/bot/log/callback")) {
            mkdir("data/bot/log/callback", 0777, true);
        }
        $cacheDir = "data/bot/cache/callback/";
        $cacheFiles = scandir($cacheDir);
        $cacheFiles = array_diff($cacheFiles, array('..', '.'));
        $callbackData = [];
        foreach ($cacheFiles as $cacheFile) {
            $cacheFile = str_replace(".json", "", $cacheFile);
            $cacheYMD = stringf($cacheFile, ",")["params"][1];
            $callbackData[$cacheYMD][] = read_file_to_array("data/bot/cache/callback/{$cacheFile}.json");
            unlink("data/bot/cache/callback/{$cacheFile}.json");
        }
        foreach ($callbackData as $callbackYMD => $callbackData) {
            write_to_file_json("data/bot/log/callback/{$callbackYMD}.json", $callbackData);
        }
        $r['callback']['last_dispose_time'] = $ymd;
        write_to_file_json('data/callback.json', []);
    }
    
    // 处理 send
    if ($ymd != $r['send']['last_dispose_time'] && file_exists("data/send.json")) {
        if (!is_dir("data/bot/log/send")) {
            mkdir("data/bot/log/send", 0777, true);
        }
        rename("data/send.json", "data/bot/log/send/{$ymd}.json");
        write_to_file_json('data/send.json', []);
        $r['send']['last_dispose_time'] = $ymd;
    }
    
    // 处理 group_send
    if ($ymd != $r['group_send']['last_dispose_time'] && file_exists("data/group_send.json")) {
        if (!is_dir("data/bot/log/group_send")) {
            mkdir("data/bot/log/group_send", 0777, true);
        }
        rename("data/group_send.json", "data/bot/log/group_send/{$ymd}.json");
        write_to_file_json('data/group_send.json', []);
        $r['group_send']['last_dispose_time'] = $ymd;
    }
    
    // 统一更新配置文件
    write_to_file_json("data/bot/app_data.json", $r);
}
