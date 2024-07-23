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
    global $bot_run_as;

    $bot_run_as["RUN_ID"] = time()."_".uuid_start();
    $bot_run_as["config"] = json_decode(file_get_contents("config/bot.json"), true);
    
    include_once(__DIR__."/logger.php");

    $dir = substr($bot_run_as['RUN_ID'], 0, 5);
    if (!is_dir("data/bot/log/run/{$dir}")) {
        if (!mkdir("data/bot/log/run/{$dir}", 0755, true)) {
            throw new Exception("Failed to create directory.");
        }
    }
    $bot_run_as["RUN_LOG_FILE"] = "data/bot/log/run/{$dir}/{$bot_run_as['RUN_ID']}.json";
    $bot_run_as["useDefaultDisplayPage"] = true;
    define('PLUGIN_DIR', 'plugin');
    define('PLUGIN_FILE_EXTENSION', '.php');
    write_to_file_json("data/bot/app/response.json", []);

    $bot_run_as["schedule"] = "start";
    app_json_file_add_list($bot_run_as["RUN_LOG_FILE"],["ok"=>true]);


    write_to_file_json($bot_run_as["RUN_LOG_FILE"], []);
}
function caGetUrl($caller) {
    return (str_replace(__DIR__, "", $caller["file"]));
}