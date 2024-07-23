<?php
if (!$bot_run_as) {
    if (file_exists("../../../tools.php")) {
        $bot_run_as["runIn"] = "page";
        require_once("../../../tools.php");
        DingraiaPHPResponseExit(403, "Forbidden to interview this page");
    } else {
        exit("Can't find toolkit,are you install DingraiaPHP?");
    }
} else {
    if (!file_exists("tools.php")) {
        exit("Can't find toolkit,are you install DingraiaPHP?");
    }
}

require_once("module/DingraiaPHP/app/admin/main.php");