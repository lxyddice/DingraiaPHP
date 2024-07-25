<?php
class Google2FA {

    const keyRegeneration   = 30;   // Interval between key regeneration
    const otpLength     = 6;    // Length of the Token generated
    
    private static $lut = array(    // Lookup needed for Base32 encoding
        "A" => 0,    "B" => 1,
        "C" => 2,    "D" => 3,
        "E" => 4,    "F" => 5,
        "G" => 6,    "H" => 7,
        "I" => 8,    "J" => 9,
        "K" => 10,   "L" => 11,
        "M" => 12,   "N" => 13,
        "O" => 14,   "P" => 15,
        "Q" => 16,   "R" => 17,
        "S" => 18,   "T" => 19,
        "U" => 20,   "V" => 21,
        "W" => 22,   "X" => 23,
        "Y" => 24,   "Z" => 25,
        "2" => 26,   "3" => 27,
        "4" => 28,   "5" => 29,
        "6" => 30,   "7" => 31
    );
    
    /**
     * Generates a 16 digit secret key in base32 format
     * @return string
     **/
    public static function generate_secret_key($length = 16) {
        $b32    = "234567QWERTYUIOPASDFGHJKLZXCVBNM";
        $s  = "";
    
        for ($i = 0; $i < $length; $i++)
            $s .= $b32[rand(0,31)];
    
        return $s;
    }
    
    /**
     * Returns the current Unix Timestamp devided by the keyRegeneration
     * period.
     * @return integer
     **/
    public static function get_timestamp() {
        return floor(microtime(true)/self::keyRegeneration);
    }
    
    /**
     * Decodes a base32 string into a binary string.
     **/
    public static function base32_encode($str, $padding = true){
        $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
        $binary = '';
        foreach (str_split($str) as $char) {
            $binary .= str_pad(base_convert(ord($char), 10, 2), 8, '0', STR_PAD_LEFT);
        }
        $encoded = '';
        foreach (str_split($binary, 5) as $fiveBits) {
            $encoded .= $chars[base_convert(str_pad($fiveBits, 5, '0', STR_PAD_RIGHT), 2, 10)];
        }
        //需要5个字节为一组，不足5个字节的分组要用=补足
        $remainder = count($bytes) % 5;
        if ($padding && $remainder > 0) {
            $encoded .= str_repeat('=', 8 - ceil($remainder * 8 / 5));
        }
        return $encoded;
    }
    public static function base32_decode($b32) {
    
        $b32    = strtoupper($b32);
    
        if (!preg_match('/^[ABCDEFGHIJKLMNOPQRSTUVWXYZ234567]+$/', $b32, $match))
            throw new Exception('Invalid characters in the base32 string.');
    
        $l  = strlen($b32);
        $n  = 0;
        $j  = 0;
        $binary = "";
    
        for ($i = 0; $i < $l; $i++) {
    
            $n = $n << 5;                 // Move buffer left by 5 to make room
            $n = $n + self::$lut[$b32[$i]];     // Add value into buffer
            $j = $j + 5;                // Keep track of number of bits in buffer
    
            if ($j >= 8) {
                $j = $j - 8;
                $binary .= chr(($n & (0xFF << $j)) >> $j);
            }
        }
    
        return $binary;
    }
    
    /**
     * Takes the secret key and the timestamp and returns the one time
     * password.
     *
     * @param binary $key - Secret key in binary form.
     * @param integer $counter - Timestamp as returned by get_timestamp.
     * @return string
     **/
    public static function oath_hotp($key, $counter)
    {
        if (strlen($key) < 8)
        throw new Exception('Secret key is too short. Must be at least 16 base 32 characters');
    
        $bin_counter = pack('N*', 0) . pack('N*', $counter);        // Counter must be 64-bit int
        $hash    = hash_hmac ('sha1', $bin_counter, $key, true);
    
        return str_pad(self::oath_truncate($hash), self::otpLength, '0', STR_PAD_LEFT);
    }
    
    /**
     * Verifys a user inputted key against the current timestamp. Checks $window
     * keys either side of the timestamp.
     *
     * @param string $b32seed
     * @param string $key - User specified key
     * @param integer $window
     * @param boolean $useTimeStamp
     * @return boolean
     **/
    public static function verify_key($b32seed, $key, $window = 4, $useTimeStamp = true) {
    
        $timeStamp = self::get_timestamp();
    
        if ($useTimeStamp !== true) $timeStamp = (int)$useTimeStamp;
    
        $binarySeed = self::base32_decode($b32seed);
    
        for ($ts = $timeStamp - $window; $ts <= $timeStamp + $window; $ts++)
            if (self::oath_hotp($binarySeed, $ts) == $key)
                return true;
    
        return false;
    
    }
    
