<?php

function dingraiaDingtalkCallbackFindValueInDictionary($dictionary, $encrypt){
    foreach ($dictionary as $key => $value) {
        if (is_array($value)) {
            if (isset($value['encodingAesKey'])) {
                $result = de_DingraiaDingtalkCallback($_GET['msg_signature'], $_GET['timestamp'], $_GET['nonce'], $encrypt, $value['token'], $value['encodingAesKey'], $value['AppKey']);
            }
            if ($result == true) {
                return ['callback'=>$result, 'appInfo'=>[$key => $value]]; // 返回子字典
            }
        } else {
            return false;
        }
    }
    echo json_encode(["success"=>false,"message"=>"Fail to decrypt"]);
    return false;
}

function getTrueKeyDingraiaDingtalkCallback($encrypt) {
    $cids = read_file_to_array("config/cropid.json");
    $r = dingraiaDingtalkCallbackFindValueInDictionary($cids, $encrypt);
    return $r;
}

function de_DingraiaDingtalkCallback($signature, $timeStamp, $nonce, $encrypt, $token, $encodingAesKey, $suiteKey) {
    try {
        $crypto = new \Hlf\DingTalkCrypto\Crypto($token, $encodingAesKey, $suiteKey);
        $decryptedMessage = $crypto->decryptMsg($signature, $timeStamp, $nonce, $encrypt);
        $decryptedMessage = json_decode($decryptedMessage, true);
        write_to_file_json("log10.json",$decryptedMessage);
        en_DingraiaDingtalkCallback('success',$nonce, $token, $encodingAesKey, $suiteKey);
        return $decryptedMessage;
    } catch(Exception $e) {
        return false;
    }
}

function en_DingraiaDingtalkCallback($text, $nonce, $token, $encodingAesKey, $suiteKey, $echoOut = true) {
    try {
        $crypto = new \Hlf\DingTalkCrypto\Crypto($token, $encodingAesKey, $suiteKey);
        //$timeStamp = time()."114";
        $timeStamp = time()."114";
        $encryptedMessage = $crypto->encryptMsg($text, $timeStamp, null);
        if ($echoOut == true) {
            echo($encryptedMessage);
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
