<?php
if ($bot_run_as) {
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
    $bot_run_as["responseMustTypeText"] = "no";
    $bot_run_as["responseMustType"] = 1;
}
?>