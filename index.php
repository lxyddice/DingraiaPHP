<?php
/*
  ____  _                       _       ____  _   _ ____
 |  _ \(_)_ __   __ _ _ __ __ _(_) __ _|  _ \| | | |  _ \
 | | | | | '_ \ / _` | '__/ _` | |/ _` | |_) | |_| | |_) |
 | |_| | | | | | (_| | | | (_| | | (_| |  __/|  _  |  __/
 |____/|_|_| |_|\__, |_|  \__,_|_|\__,_|_|   |_| |_|_|
                |___/                       v240908.2-Alpha

error_reporting(E_ERROR | E_WARNING | E_PARSE);
ini_set('display_errors', 1);
*/

global $bot_run_as;
$bot_run_as = [];

    require_once("module/DingraiaPHP/app/start.php");
    require_once("tools.php");
    app_start();

    $bot_run_as["indexDir"] = dirname(__FILE__);
    ob_start();
    header("X-Content-Type-Options: nosniff");
    define("DingraiaPHP_APP_CHECK", "1");
    app_json_file_add_list($bot_run_as["RUN_LOG_FILE"], ["time" => microtime(), "type" => "start", "run_fn" => $bot_run_as]);
    $c = get_dingtalk_post();
    $c = isset($c[0]) ? $c[0] : null;
    if (isset($c['lxy_mode']) && $c['lxy_mode'] == 'card_callback') {
        $conversationId = $openConversationId = $c['openConversationId'];
        $chatbotCorpId = $c['chatbotCorpId'];
        $bot_run_as['outTrackId'] = $c['outTrackId'];
        $bot_run_as['userId'] = $c['userId'];
        $bot_run_as['content'] = json_decode($c['content'], true);
        $bot_run_as['chat_mode'] = 'card_callback';
    } elseif (isset($c['dingraia']) && $c['dingraia'] == 'master') {
        $c = $bot_run_as['callback'];
        $bot_run_as['chat_mode'] = "dingraia_master";
    } elseif (isset($c['chat_mode'])) {
        if ($c['chat_mode'] == "cb" || $c['chat_mode'] == "mcb") {
            $bot_run_as['chat_mode'] = 'cb';
            $bot_run_as['callbackContent'] = $c['callbackContent']['callback'];
            $bot_run_as['appInfo'] = $c['callbackContent']['appInfo'];
            $bot_run_as['config']['index_return'] = 200;
        }
    } elseif (isset($c['chat_mode']) && $c['chat_mode'] == 'tgbot') {
        $bot_run_as['body'] = $c;
        $bot_run_as['chat_mode'] = 'tgbot';
        $bot_run_as['chatId'] = $c['message']['chat']['id'];
    } elseif (isset($c['body']['userid'])) {
        $message = str_replace('\n', "", $c['body']['message']);
        $userid = $c['body']['userid'];
        $webhook = $c['body']['webhook'];
        $staffid = $c['body']['senderStaffId'];
        $groupnanme = $c['body']['conversationTitle'];
        $name = $c['body']['name'];
        $conversationType = $c['body']['conversationType'];
        $conversationId = $c['body']['conversationId'];
        $chatbotCorpId = $c['body']['chatbotCorpId'];
        $robotCode = $c['body']['robotCode'];
        $atUsers = $c['body']['atUsers'];
        $messagetype = isset($c['body']['messagetype']) ? $c['body']['messagetype'] : "text";
        $messagedownloadCode = isset($c['body']['downloadCode']) ? $c['body']['downloadCode'] : "";
        $messagecontent = isset($c['body']['messagecontent']) ? $c['body']['messagecontent'] : "";
        $globalmessage = $message;
        $restaffid = $staffid;
        $rename = htmlspecialchars($name);
        $uid = userid2uid($userid)['uid'];
        $bot_run_as["logger"]["class"]->info("[$groupnanme($conversationId)] $name($uid) -> $message");
    } else {
        $DingraiaPHPGet = $c;
    }
    app_json_file_add_list($bot_run_as["RUN_LOG_FILE"], ["time" => microtime(), "type" => "start", "run" => 'ok']);
    if (isset($userid) && isset($staffid)) {
        $guserarr = userid2uid($userid);
    }
    $globalFunctionArray = $bot_run_as["config"]["global_function"];
    foreach ($globalFunctionArray as $functionName) {
        global $$functionName;
    }

    $bot_run_as['api_url'] = "https://api.lxyddice.top/dingbot/php/api";
    $bot_run_as['webhook'] = $webhook;
    if (read_file_to_array("data/bot/run.json")['start'] != true) {
        //send_message("冰晶正在睡觉...",$webhook,$staffid);
        DingraiaPHPResponseExit(400, "Bot global stop");
    }
    $grouparr = read_file_to_array("config/group.json");
    $pluginDir = 'plugin';
    require_once("module/DingraiaPHP/app/requireRunPlugin.php");
    $DingraiaPHPresponse = read_file_to_array("data/bot/app/response.json");
    
    if ($bot_run_as["config"]["notLoadPlugins"] == 0) {
        if (is_dir($pluginDir)) {
            $files = scandir($pluginDir);
            $pluginarr = [];
            foreach ($files as $file) {
                if (strpos($file, '.') !== 0 && strpos($file, '_') !== 0 && pathinfo($file, PATHINFO_EXTENSION) === 'php') {
                    $filePath = $pluginDir . DIRECTORY_SEPARATOR . $file;
                    require_once $filePath;
                    $DingraiaPHPresponse["result"]["loadPlugins"]["success"] = true;
                    $DingraiaPHPresponse["result"]["loadPlugins"]["plugins"][] = $filePath;
                    $pluginarr[] = $filePath;
                }
            }
        }
    }
    if ($bot_run_as["config"]["hideAllEcho"] == 0) {
        if ($bot_run_as["config"]["useDefaultDisplayPage"] == 0) {
            write_to_file_json("data/bot/app/response.json", ["content"=>$DingraiaPHPresponse, "type"=>"json"]);
        } else {
            write_to_file_json("data/bot/app/response.json", ["content"=>file_get_contents($bot_run_as["config"]["indexReveal"]), "type"=>"html"]);
        }
    }
    /*
    聆听我的命令，强大的克尔莫。
    粉碎前方的障碍吧！
    等我完成最终的试炼，神的光辉会让你永远伟大！
    */
    /*再此，终结吧！*/
    require_once("module/DingraiaPHP/app/runEnd.php");