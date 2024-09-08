<?php
if (file_exists("fn.php")) {
    require_once("fn.php");
} elseif (file_exists("module/DingraiaPHP/app/admin/fn.php")) {
    require_once("module/DingraiaPHP/app/admin/fn.php");
} else {
    exit("Can't find toolkit,are you install DingraiaPHP?");
}
if ($bot_run_as) {
    session_start();
    
    $bot_run_as["responseMustTypeText"] = "no";
    $bot_run_as["responseMustType"] = 1;
    
    if (isset($_GET["return"])) {
        setcookie("return", $_GET["return"], 120);
    }
    if (DingraiaPHPHtmlAdmin_verifyLogin()) {
        session_start();
        if (isset($_GET["page"])) {
            $pageFile = read_file_to_array(__DIR__."/page.json");
            if (isset($pageFile[$_GET["page"]])) {
                require_once(__DIR__."/".$pageFile[$_GET["page"]]);
            } else {
                require_once(__DIR__."/".$pageFile["default"]);
            }
        } else {
            header("Location: ?action=p&page=index");
        }
    } elseif (isset($_GET["sign"])) {
        $f = read_file_to_array("data/bot/app/htmlAdminLogin.json");
        $sessionKey = read_file_to_array("data/bot/app/htmlAdminSession.json");
        if (isset($f[$_GET["sign"]])) {
            $urn = $f[$_GET["sign"]]["username"];
            echo("Try login with ".$urn);
            $_SESSION["DingraiaPHPHtmlAdmin_name"] = $urn;
            $loginTypeAccess = ["dingtalkOauth2","admin"];
            if (isset($_GET["loginType"])) {
                if (!in_array($_GET["loginType"], $loginTypeAccess)) {
                    header("Location: ?action=admin&returnTo=".urlencode("http://".$_SERVER["HTTP_HOST"].parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH)."?action=admin&page=index"));
                    exit();
                }
                $_SESSION["DingraiaPHPHtmlAdmin_loginUuid"] = $_GET["loginType"]."_".$urn;
            } else {
                $_SESSION["DingraiaPHPHtmlAdmin_loginUuid"] = "admin_".$urn;
            }
            unset($f[$_GET["sign"]]);
            $sessionUuid = uuid();
            $_SESSION["DingraiaPHPHtmlAdmin_logoutTime"] = time() + 1200;
            $_SESSION["DingraiaPHPHtmlAdmin_sessionUuid"] = $sessionUuid;
            $headers = getallheaders();
            $sessionKey[$sessionUuid] = ["UA"=>$headers["User-Agent"],"IP"=>DingraiaPHPGetIp()];
            write_to_file_json("data/bot/app/htmlAdminSession.json", $sessionKey);
            write_to_file_json("data/bot/app/htmlAdminLogin.json", $f);
            if (isset($_COOKIE["return"])) {
                $page = $_COOKIE["return"];
            } else {
                $page = $_GET["page"] ?? "chat";
            }
            setcookie("return", null, time()-1);
            header("Location: ?action=admin&page=$page");
        } else {
            header("Location: ?action=admin&returnTo=".urlencode("http://".$_SERVER["HTTP_HOST"].parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH)."?action=admin&page=index"));
        }
    } else {
        if (isset($_GET["login"]) && $_GET["login"] == "oauth2") {
            session_start();
            $lid = $_SESSION['lid'] = uuid();
            $requestUri = $_SERVER['REQUEST_URI'];
            $path = parse_url($requestUri, PHP_URL_PATH);
            header("Location: {$bot_run_as['conf']['host_url']}?client_id={$bot_run_as['config']['htmlAdmin']['appId']}&state={$lid}&redirect_uri=".urlencode("http://".$_SERVER["HTTP_HOST"].$path."?action=admin&login=dt_verify"));
        } elseif (isset($_GET["login"]) && $_GET["login"] == "dt_verify") {
            if (isset($_GET['DingraiaPHPState']) && isset($_GET['state'])) {
                $t = time();
                $sign = hash('sha256', $_GET['DingraiaPHPState'].$_GET['state'].$t.$bot_run_as["config"]["dingraiaAuthKey"]);
                $url = "{$bot_run_as['config']['host_url']}?action=api&type=oauth2Get&DingraiaPHPState={$_GET['DingraiaPHPState']}&state={$_GET['state']}&timeStamp={$t}&sign={$sign}";
                $res = requests("GET",$url)['body'];
                $res = json_decode($res, true);
                if ($res['success'] == true) {
                    if (in_array($res["result"]["unionId"], $bot_run_as["config"]["htmlAdmin"]["dingtalkOauth2Allow"])) {
                        $data = $res['data'];
                        file_put_contents("data/user/{$data['unionId']}.json", json_encode($res, JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE));
                        setcookie("dingId", $data['unionId'], time()+86400);
                        $_SESSION['DingraiaPHPHtmlAdmin_loginUuid'] = "dingtalkOauth2_{$res['result']['unionId']}";
                        $refUuid = uuid();
                        $f = read_file_to_array("data/bot/app/htmlAdminLogin.json");
                        $f[$refUuid] = ["username"=>$res["result"]["nick"]];
                        write_to_file_json("data/bot/app/htmlAdminLogin.json", $f);
                        header("Location: ?action=admin&sign=".$refUuid);
                    } else {
                        DingraiaPHPResponseExit(403, "很遗憾，您似乎不具备登录管理员页面的权限~");
                    }
                } else {
                    
                    DingraiaPHPResponseExit(400, "登录失败");
                }
            } else {
                DingraiaPHPResponseExit(400, "参数错误");
            }
        } else {
            require_once(__DIR__."/login.php");
            exit();
        }
    }
}
