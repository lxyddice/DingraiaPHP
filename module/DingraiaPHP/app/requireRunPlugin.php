<?php
if ($bot) {
    foreach($bot["config"]["accessHeader"] as $ah) {
        header($ah);
    }
    require_once("install/config.php");
    include_once("pdo.php");
    include_once(__DIR__."/api/main.php");
    if (isset($_GET['action']) && $_GET['action'] == "admin") {
        $bot["useDefaultDisplayPage"] = false;
        require_once("module/DingraiaPHP/app/admin.php");
    }
    if (isset($_GET['action']) && $_GET['action'] == "p") {
        $bot["useDefaultDisplayPage"] = false;
        require_once(__DIR__."/templeats/start.php"); 
    }
    
}


function containsDangerousChars_a_1($input) {
    $dangerousChars = array(" ","'", "\"", ";", "<", ">", "(", ")", "&", "$", "#", "@");
    foreach ($dangerousChars as $char) {
        if (strpos($input, $char) !== false) {
            return true;
        }
    }
    return false;
}
//TODO 这里因为require的逆天无法顺序导致curl无法调用这个函数在APP.php，只能放前面了，要修
function xyApp_checkToken($token) {
    $tkf = read_file_to_array("data/bot/apiPlugin/data/lxyAPP/token.json");
    if (isset($tkf["tokenList"][$token])) {
        if ($tkf["tokenList"][$token]["expiration"] > time()) {
            return ["unionId"=>$tkf["tokenList"][$token]["unionId"], "expiration"=>$tkf["tokenList"][$token]["expiration"]];
        }
    }
    unset($tkf["tokenList"][$token]);
    write_to_file_json("data/bot/apiPlugin/data/lxyAPP/token.json", $tkf);
    return false;
}