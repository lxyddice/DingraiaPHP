<?php

function DingraiaPHPMaaArknightsMain($body, $conf) {
    global $bot_run_as;
    
    header('Content-Type:application/json; charset=utf-8');
    $MAAAllowUserList = [""];
    $MAAAllowDeviceList = [""];
    
    $bot_run_as['echoLoadPlugins'] = false;
    if (isset($_GET['MAAArknightsGetTask'])) {
        if ($_SERVER['REQUEST_METHOD'] === "POST") {
            if (in_array($body['user'], $MAAAllowUserList) && in_array($body['device'], $MAAAllowDeviceList)) {
                $bot_run_as['chat_mode'] = "MAAArknights";
                return $body;
            } else {
                DingraiaPHPResponseExit(403, "User or device not allowed", null, true, true);
            }
        } else {
            DingraiaPHPResponseExit(405, "Method Not Allowed.Need post request", null, true, true);
        }
    }
    
    if (isset($_GET['MAAArknightsReportStatus'])) {
        if ($_SERVER['REQUEST_METHOD'] === "POST") {
            if (in_array($body['user'], $MAAAllowUserList) && in_array($body['device'], $MAAAllowDeviceList)) {
                $bot_run_as['chat_mode'] = "MAAArknightsReport";
                return $body;
            } else {
                DingraiaPHPResponseExit(403, "User or device not allowed", null, true, true);
            }
        } else {
            DingraiaPHPResponseExit(405, "Method Not Allowed.Need post request", null, true, true);
        }
    }
    return false;
}