<?php
/*
  ____  _                       _       ____  _   _ ____
 |  _ \(_)_ __   __ _ _ __ __ _(_) __ _|  _ \| | | |  _ \
 | | | | | '_ \ / _` | '__/ _` | |/ _` | |_) | |_| | |_) |
 | |_| | | | | | (_| | | | (_| | | (_| |  __/|  _  |  __/
 |____/|_|_| |_|\__, |_|  \__,_|_|\__,_|_|   |_| |_|_|
                |___/                       v240723-Alpha
*/

global $bot_run_as;
$bot_run_as = [];
//有些注释掉的代码真的很老，动不了了
    require_once("module/DingraiaPHP/app/start.php");
    require_once("tools.php");
    app_start();

    $bot_run_as["indexDir"] = dirname(__FILE__);
    ob_start();
    header("X-Content-Type-Options: nosniff");
    define("DingraiaPHP_APP_CHECK", "1");
    app_json_file_add_list($bot_run_as["RUN_LOG_FILE"], ["time" => microtime(), "type" => "start", "run_fn" => $bot_run_as]);
    $c = get_dingtalk_post();
//    $bot_run_as["logger"]->info("ok");
    if ($hideLoadPluginInfo_B == 1) {
        $hideLoadPluginInfo = 1;
    } else {
        //$hideLoadPluginInfo = $bot_run_as['config']['index_hide_load'];
    }
    if ($c && $c['v'] != true) {
        $c = $c[0];
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
        } elseif (isset($c['chat_mode']) && $c['chat_mode'] == "cb" || $c['chat_mode'] == "mcb") {
            $bot_run_as['chat_mode'] = 'cb';
            $hideLoadPluginInfo = 1;
            $bot_run_as['callbackContent'] = $c['callbackContent']['callback'];
            $bot_run_as['appInfo'] = $c['callbackContent']['appInfo'];
            $bot_run_as['config']['index_return'] = 200;
        } elseif (isset($c['chat_mode']) && $c['chat_mode'] == 'tgbot') {
            $bot_run_as['body'] = $c;
            $bot_run_as['chat_mode'] = 'tgbot';
            $bot_run_as['chatId'] = $c['message']['chat']['id'];
        } elseif (isset($c['body']['userid'])) {
            $message = str_replace('\n', "", $c['body']['message']);
            $userid = $c['body']['userid'];
            $robotid = $c['body']['robot_id'];
            $webhook = $c['body']['webhook'];
            $staffid = $c['body']['senderStaffId'];
            $groupnanme = $c['body']['conversationTitle'];
            $name = $c['body']['name'];
            $conversationType = $c['body']['conversationType'];
            $conversationId = $c['body']['conversationId'];
            $chatbotCorpId = $c['body']['chatbotCorpId'];
            $robotCode = $c['body']['robotCode'];
            $atUsers = $c['body']['atUsers'];
            $messagetype = $c['body']['msgtype'];
            $messagedownloadCode = $c['body']['downloadCode'];
            $messagecontent = $c['body']['content'];
            $globalmessage = $message;
            $restaffid = $staffid;
            $bot_run_as[$groupmessage_send_to_user] == false;
            $rename = htmlspecialchars($name);
            $uid = userid2uid($userid)['uid'];
            $bot_run_as["logger"]["class"]->info("[$groupnanme($conversationId)] $name($uid) -> $message");
        } else {
            $DingraiaPHPGet = $c;
        }
    }
    app_json_file_add_list($bot_run_as["RUN_LOG_FILE"], ["time" => microtime(), "type" => "start", "run" => 'ok']);
    // require_once("module/DingraiaPHP/app/accountLinkage.php");
    if ($userid && $staffid) {
        $guserarr = userid2uid($userid);
    }
    $globalFunctionArray = $bot_run_as["config"]["global_function"];
    foreach ($globalFunctionArray as $functionName) {
        global $$functionName;
    }

    $bot_run_as['api_url'] = "https://api.lxyddice.top/dingbot/php/api";
    $bot_run_as['webhook'] = $webhook;
    $directAccess = false;

    if ($globalmessage == "/groupid") {
        send_message($conversationId, $webhook, $staffid);
        exit();
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


    if (read_file_to_array("data/bot/run.json")['start'] != true) {
        DingraiaPHPResponseExit(400, "Bot global stop");
    }
    $grouparr = read_file_to_array("config/group.json");
    $pluginDir = 'plugin';
    require_once("module/DingraiaPHP/app/requireRunPlugin.php");
    $DingraiaPHPresponse = read_file_to_array("data/bot/app/response.json");
    if ($bot_run_as['config']['index_not_load'] == 0) {
        if (is_dir($pluginDir)) {
            $files = scandir($pluginDir);
            $pluginarr = [];
            foreach ($files as $file) {
                if (strpos($file, '.') !== 0 && strpos($file, '_') !== 0 && pathinfo($file, PATHINFO_EXTENSION) === 'php') {
                    $filePath = $pluginDir . DIRECTORY_SEPARATOR . $file;
                    require_once $filePath;
                    if ($c['chat_mode'] != "cb" && $bot_run_as['echoLoadPlugins']) {
                        $DingraiaPHPresponse["result"]["loadPlugins"]["success"] = true;
                        $DingraiaPHPresponse["result"]["loadPlugins"]["plugins"][] = $filePath;
                        $prlcb = true;
                    } else {
                        $prlcb = false;
                    }
                    $pluginarr[] = $filePath;
                }
            }
            if ($prlcb) {
                $DingraiaPHPresponse["request_id"] = $bot_run_as['RUN_ID'];
            }
        } else {
            $DingraiaPHPresponse["result"]["loadPlugins"]["success"] = false;
            $DingraiaPHPresponse["result"]["loadPlugins"]["msg"] = "无法载入插件文件夹...";
        }
        if ($DingraiaPHPresponse) {
            if ($bot_run_as['config']['indexDefault'] == "1") {
                write_to_file_json("data/bot/app/response.json", ["type" => "html", "content" => file_get_contents($bot_run_as['config']['index_reveal'])]);
            } else {
                write_to_file_json("data/bot/app/response.json", ["type" => "json", "content" => $DingraiaPHPresponse]);
            }
        }
    }

    if (strpos($message, "/plugin") === 0) {
        $pluginDir = 'plugin';
        if (!permission_check("bot.plugin", $guserarr["uid"])) {
            send_message("Access denied", $webhook, $staffid);
            exit();
        }
        $result = stringf($message);
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

    /*
    聆听我的命令，强大的克尔莫。
    粉碎前方的障碍吧！
    等我完成最终的试炼，神的光辉会让你永远伟大！
    */
    /*再此，终结吧！*/

    require_once("module/DingraiaPHP/app/runEnd.php");