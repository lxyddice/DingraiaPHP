<?php
/*
/***
 *_______________#########_______________________
 *______________############_____________________
 *______________#############____________________
 *_____________##__###########___________________
 *____________###__######_#####__________________
 *____________###_#######___####_________________
 *___________###__##########_####________________
 *__________####__###########_####_______________
 *________#####___###########__#####_____________
 *_______######___###_########___#####___________
 *_______#####___###___########___######_________
 *______######___###__###########___######_______
 *_____######___####_##############__######______
 *____#######__#####################_#######_____
 *____#######__##############################____
 *___#######__######_#################_#######___
 *___#######__######_######_#########___######___
 *___#######____##__######___######_____######___
 *___#######________######____#####_____#####____
 *____######________#####_____#####_____####_____
 *_____#####________####______#####_____###______
 *______#####______;###________###______#________
 *________##_______####________####______________
 */
/*
error_reporting(E_ERROR | E_WARNING | E_PARSE);
ini_set('display_errors', 1);
*/
global $bot_run_as;
require_once("tools.php");
$bot_run_as['config'] = read_file_to_array("config/bot.json");
// This code sucks, you know it and I know it.  
// Move on and call me an idiot later.
// 你我都知道这代码很烂
// 先不要骂我2B了，请先继续往下看
$c = get_dingtalk_post();
write_to_file_json('log.json',$c);
if ($c && $c['v'] != true) {
    $c = $c[0];
    if (isset($c['lxy_mode']) && $c['lxy_mode'] == 'card_callback') {
        $conversationId = $openConversationId = $c['openConversationId'];
        $chatbotCorpId = $c['chatbotCorpId'];
        $bot_run_as['outTrackId'] = $c['outTrackId'];
        $bot_run_as['userId'] = $c['userId'];
        $bot_run_as['content'] = json_decode($c['content'],true);
        $bot_run_as['chat_mode'] = 'card_callback';
    } elseif (isset($c['dingraia']) && $c['dingraia'] == 'master') {
        $c = $bot_run_as['callback'];
        $bot_run_as['chat_mode'] = "dingraia_master";
    } elseif (isset($c['chat_mode']) && $c['chat_mode'] == "cb" ||  $c['chat_mode'] == "mcb") {
        $bot_run_as['chat_mode'] = 'cb';
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
    } else {
        $DingraiaPHPGet = $c;
    }
} 
//$globalmessage = "/me";
$globalFunctionArray = read_file_to_array("config/bot.json")["global_function"];
foreach ($globalFunctionArray as $functionName) {
    global $$functionName;
}

$bot_run_as['api_url'] = "https://api.lxyddice.top/dingbot/php/api";
$bot_run_as['webhook'] = $webhook;
$directAccess = false;


tool_log(1,'bot_run');

if ($globalmessage == "/groupid") {
    send_message($conversationId,$webhook,$staffid);
    exit();
}

$con = mysqli_connect($db_host, $db_user, $db_pass, $db_name);
if (isset($userid)) {
    $guserarr = userid2uid($userid);
    $banStatus = checkUserBanStatus($userid,$guserarr);

    if ($banStatus != "没有封禁，继续执行") {
        if (in_array($guserarr['uid'], [])) {
            exit();
        }
        if ($banStatus != "冰晶再也不想和你聊天惹...\nYou are permanently banned from lxybot") {
            send_message($banStatus, $webhook, $staffid);
            exit();
        }
        exit();
    }
    
}
/*
if ($globalmessage == "/bot d agree") {
        write_to_file_json("data/user/is_read_disclaimer/".$guserarr['uid'].".json",['read'=>true]);
        send_message('你同意了冰晶bot免责声明！',$webhook,$staffid);
    }
    
if (!is_read_disclaimer($guserarr['uid'])) {
    send_message("你没有同意冰晶bot免责声明，请用/bot d agree  同意\n你可以在https://mc.lxyddice.top/lxe/bot  查看",$webhook,$staffid);
    exit();
}
*/  
if ($globalmessage == "/bot_start") {
    if (permission_check("bot.start", $guserarr["uid"])) {
        write_to_file_json("data/bot/run.json",["start"=>true]);
        send_message("真是美好的一天呢~\nbot start",$webhook,$staffid);
        exit();
    } else {
        send_message('Access denied', $webhook, $staffid);
        exit();
    }
}

