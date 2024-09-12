<?php

function  uuid_start()  {  
    $chars = md5(uniqid(mt_rand(), true));  
    $uuid = substr ( $chars, 0, 8 ) . '-'
            . substr ( $chars, 8, 4 ) . '-' 
            . substr ( $chars, 12, 4 ) . '-'
            . substr ( $chars, 16, 4 ) . '-'
            . substr ( $chars, 20, 12 );  
    return $uuid ;  
}
function app_start() {
    global $bot;

    $bot["RUN_ID"] = time()."_".uuid();
    $bot["config"] = json_decode(file_get_contents("config/bot.json"), true);
    
    if (file_exists("config/lang/{$bot['config']['lang']}.json")) {
        $bot["lang"] = json_decode(file_get_contents("config/lang/{$bot['config']['lang']}.json"), true);
    } else {
        exit("无法获取语言");
    }
    
    include_once(__DIR__."/logger.php");

    $dir = substr($bot['RUN_ID'], 0, 5);
    if (!is_dir("data/bot/log/run/{$dir}")) {
        if (!mkdir("data/bot/log/run/{$dir}", 0755, true)) {
            throw new Exception("Failed to create directory.");
        }
    }
    $bot["RUN_LOG_FILE"] = "data/bot/log/run/{$dir}/{$bot['RUN_ID']}.json";
    $bot["useDefaultDisplayPage"] = true;
    $bot["chat_mode"] = "normal";
    define('PLUGIN_DIR', 'plugin');
    define('PLUGIN_FILE_EXTENSION', '.php');
    write_to_file_json("data/bot/app/response.json", []);

    $bot["schedule"] = "start";
    
    $bot["runIn"] = "DingraiaPHP";
    
    app_json_file_add_list($bot["RUN_LOG_FILE"],["ok"=>true]);
    
    write_to_file_json($bot["RUN_LOG_FILE"], []);

    
    $requireMoudle = "lxyddice";
    require_once("module/DingraiaPHP/app/autoload.php");
}

function caGetUrl($caller) {
    return (str_replace(__DIR__, "", $caller["file"]));
}