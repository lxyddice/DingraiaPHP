<?php

function xy_arrayToXml($arr) {
    $xml = "<xml>";
    foreach ($arr as $key => $val) {
        if (is_numeric($val)) {
            $xml .= "<$key>$val</$key>";
        } else {
            $xml .= "<$key><![CDATA[$val]]></$key>";
        }
    }
    $xml .= "</xml>";
    return $xml;
}

function DingraiaPHPRunEnd($hideLoadPluginInfo, $hideLoadPluginInfo_B) {
    global $bot_run_as, $DingraiaPHPPdo;

    if ($bot_run_as['startTime']) {
        endBotRun($bot_run_as);
    }

    if ($DingraiaPHPPdo) {
        $DingraiaPHPPdo = null;
    }

    ob_start();
    executeEndTasks();

    $response = read_file_to_array("data/bot/app/response.json");

    if ($bot_run_as["responseMustType"] == 1) {
        $response["type"] = $bot_run_as["responseMustTypeText"];
    }

    $responseType = trim($response["type"]);

    if ($responseType != "no") {
        outputResponse($responseType, $response["content"]);
    }

    write_to_file_json("data/bot/app/response.json", []);
    $output = ob_get_clean();
    echo $output;
}

function endBotRun(&$bot_run_as) {
    $bot_run_as["schedule"] = "end";
    require_once(__DIR__ . "/webhook/main.php");

    $endTime = microtime(true);
    $endMemory = memory_get_usage();
    $executionTime = round($endTime - $bot_run_as['startTime'], 4);
    $usedMemory = formatBytes($endMemory - $bot_run_as['startMemory']);

    app_json_file_add_list($bot_run_as["RUN_LOG_FILE"], [
        "time" => microtime(true),
        "type" => "end",
        "data" => [
            "bot_run_as" => $bot_run_as,
            "usedMemory" => $usedMemory,
            "executionTime" => $executionTime
        ]
    ]);
    
    $bot_run_as["logger"]["class"]->trace("END");
}

function executeEndTasks() {
    if (file_exists("data/bot/app/endTasks.json")) {
        $endTasks = read_file_to_array("data/bot/app/endTasks.json");
        if (is_array($endTasks) && count($endTasks) > 0) {
            foreach ($endTasks as $endTask) {
                if (file_exists($endTask["file"])) {
                    require_once $endTask["file"];
                    if (function_exists($endTask["fn_name"])) {
                        $endTask["fn_name"]();
                    }
                }
            }
        }
        write_to_file_json("data/bot/app/endTasks.json", []);
    }
}

function outputResponse($responseType, $content) {
    if ($responseType == "json") {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($content);
    } elseif ($responseType == "html") {
        echo $content;
    }
}

DingraiaPHPRunEnd($hideLoadPluginInfo, $hideLoadPluginInfo_B);
?>