    /**
     * Extracts the OTP from the SHA1 hash.
     * @param binary $hash
     * @return integer
     **/
    public static function oath_truncate($hash)
    {
        $offset = ord($hash[19]) & 0xf;
    
        return (
            ((ord($hash[$offset+0]) & 0x7f) << 24 ) |
            ((ord($hash[$offset+1]) & 0xff) << 16 ) |
            ((ord($hash[$offset+2]) & 0xff) << 8 ) |
            (ord($hash[$offset+3]) & 0xff)
        ) % pow(10, self::otpLength);
    }

}

if ($bot_run_as) {
    session_start();
    function DingraiaPHPHtmlAdminApi_verifyLogin() {
        $rgkNo = 1;
        require_once(__DIR__."/../admin/fn.php");
        if (DingraiaPHPHtmlAdmin_verifyLogin()) {
            return true;
        } else {
            return false;
        }
    }
    if ($_GET["type"] == "htmlAdminLogin") {
        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $apiUn = $_POST["username"];
            $apiUn2FA = "admin_".$apiUn;
            $apiPw = $_POST["password"];
            $apiCe = $_POST["code"];
            $f = read_file_to_array("data/bot/app/htmlAdmin2FAKey.json");
            if (isset($f[$apiUn2FA])) {
                $result = Google2FA::verify_key($f[$apiUn2FA], $apiCe);
            } else {
                $result = true;
                $apiResponse["tips"] = "未开启2FA，请注意账号安全";
            }
            if ($apiUn == $bot_run_as["config"]["htmlAdmin"]["username"] && $apiPw == $bot_run_as["config"]["htmlAdmin"]["password"]  && $result) {
                $apiResponse["code"] = 0;
                $refUuid = uuid();
                $apiResponse["result"] = ["loginUuid"=>$refUuid];
                $f = read_file_to_array("data/bot/app/htmlAdminLogin.json");
                $f[$refUuid] = ["username"=>$apiUn];
                write_to_file_json("data/bot/app/htmlAdminLogin.json", $f);
            } else {
                $apiResponse["success"] = false;
                $apiResponse["code"] = -5;
                if (!$result) {
                    $apiResponse["tips"] = "2FA代码错误";
                }
            }
        } else {
            $apiResponse["success"] = false;
            $apiResponse["code"] = -4;
        }
    }
    if ($_GET['type'] == "getGroupMessage") {
        if (DingraiaPHPHtmlAdminApi_verifyLogin()) {
            $data = read_file_to_array("data/bot/chatUseGroup.json");
            if (isset($_GET['cid'])) {
                $cid = base64_decode($_GET['cid']);
                if (isset($data[$cid])) {
                    $data = $data[$cid];
                    if ($data["chatType"] == 1) {
                        $data["staffId"] = $data["message"][0]["senderStaffId"];
                    }
                    $apiResponse["code"] = 0;
                    $apiResponse['result'] = $data;
                } else {
                    $apiResponse['success'] = false;
                    $apiResponse["code"] = -1001;
                    $apiResponse['message'] = "不存在的cid";
                }
            } else {
                foreach ($data as $k => $v) {
                    unset($data[$k]["message"]);
                }
                $apiResponse["code"] = 0;
                $apiResponse['result'] = $data;
            }
        } else {
            $apiResponse["success"] = false;
            $apiResponse["code"] = -5;
        }
    }
    if ($_GET['type'] == "sendGroupMessage") {
        if (DingraiaPHPHtmlAdminApi_verifyLogin()) {
            $webhook = $_POST['webhook'];
            if (strpos($webhook, "https://oapi.dingtalk.com/") === 0) {
                if ($_POST['groupMode'] == "true") {
                    $robotCode = $_POST['robotCode'];
                    $r = useRobotcode2Corpid($_POST['robotCode']);
                    $chatbotCorpId = $r['cropid'];
                    $bot_run_as["data"]["chatbotCorpId"] = $chatbotCorpId;
                    $conversationId = $_POST['groupId'];
                    if ($_POST['chatType'] == 1) {
                        $res = sampleText($_POST['content'], $webhook, 0, [$_POST["staffId"]]);
                    } else {
                        $res = sampleText($_POST['content'], $webhook);
                    }
                    $res = json_decode($res, true);
                } else {
                    $res = send_message($_POST['content'], $webhook);
                    $res = json_decode($res, true);
                }
                if (isset($res["processQueryKey"])) {
                    $apiResponse['code'] = 0;
                    $apiResponse['result'] = $res;
                } else {
                    $apiResponse['code'] = -1002;
                    $apiResponse['message'] = "远程服务器返回错误";
                    if ($res == null) {
                        $res = ["code"=> -1003];
                    }
                    $apiResponse['result'] = $res;
                }
            } else {
                $apiResponse["code"] = -2;
            }
        } else {
            $apiResponse["success"] = false;
            $apiResponse["code"] = -5;
        }
    }
    if ($_GET["type"] == "htmlAdmin_getAd") {
        $apiResponse['code'] = 0;
        $apiResponse['result'] = [["title"=>"这是测试广告","icon"=>'<svg xmlns="http://www.w3.org/2000/svg" width="128" height="128" viewBox="0 0 24 24"><path fill="currentColor" d="M12 2C6.477 2 2 6.477 2 12s4.477 10 10 10s10-4.477 10-10S17.523 2 12 2m4.49 9.04l-.006.014c-.42.898-1.516 2.66-1.516 2.66l-.005-.012l-.32.558h1.543l-2.948 3.919l.67-2.666h-1.215l.422-1.763a16.91 16.91 0 0 0-1.223.349s-.646.378-1.862-.729c0 0-.82-.722-.344-.902c.202-.077.981-.175 1.595-.257a80.204 80.204 0 0 1 1.338-.172s-2.555.039-3.161-.057c-.606-.095-1.375-1.107-1.539-1.996c0 0-.253-.488.545-.257c.798.231 4.101.9 4.101.9S8.27 9.312 7.983 8.99c-.286-.32-.841-1.754-.769-2.634c0 0 .031-.22.257-.16c0 0 3.176 1.45 5.347 2.245c2.172.795 4.06 1.199 3.816 2.228c-.02.087-.072.216-.144.37"/></svg>',"info"=>"这是测试广告的内容简介","location"=>"https://github.com/lxyddice/DingraiaPHP"],["title"=>"lxyddice","icon"=>'<svg xmlns="http://www.w3.org/2000/svg" width="128" height="128" viewBox="0 0 24 24"><path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m14 9l3 5.063M4 9l6 6m-6 0l6-6m10 0l-4.8 9"/></svg>',"info"=>"DingraiaPHP主开发者：lxyddice","location"=>"https://github.com/lxyddice"]];
    }
    if ($_GET["type"] == "htmlAdmin_getAdminInfo") {
        $apiResponse['code'] = 0;
        $apiResponse['result'] = [];
    }
    if ($_GET["type"] == "htmlAdmin_create2FA_Account") {
        if (DingraiaPHPHtmlAdminApi_verifyLogin()) {
            $f = read_file_to_array("data/bot/app/htmlAdmin2FAKey.json");
            if (isset($f[$_SESSION['DingraiaPHPHtmlAdmin_loginUuid']])) {
                $apiResponse["success"] = false;
                $apiResponse["code"] = -1;
                $apiResponse["result"] = ["username"=>$_SESSION["DingraiaPHPHtmlAdmin_loginUuid"]];
                $apiResponse["tips"] = "已存在此用户的2FA密钥，请勿重复生成~";
            } else {
                $apiResponse['code'] = 0;
                require_once('module/phpqrcode/qrlib.php');
                $InitalizationKey = Google2FA::base32_encode(uuid());
                $TimeStamp    = Google2FA::get_timestamp();
                $secretkey    = Google2FA::base32_decode($InitalizationKey);
                $otp          = Google2FA::oath_hotp($secretkey, $TimeStamp);
                $result = Google2FA::verify_key($InitalizationKey, "123456");
                $qrcodeUuid = uuid();
                QRcode::png("otpauth://totp/DingraiaPHP:{$_SESSION['DingraiaPHPHtmlAdmin_loginUuid']}?algorithm=SHA1&digits=6&issuer=DingraiaPHP&period=30&secret={$InitalizationKey}", "data/bot/app/{$qrcodeUuid}.png", QR_ECLEVEL_L, 10);
                $picB64 = base64_encode(file_get_contents("data/bot/app/{$qrcodeUuid}.png"));
                unlink("data/bot/app/{$qrcodeUuid}.png");
                $f[$_SESSION['DingraiaPHPHtmlAdmin_loginUuid']] = $InitalizationKey;
                write_to_file_json("data/bot/app/htmlAdmin2FAKey.json",$f);
                $apiResponse["code"] = 0;
                $apiResponse["result"] = ["pic"=>$picB64, "timeStamp"=>$TimeStamp, "initalizationKey"=>$InitalizationKey, "code"=>$otp, "res"=>$result];
            }
        } else {
            $apiResponse["success"] = false;
            $apiResponse["code"] = -5;
        }
    }
    if ($_GET["type"] == "htmlAdmin_check2FA_Account") {
        if (DingraiaPHPHtmlAdminApi_verifyLogin()) {
            $f = read_file_to_array("data/bot/app/htmlAdmin2FAKey.json");
            if (isset($f[$_SESSION['DingraiaPHPHtmlAdmin_loginUuid']])) {
                $result = Google2FA::verify_key($f[$_SESSION['DingraiaPHPHtmlAdmin_loginUuid']], $_GET["code"]);
                if ($result) {
                    $apiResponse["code"] = 0;
                    $apiResponse["result"] = ["verifyCodeResult"=>true,"time"=>time()];
                } else {
                    $apiResponse["success"] = false;
                    $apiResponse["code"] = -1;
                    $apiResponse["tips"] = "2FA代码错误";
                }
            } else {
                $apiResponse["success"] = false;
                $apiResponse["code"] = -6;
            }
        } else {
            $apiResponse["success"] = false;
            $apiResponse["code"] = -5;
        }
    }
    if ($_GET["type"] == "htmlAdmin_prolongLoginSession") {
        if (DingraiaPHPHtmlAdminApi_verifyLogin()) {
            $l = $_SESSION["DingraiaPHPHtmlAdmin_logoutTime"];
            $prolongTime = $_GET["prolongTime"] ?? null;
            if ($prolongTime && $prolongTime > 0 && $prolongTime < 86400) {
                $add = $prolongTime;
                $apiResponse["code"] = 0;
            } elseif ($prolongTime) {
                $apiResponse["code"] = -1;
                $apiResponse["tips"] = "设定超出允许值";
                $add = 600;
            } else {
                $apiResponse["code"] = 0;
                $add = 600;
            }
            $_SESSION["DingraiaPHPHtmlAdmin_logoutTime"] = time() + $add;
            $apiResponse["result"] = ["timeStamp" => time(), "oldLogoutTime" => $l, "prolongTime"=>(int)$add, "newLogoutTime" => $_SESSION["DingraiaPHPHtmlAdmin_logoutTime"]];
        } else {
            $apiResponse["success"] = false;
            $apiResponse["code"] = -5;
        }
    }
    if ($_GET["type"] == "htmlAdmin_clearLoginSessions") {
        if (DingraiaPHPHtmlAdminApi_verifyLogin()) {
            $sessionKey = read_file_to_array("data/bot/app/htmlAdminSession.json");
            write_to_file_json("data/bot/app/htmlAdminSession.json", []);
            $apiResponse["code"] = 0;
            $apiResponse["result"] = ["timeStamp" => time(), "clearSession"=>$sessionKey];
        } else {
            $apiResponse["success"] = false;
            $apiResponse["code"] = -5;
        }
    }
    if ($_GET["type"] == "htmlPage_logout") {
        if (DingraiaPHPHtmlAdminApi_verifyLogin()) {
            unset($_SESSION['DingraiaPHPHtmlAdmin_sessionUuid']);
            $apiResponse["code"] = 0;
            $apiResponse["result"] = ["timeStamp" => time(), "clearSession"=>$sessionKey];
        } else {
            $apiResponse["success"] = false;
            $apiResponse["code"] = -5;
        }
    }
    
    if ($_GET["type"] == "add_user") {
        if (DingraiaPHPHtmlAdminApi_verifyLogin()) {
            $userId = $_POST['userId'];
            $userName = $_POST['userName'];
            $permissions = $_POST['permissions'];
            
            $userData = read_file_to_array('config/permission/users.json');
            
            $userData[] = [
                'userId' => $userId,
                'userName' => $userName,
                'permissions' => explode(',', $permissions)
            ];
            
            write_to_file_json('config/permission/users.json', $userData);
            $apiResponse["code"] = 0;
        } else {
            $apiResponse["success"] = false;
            $apiResponse["code"] = -5;
        }
    }
    if ($_GET["type"] == "add_group") {
        if (DingraiaPHPHtmlAdminApi_verifyLogin()) {
            $groupName = $_POST['groupName'];
            $permissions = $_POST['permissions'];
            $uid = $_POST['uid'];
            
            // 读取现有的组数据
            $groupData = read_file_to_array('config/permission/group.json');
            
            $groupData[$groupData] = ["permission"=>$permissions, "uid"=>$uid];
            
            write_to_file_json('config/permission/group.json', $groupData);
            $apiResponse["code"] = 0;
        } else {
            $apiResponse["success"] = false;
            $apiResponse["code"] = -5;
        }
    }
    if ($_GET["type"] == "get_users") {
        if (DingraiaPHPHtmlAdminApi_verifyLogin()) {
            $apiResponse["result"] = read_file_to_array('config/permission/user.json');
            $apiResponse["code"] = 0;
        } else {
            $apiResponse["success"] = false;
            $apiResponse["code"] = -5;
        }
    }
    if ($_GET["type"] == "get_groups") {
        if (DingraiaPHPHtmlAdminApi_verifyLogin()) {
            $apiResponse["result"] = read_file_to_array('config/permission/group.json');
            $apiResponse["code"] = 0;
        } else {
            $apiResponse["success"] = false;
            $apiResponse["code"] = -5;
        }
    }
}