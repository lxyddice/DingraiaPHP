<?php
ignore_user_abort(true);
function dingraiaDingtalkCallbackFindValueInDictionary($dictionary, $encrypt){
    global $bot;
    $bot["callback"]["verify"] = false;
    foreach ($dictionary as $key => $value) {
        if (is_array($value)) {
            if (isset($value['encodingAesKey'])) {
                $result = de_DingraiaDingtalkCallback($_GET['msg_signature'], $_GET['timestamp'], $_GET['nonce'], $encrypt, $value['token'], $value['encodingAesKey'], $value['AppKey']);
            }
            if (isset($result) && $result == true) {
                $bot["callback"]["verify"] = true;
                return ['callback'=>$result, 'appInfo'=>[$key => $value]]; // 返回子字典
            }
        } else {
            return false;
        }
    }
    echo json_encode(["success"=>false,"message"=>"Fail to decrypt","request_id"=>$bot["RUN_ID"]]);
    return false;
}

function getTrueKeyDingraiaDingtalkCallback($encrypt) {
    $cids = read_file_to_array("config/cropid.json");
    $r = dingraiaDingtalkCallbackFindValueInDictionary($cids, $encrypt);
    return $r;
}

function de_DingraiaDingtalkCallback($signature, $timeStamp, $nonce, $encrypt, $token, $encodingAesKey, $suiteKey) {
    global $bot;
    try {
        $crypto = new \Hlf\DingTalkCrypto\Crypto($token, $encodingAesKey, $suiteKey);
        $decryptedMessage = $crypto->decryptMsg($signature, $timeStamp, $nonce, $encrypt);
        $decryptedMessage = json_decode($decryptedMessage, true);
        write_to_file_json("log10.json",$decryptedMessage);
        en_DingraiaDingtalkCallback('success',$nonce, $token, $encodingAesKey, $suiteKey);
        return $decryptedMessage;
    } catch(Exception $e) {
        app_json_file_add_list($bot["RUN_LOG_FILE"], ["time"=>microtime(),"type"=>"callbackError","run_fn"=>$bot,"error"=>$e]);
        return false;
    }
}

function en_DingraiaDingtalkCallback($text, $nonce, $token, $encodingAesKey, $suiteKey, $echoOut = true) {
    try {
        $crypto = new \Hlf\DingTalkCrypto\Crypto($token, $encodingAesKey, $suiteKey);
        $timeStamp = time()."114";
        $encryptedMessage = $crypto->encryptMsg($text, $timeStamp, null);
        if ($echoOut == true) {
            echo($encryptedMessage);
            //fastcgi_finish_request();
            write_to_file_json("log10-en.json",$encryptedMessage);
        }
        return $encryptedMessage;
    } catch(Exception $e) {
        return false;
    }
}

function dingraiaDingtalkCallback($encrypt) {
    require_once 'Crypto.php';
    header("Content-type:application/json");
    $r = getTrueKeyDingraiaDingtalkCallback($encrypt);
    return $r;
    
}
?>
