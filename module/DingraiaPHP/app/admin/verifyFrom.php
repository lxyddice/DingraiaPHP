<?php
if (file_exists("tools.php")) {
    $bot["runIn"] = "page";
    require_once("tools.php");
    if (!$bot["config"]) {
        DingraiaPHPResponseExit(403, "Forbidden to display this page");
    }
} elseif (file_exists("../../../../tools.php")) {
    $bot["runIn"] = "page";
    require_once("../../../../tools.php");
    if (!$bot["config"]) {
        DingraiaPHPResponseExit(403, "Forbidden to display this page");
    }
} else {
    exit("Can't find toolkit,are you install DingraiaPHP?");
}