if ($globalmessage == "/bot_close") {
    if (permission_check("bot.close", $guserarr["uid"])) {
        write_to_file_json("data/bot/run.json",["start"=>false]);
        send_message("有点...困了...\nbot closed",$webhook,$staffid);
        exit();
    } else {
        send_message('Access denied', $webhook, $staffid);
        exit();
    }
}

if (read_file_to_array("data/bot/run.json")['start'] != true) {
    send_message("冰晶正在睡觉...",$webhook,$staffid);
    exit();
}
$grouparr = read_file_to_array("config/group.json");
/*
if ($conversationType == 2) {
    $grouparr = read_file_to_array("config/group.json");
    if (!in_array($conversationId, $grouparr['white_list'])) {
        send_message("该群聊不在白名单，无法使用冰晶bot",$webhook,$staffid);
        exit();
    }
} else {
    if (!permission_check("bot.siliao", $guserarr["uid"])) {
        send_message("你没有私聊的权限",$webhook,$staffid);
        exit();
    }
}
*/


tool_log(4,$bot_run_as);

$pluginDir = 'plugin';
set_error_handler("customErrorHandler");
header("HTTP/1.1 {$bot_run_as['config']['index_return']}");
if ($bot_run_as['config']['index_hide_load'] == "1" && $bot_run_as['config']['index_return'] != 200) {
    require_once($bot_run_as['config']['index_reveal']);
}
if ($bot_run_as['config']['index_not_load'] == 0) {
    if (is_dir($pluginDir)) {
        $files = scandir($pluginDir);
        $pluginarr = [];
        foreach ($files as $file) {
            if (strpos($file, '.') !== 0 && strpos($file, '_') !== 0 && pathinfo($file, PATHINFO_EXTENSION) === 'php') {
                $filePath = $pluginDir . DIRECTORY_SEPARATOR . $file;
                require_once $filePath;
                if ($bot_run_as['config']['index_hide_load'] == "0") {
                    if ($c['chat_mode'] != "cb" && $bot_run_as['echoLoadPlugins']) {
                        echo "成功加载" . $filePath . "<br>";
                    }
                }
                $pluginarr[] = $filePath;
            }
        }
    } else {
        echo "无效的插件文件夹路径：$pluginDir";
    }
}
    
tool_log(4,$c);
if (strpos($message, "/plugin") === 0) {
    $pluginDir = 'plugin';
    if (!permission_check("bot.plugin", $guserarr["uid"])) {
        send_message("Access denied",$webhook,$staffid);
        exit();
    }
    $result = stringf($message);
    $todo = $result["params"][1];
    $f = $result["params"][2];
    if (strpos($f,'_')!==false) {
        $r = "插件操作失败";
        send_message($r, $webhook);
        exit();
    }
    if ($todo == "enable") {
        $oldFileName = "_".$f.".php";
        $newFileName = $f.".php";
        $oldFilePath = $pluginDir . DIRECTORY_SEPARATOR . $oldFileName;
        $newFilePath = $pluginDir . DIRECTORY_SEPARATOR . $newFileName;
        if (file_exists($oldFilePath)) {
            if (rename($oldFilePath, $newFilePath)) {
                $r = "插件启用成功";
                tool_log(1,"$oldFilePath 插件禁用");
            } else {
                $r = "插件启用失败";
                tool_log(1,"$oldFilePath 插件启用");
            }
        } else {
            $r = "不存在此插件";
        }
    } elseif ($todo == "disable") {
        $oldFileName = $f.".php";
        $newFileName = "_".$f.".php";
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
        
            $jsonResult = json_encode($pluginarr, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE);
            send_message($jsonResult,$webhook);
        } else {
            $jsonResult = json_encode(array('error' => 'Invalid plugin folder path: ' . $pluginDir));
            send_message($jsonResult,$webhook,$staffid);
        }
    }
        send_message($r, $webhook);
}

?>