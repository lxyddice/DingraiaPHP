<?php
function DingraiaPHPLogDisposeMainFn() {
    $r = read_file_to_array("data/bot/app_data.json");
    $logLog = $r['log'];
    $ymd = date('Y-m-d');
    if ($ymd != $logLog['last_dispose_time'] && file_exists("data/get.json")) {
        rename("data/get.json", "data/bot/log/get/".$ymd.".json");
        write_to_file_json('data/get.json',[]);
        $r['log']['last_dispose_time'] = $ymd;
        write_to_file_json("data/bot/app_data.json", $r);
    }
    $logLog = $r['callback'];
    $ymd = date('Y-m-d');
    $r = read_file_to_array("data/bot/app_data.json");
    if ($ymd != $logLog['last_dispose_time'] && file_exists("data/callback.json")) {
        rename("data/callback.json", "data/bot/log/callback/".$ymd.".json");
        write_to_file_json('data/callback.json',[]);
        $r['callback']['last_dispose_time'] = $ymd;
        $cacheDir = 'data/bot/cache/';
        $logDir = 'data/bot/log/callback/';
        $files = glob($cacheDir . "callback,{$ymd}*.json");
        usort($files, function($a, $b) {
            return filemtime($a) <=> filemtime($b);
        });
        $logFile = "{$logDir}{$ymd}_Beta.json";
        $logData = [];
        foreach ($files as $file) {
            $content = file_get_contents($file);
            $jsonData = json_decode($content, true);
            if ($jsonData) {
                $logData[] = $jsonData;
            }
            unlink($file);
            DingraiaPHPAddNormalResponse("clearCache",["type"=>"callback","path"=>$logFile], true);
        }
        if (!empty($logData)) {
            file_put_contents($logFile, json_encode($logData));
        }
        write_to_file_json("data/bot/app_data.json", $r);
    }
    $logLog = $r['send'];
    $ymd = date('Y-m-d');
    $r = read_file_to_array("data/bot/app_data.json");
    if ($ymd != $logLog['last_dispose_time'] && file_exists("data/send.json")) {
        rename("data/send.json", "data/bot/log/send/".$ymd.".json");
        write_to_file_json('data/send.json',[]);
        $r['send']['last_dispose_time'] = $ymd;
        write_to_file_json("data/bot/app_data.json", $r);
    }
    $logLog = $r['group_send'];
    $ymd = date('Y-m-d');
    $r = read_file_to_array("data/bot/app_data.json");
    if ($ymd != $logLog['last_dispose_time'] && file_exists("data/group_send.json")) {
        rename("data/group_send.json", "data/bot/log/group_send/".$ymd.".json");
        write_to_file_json('data/group_send.json',[]);
        $r['group_send']['last_dispose_time'] = $ymd;
        write_to_file_json("data/bot/app_data.json", $r);
    }
}