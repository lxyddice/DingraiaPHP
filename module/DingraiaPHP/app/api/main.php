<?php
if ($bot_run_as) {
    if (isset($_GET['action']) && $_GET['action'] == "api") {
        $_GET['type'] = $_GET['type']?? "";
        $bot_run_as["useDefaultDisplayPage"] = false;
        $hideLoadPluginInfo = 1;
        header('Content-Type: application/json');
        $apiResponse = ["success"=>false,"code"=>404,"message"=>"API端点不存在","result"=>null,"request_id"=>$bot_run_as["RUN_ID"]];
        /*无授权API*/
        if ($_GET['type'] == 'version') {
            $data = read_file_to_array('config/bot.json')['dingraia_php_version'];
            $apiResponse["code"] = 0;
            $apiResponse["result"] = $data;
        }
        
        if ($_GET["type"] == 'cronSurvival') {
            if (file_exists("data/bot/cron/alive.lock")) {
                $pidToCheck = file_get_contents("data/bot/cron/alive.lock");
                if (posix_getpgid($pidToCheck)) {
                    $apiResponse["code"] = 0;
                    $apiResponse["result"] = ["cron"=>true,"pid"=>$pidToCheck, "survival"=>true];
                } else {
                    $apiResponse["code"] = 0;
                    $apiResponse["result"] = ["cron"=>true,"pid"=>$pidToCheck, "survival"=>false];
                }
            } else {
                $apiResponse["code"] = 0;
                $apiResponse["result"] = ["cron"=>false,"pid"=>$pidToCheck, "survival"=>false];
            }
        }
        
        if ($_GET["type"] == "getIp") {
            if (DIngraiaPHPGetIp()) {
                $apiResponse["code"] = 0;
                $apiResponse["result"] = DIngraiaPHPGetIp();
            } else {
                $apiResponse["success"] = false;
                $apiResponse["code"] = -8;
            }
        }
        
        if ($_GET["type"] == "getShortUrlInfo") {
            $id = $_GET["id"];
            if (isset($_GET["id"])) {
                if (file_exists("data/bot/app/shortUrl/sid/{$id}.json")) {
                    $f = read_file_to_array("data/bot/app/shortUrl/sid/{$id}.json");
                    $apiResponse["code"] = 0;
                    $apiResponse["result"] = ["url"=>base64_decode($f["url"])];
                } else {
                    $apiResponse["success"] = false;
                    $apiResponse["code"] = -6;
                }
            } else {
                $apiResponse["success"] = false;
                $apiResponse["code"] = -2;
            }
        }
        
        if ($_GET['type'] == "searchUser") {
            $n = trim($_GET['name']);
            if ($n == "") {
                $apiResponse["success"] = false;
                $apiResponse['code'] = -3;
            } elseif (containsDangerousChars_a_1($n)) {
                $apiResponse["success"] = false;
                $apiResponse['code'] = -2;
            } else {
                $searchName = "%{$n}%";
                $searchName = str_replace("_", "\\_", $searchName);
                $sql = "SELECT * FROM dingbotuid WHERE name LIKE ?";
                $stmt = $con->prepare($sql);
                $stmt->bind_param("s", $searchName);
                $stmt->execute();
                $result = $stmt->get_result();
                $out = [];
                while ($row = $result->fetch_assoc()) {
                    $out[] = ["uid"=>$row["uid"] ,"name"=>$row['name']];
                }
                $apiResponse["result"] = $out;
                $apiResponse["code"] = 0;
            }
        }
        if ($_GET['type'] == "getCronTask") {
            if (file_exists("data/bot/cron/tasks.json")) {
                $apiResponse["code"] = 0;
                $apiResponse["result"] = read_file_to_array("data/bot/cron/tasks.json");
            } else {
                $apiResponse["success"] = false;
                $apiResponse["code"] = -6;
            }
        }
        if ($_GET['type'] == "runCronTask") {
            $task = $_GET["task"];
            require_once("data/bot/cron/plugin/1.php");
            send_message("收到{$task}","https://oapi.dingtalk.com/robot/send?access_token=ef8f15f1542ff3364ef3cfc6045cca30369cf0d49cf1f97751faffbe889242cc");
            if (function_exists($task)) {
                $task();
                $apiResponse["code"] = 0;
            } else {
                $apiResponse["success"] = false;
                $apiResponse["code"] = -6;
            }
        }
        if ($_GET['type'] == "getLogger") {
            function readAndDeleteJsonFiles_xfjehdalfruhs($directory) {
                $files = glob($directory . '/*.json');
                $jsonData = [];
            
                foreach ($files as $file) {
                    if (is_file($file)) {
                        $fileName = basename($file);
                        $fileContent = file_get_contents($file);
                        $jsonData[$fileName] = json_decode($fileContent, true);
                        unlink($file); // 删除文件
                    }
                }
            
                return $jsonData;
            }
            
            $directory = 'data/bot/logger/c';
            $jsonData = readAndDeleteJsonFiles_xfjehdalfruhs($directory);
            $apiResponse["code"] = 0;
            $apiResponse["result"] = $jsonData;
        }
        if ($_GET['type'] == "tools.uid2name") {
            $directory = 'data/bot/user/';
            $files = scandir($directory);
            foreach ($files as $file) {
                if ($file != "." && $file != "..") {
                    $filePath = $directory . $file;
                    if (is_file($filePath) && pathinfo($filePath, PATHINFO_EXTENSION) == 'json') {
                        $jsonContent = file_get_contents($filePath);
                        $data = json_decode($jsonContent, true);
                        if (isset($data['uid']) && isset($data['name'])) {
                            $uid = $data['uid'];
                            $name = $data['name'];
                            $resultData[$uid] = $name;
                        }
                    }
                }
            }
            #write_to_file_json("data/bot/user/uid2name.json",$resultData);
            $apiResponse["code"] = 0;
            $apiResponse["result"] = $resultData;
        }
        require_once(__DIR__."/htmlAdmin.php");
        require_once(__DIR__."/lxyddice.php");
        require_once(__DIR__."/other.php");
        require_once(__DIR__."/githubOAuth2.php");
        require_once(__DIR__."/htmlPage.php");
        require_once(__DIR__."/lxyCurlPHP.php");
        require_once(__DIR__."/lxyAPP.php");
        require_once(__DIR__."/lxyTi.php");
        require_once(__DIR__."/lxyCraft.php");
        /*需验证签名的API*/
        if ($_GET['type'] == "oauth2Get") {
            if (isset($_GET['state']) && isset($_GET['DingraiaPHPState']) && isset($_GET['timeStamp']) && isset($_GET['sign'])) {
                $DingraiaPHPState = $_GET['DingraiaPHPState'];
                $state = $_GET['state'];
                $ts = $_GET['timeStamp'];
                $sign = $_GET['sign'];
                $f = read_file_to_array("data/bot/oauth2Login.json");
                $o = read_file_to_array("data/bot/oauth2.json");
                $tsign = hash('sha256', $DingraiaPHPState.$state.$ts.$bot_run_as['config']['dingraiaAuthKey']);
                if ($sign == $tsign && time() > $bot_run_as['config']['dingraiaAuthTimeout']) {
                    if (isset($f[$DingraiaPHPState]) && $o[$DingraiaPHPState]['state'] == $state) {
                        $r = read_file_to_array("data/bot/oauth2UsedState.json");
                        if (isset($r[$DingraiaPHPState])) {
                            $apiResponse['success'] = false;
                            $apiResponse['code'] = -5;
                            $apiResponse['tips'] = "此state已被使用，不支持再次调用啦~";
                        } else {
                            $apiResponse['code'] = 0;
                            $apiResponse['result'] = $f[$DingraiaPHPState];
                            $r[$DingraiaPHPState] = ["useTime"=>time()];
                            write_to_file_json("data/bot/oauth2UsedState.json", $r);
                        }
                    } else {
                        $g = read_file_to_array("data/bot/tempDingtalkLogin.json");
                        if (isset($g[$DingraiaPHPState])) {
                            $r = read_file_to_array("data/bot/oauth2UsedState.json");
                            if (isset($r[$DingraiaPHPState])) {
                                $apiResponse['success'] = false;
                                $apiResponse['code'] = -5;
                                $apiResponse['tips'] = "此state已被使用，不支持再次调用啦~";
                            } else {
                                $apiResponse['code'] = 0;
                                $apiResponse['result'] = ["nick"=>$g[$DingraiaPHPState]["result"]["name"],"unionId"=>$g[$DingraiaPHPState]["result"]["unionid"],"avatarUrl"=>$g[$DingraiaPHPState]["result"]["avatar"],"mobile"=>$g[$DingraiaPHPState]["result"]["mobile"],"stateCode"=>$g[$DingraiaPHPState]["result"]["state_code"],"allResult"=>$g[$DingraiaPHPState]["result"]];
                                $r[$DingraiaPHPState] = ["useTime"=>time()];
                                write_to_file_json("data/bot/oauth2UsedState.json", $r);
                            }
                        } else {
                            $apiResponse['success'] = false;
                            $apiResponse['code'] = -8;
                        }
                    }
                } else {
                    $apiResponse['success'] = false;
                    $apiResponse['code'] = -5;
                }
            } else {
                $apiResponse['success'] = false;
                $apiResponse['code'] = -2;
            }
        }
        
        if ($_GET['type'] == "uid2staffid" && isset($_GET['timeStamp']) && isset($_GET['sign'])) {
            $ts = $_GET['timeStamp'];
            $sign = $_GET['sign'];
            $f = read_file_to_array("data/bot/oauth2Login.json");
            $o = read_file_to_array("data/bot/oauth2.json");
            if (isset($f[$DingraiaPHPState]) && $o[$DingraiaPHPState]['state'] == $state) {
                $tsign = hash('sha256', $ts.$bot_run_as['config']['dingraiaAuthKey']);
                if ($sign == $tsign) {
                    $apiResponse['code'] = 0;
                    $apiResponse['result'] = $f[$DingraiaPHPState];
                } else {
                    $apiResponse['success'] = false;
                    $apiResponse['code'] = -5;
                }
            } else {
                $apiResponse['success'] = false;
                $apiResponse['code'] = -8;
            }
        }
        
        if ($_GET['type'] == "getPlugnList") {
            if (is_dir($pluginDir)) {
                $files = scandir($pluginDir);
                foreach ($files as $file) {
                    if (strpos($file, '.') !== 0 && pathinfo($file, PATHINFO_EXTENSION) === 'php') {
                        $filePath = $pluginDir . DIRECTORY_SEPARATOR . $file;
                        if (strpos($file, '_') === 0) {
                            $pluginarr['disable'][] = $file;
                        } else {
                            $pluginarr['enable'][] = $file;
                        }
                    }
                }
                $apiResponse['success'] = true;
                $apiResponse['code'] = 0;
                $apiResponse['result'] = $pluginarr;
            } else {
                $apiResponse['success'] = false;
                $apiResponse["code"] = -6;
            }
        }
        function faw884gv84saf49qw87f4q($str) {
            $length = strlen($str);
            if ($length <= 8) {
                return $str;
            }
            $prefix = substr($str, 0, 6);
            $masked_string = $prefix . str_repeat('*', 8);
            return $masked_string;
        }
        /*需传入密钥的*/
        $apiKey = "lxyddice233_14514";
        if ($_GET["type"] == "getTiGroupBanList") {
            if ($_GET["key"] == $apiKey) {
                $apiResponse["code"] = 0;
                $f = read_file_to_array("data/bot/apiPlugin/TiGroupBan/user.json");
                foreach ($f as $k) {
                    $ul[] = $k["result"]["userid"];
                }
                $apiResponse['result'] = ["userList"=>$ul,"allInfo"=>$f];
            } else {
                $apiResponse["code"] = -5;
            }
        }
        if ($_GET['type'] == 'getRunIdInfo') {
            if ($_GET["key"] == $apiKey) {
                $id = $_GET["id"];
                if (containsValidCharacters_2($id)) {
                    $f = "data/bot/log/run/{$id}.json";
                    if (file_exists($f)) {
                        $apiResponse["code"] = 0;
                        $d = read_file_to_array($f);
                        if (isJson($d["data"])) {
                            $d = json_decode($d["data"]);
                        }
                        $apiResponse["result"] = $d;
                    } else {
                        $dir = substr($id, 0, 5);
                        $f = "data/bot/log/run/{$dir}/{$id}.json";
                        if (file_exists($f)) {
                            $apiResponse["code"] = 0;
                            $d = read_file_to_array($f);
                            if (isJson($d["data"])) {
                                $d = json_decode($d["data"]);
                            }
                            $apiResponse["result"] = $d;
                        } else {
                            $apiResponse["success"] = false;
                            $apiResponse["code"] = -6;
                        }
                    }
                } else {
                    $apiResponse["success"] = false;
                    $apiResponse["code"] = -1;
                }
            } else {
                $apiResponse["code"] = -5;
            }
        }
        if ($_GET["type"] == "getLog") {
            if ($_GET["key"] == $apiKey) {
                $id = $_GET["id"];
                if (containsValidCharacters_1($id)) {
                    $f = "data/bot/error_log/logs/{$_GET['id']}.json";
                    if (file_exists($f)) {
                        $apiResponse["success"] = true;
                        $apiResponse["code"] = 0;
                        $d = read_file_to_array($f);
                        $d = $d["data"];
                        $apiResponse["result"] = $d;
                    } else {
                        $apiResponse["success"] = false;
                        $apiResponse["code"] = -6;
                    }
                } else {
                    $apiResponse["success"] = false;
                    $apiResponse["code"] = -1;
                }
            } else {
                $apiResponse["code"] = -5;
            }
        }
        
        if ($apiResponse["code"] < 1) {
            if ($apiResponse["code"] == 0) {
                $apiResponse["success"] = true;
                $apiResponse["message"] = "API调用成功了喵~";
            }
            if ($apiResponse["code"] == -1) {
                $apiResponse["message"] = "baka！大人是不是说了什么不该说的话喵>_<";
            }
            if ($apiResponse["code"] == -2) {
                $apiResponse["message"] = "冰晶不懂，帮顶...是真的不懂啦~您是不是少说了什么呢？";
            }
            if ($apiResponse["code"] == -3) {
                $apiResponse["message"] = "好奇怪的请求呀...总之搬出来这个(Bad request)就可以了吧～";
            }
            if ($apiResponse["code"] == -4) {
                $apiResponse["message"] = "咦，方法错啦，杂鱼快认真看看文档喵！";
            }
            if ($apiResponse["code"] == -5) {
                $apiResponse["message"] = "冰晶拒绝大人的请求(//̀Д/́/)";
            }
            if ($apiResponse["code"] == -6) {
                $apiResponse["message"] = "（翻找）没...没找到诶？TAT";
            }
            if ($apiResponse["code"] == -7) {
                $apiResponse["message"] = "抱歉啦~您的权限不够呢(。﹏。)";
            }
            if ($apiResponse["code"] == -8) {
                $apiResponse["message"] = "好像...好像出了点小意外呢，请让冰晶自己思考一下，果咩纳塞！（＞人＜；）";
            }
            if ($apiResponse["code"] == -9) {
                $apiResponse["message"] = "请......求......超......时......（咕噜咕噜）";
            }
            if ($apiResponse["code"] == -10) {
                $apiResponse["message"] = "抱歉，真的出现了一些错误！这是可能存在的错误ID：{$apiWorngId}";
            }
            $apiResponse["apiPath"] = $_GET["type"];
        }
        if (!$bot_run_as["useDefaultDisplayPage"]) {
            $r = write_to_file_json("data/bot/app/response.json", ["type"=>"no"]);
            $bot_run_as["responseMustTypeText"] = "no";
            $bot_run_as["responseMustType"] = 1;
            echo(json_encode($apiResponse, JSON_UNESCAPED_UNICODE));
            app_json_file_add_list($bot_run_as["RUN_LOG_FILE"], ["time"=>microtime(true),"type"=>"apiResponse","result"=>$apiResponse]);
        }
    }
}
?>