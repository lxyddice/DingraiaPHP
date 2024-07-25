<?php
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