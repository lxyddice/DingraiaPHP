<?php
if ($bot_run_as) {
    try {
        $DingraiaPHPPdo = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);
        $DingraiaPHPPdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $bot_run_as["sqlPdoConnect"] = "true";
    } catch (PDOException $e) {
        $DingraiaPHPresponse = read_file_to_array("data/bot/app/response.json");
        DingraiaPHPAddNormalResponse("pdoError", $e);
        write_to_file_json("data/bot/app/response.json", $DingraiaPHPresponse);
        
    }
}