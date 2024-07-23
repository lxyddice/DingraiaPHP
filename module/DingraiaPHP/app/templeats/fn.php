<?php
if ($rgkNo != 1) {
    if (file_exists("tools.php")) {
        $bot_run_as["runIn"] = "page";
        require_once("tools.php");
        if (!$bot_run_as["config"]) {
            DingraiaPHPResponseExit(403, "Forbidden to display this page");
        }
    } elseif (file_exists("../../../../tools.php")) {
        $bot_run_as["runIn"] = "page";
        require_once("../../../../tools.php");
        if (!$bot_run_as["config"]) {
            DingraiaPHPResponseExit(403, "Forbidden to display this page");
        }
    } else {
        exit("Can't find toolkit,are you install DingraiaPHP?");
    }
}
