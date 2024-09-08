<?php
if ($rgkNo != 1) {
    if (file_exists("verifyFrom.php")) {
        require_once("verifyFrom.php");
    } elseif (file_exists("module/DingraiaPHP/app/admin/verifyFrom.php")) {
        require_once("module/DingraiaPHP/app/admin/verifyFrom.php");
    } else {
        exit("Can't find toolkit,are you install DingraiaPHP?");
    }
}

function DingraiaPHPHtmlAdmin_verifyLogin() {
    $sessionKey = read_file_to_array("data/bot/app/htmlAdminSession.json");
    if (isset($_SESSION["DingraiaPHPHtmlAdmin_logoutTime"]) && $_SESSION["DingraiaPHPHtmlAdmin_logoutTime"] > time() && isset($sessionKey[$_SESSION["DingraiaPHPHtmlAdmin_sessionUuid"]])) {
        return true;
    }
    return false;
}