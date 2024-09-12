<?php

//导入外置模块
if ($requireMoudle == "lxyddice") {
    $x = DingraiaPHPLoadMoudlePluginMain($body, $conf);
}

function DingraiaPHPLoadMoudlePluginMain($body, $conf) {
    global $bot;
    
    if (isset($_GET["cronDie"])) {
        $cronAuth = "lxyddice";
        require_once("module/DingraiaPHP/cron/die.php");
    }
    $pluginConfig = "config/module/plugins.json";
    if (file_exists($pluginConfig)) {
        $pluginConfig = read_file_to_array($pluginConfig);
        foreach ($pluginConfig as $plugin) {
            if (isset($plugin["getParams"]) && isset($plugin["requireFile"])) {
                foreach ($_GET as $key => $value) {
                    if (in_array($key, $plugin["getParams"])) {
                        foreach ($plugin["requireFile"] as $file) {
                            if (file_exists($file)) {
                                require_once($file);
                                $func = $plugin["start"];
                                $c = $func($body, $conf);
                                if ($c) {
                                    $cm = isset($plugin["chatMode"])? $plugin["chatMode"] : "default";
                                    $c["chat_mode"] = $bot["chat_mode"] = $cm;
                                    $r[] = $c;
                                    return $r;
                                } elseif (isset($plugin["failReturn"])) {
                                    if (isset($plugin["failReturn"])) {
                                        if (!isset($plugin["failReturn"][2])) {
                                            $plugin["failReturn"][2] = null;
                                        }
                                        DingraiaPHPResponseExit($plugin["failReturn"][0], $plugin["failReturn"][1], $plugin["failReturn"][2], true, true);
                                    }
                                    return false;
                                }
                            } else {
                                DingraiaPHPResponseExit(404, $file." Plugin Not Found", null, true, true);
                            }
                        }
                    }
                }
            }
            
        }
    } else {
        DingraiaPHPResponseExit(404, "Plugin Config Not Found", null, true, true);
    }
    /*下面是框架功能内，以插件形式载入，请勿动~*/
    if (isset($_GET['action'])) {
        if ($_GET['action'] == "api") {
            /*框架API类*/
            $c["chat_mode"] = "DingraiaPHPApi";
            $r[] = $c;
            return $r;
        }
        if ($_GET['action'] == "admin") {
            /*框架管理员类*/
            $c["chat_mode"] = "DingraiaPHPAdminHtml";
            $r[] = $c;
            return $r;
        }
    }
    
    return false;
}