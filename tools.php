<?php
/*
  ____  _                       _       ____  _   _ ____  
 |  _ \(_)_ __   __ _ _ __ __ _(_) __ _|  _ \| | | |  _ \ 
 | | | | | '_ \ / _` | '__/ _` | |/ _` | |_) | |_| | |_) |
 | |_| | | | | | (_| | | | (_| | | (_| |  __/|  _  |  __/ 
 |____/|_|_| |_|\__, |_|  \__,_|_|\__,_|_|   |_| |_|_|    
                |___/                      
                                    Github version
*/
global $bot_run_as;
if (isset($bot_run_as["runIn"])) {
    if ($bot_run_as["runIn"] != "page") {
        require_once("install/config.php");
        require_once($autoload);
        require_once("module/DingraiaPHP/app/serviceBan.php");
        $bot_run_as['echoLoadPlugins'] = true;
    }
} else {
    require_once("install/config.php");
    require_once($autoload);
    require_once("module/DingraiaPHP/app/serviceBan.php");
    $bot_run_as['echoLoadPlugins'] = true;
}

$bot_run_as['startTime'] = microtime(true);
$bot_run_as['startMemory'] = memory_get_usage();

function dingraia_version() {
    return requests("GET","https://api.lxyddice.top/dingbot/php/?action=api&type=version")["body"];
}
function dingraia_version_b() {
    return requests("GET","https://api.lxyddice.top/dingbot/php/?action=api&type=version")["body"];
}

function is_read_disclaimer($uid): bool
{
    $fp = "data/user/is_read_disclaimer/$uid.json";
    if (file_exists($fp)) {
        $data = json_decode(file_get_contents($fp));
        if (isset($data['read']) and $data['read']) {
            return true;
        }
    }
    return false;
}
function containsValidCharacters_1($str): bool
{
    if (preg_match('/[^A-Za-z0-9\-]/', $str)) {
        return false;
    } else {
        return true;
    }
}
function containsValidCharacters_2($str): bool
{
    if (preg_match('/[^A-Za-z0-9\-_]/', $str)) {
        return false;
    } else {
        return true;
    }
}
function DingraiaPHPRandomStrGenerator($length = 16): string
{
    $str = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    $len = strlen($str)-1;
    $randstr = '';
    for ($i=0;$i<$length;$i++) {
        $num=mt_rand(0,$len);
        $randstr .= $str[$num];
    }
    return $randstr; 
}
function DingraiaPHPCreateShortUrl($url, $onlyOnce = true, $idL = 8) {
    global $bot_run_as;
    $urlB64 = base64_encode($url);
    $id = DingraiaPHPRandomStrGenerator($idL);
    $phId = substr($urlB64, 0, 16);
    if ($onlyOnce) {
        if (file_exists("data/bot/app/shortUrl/bid/{$phId}.json")) {
            $phIdData = read_file_to_array("data/bot/app/shortUrl/bid/{$phId}.json");
            if (isset($phIdData["urls"][$urlB64])) {
                $id = $phIdData["urls"][$urlB64]["id"];
                $phIdData["newRequest"][$urlB64][] = ["time"=>time(), "runId"=>$bot_run_as["RUN_ID"]];
                write_to_file_json("data/bot/app/shortUrl/sid/{$id}.json", $phIdData);
            } else {
                $phIdData["urls"][$urlB64] = ["id"=>$id];
                write_to_file_json("data/bot/app/shortUrl/bid/{$phId}.json", $phIdData);
                write_to_file_json("data/bot/app/shortUrl/sid/{$id}.json", ["url"=>$urlB64, "createAt"=>time()]);
            }
        } else {
            $phIdData["urls"][$urlB64] = ["id"=>$id];
            write_to_file_json("data/bot/app/shortUrl/bid/{$phId}.json", $phIdData);
            write_to_file_json("data/bot/app/shortUrl/sid/{$id}.json", ["url"=>$urlB64, "createAt"=>time()]);
        }
    } else {
        write_to_file_json("data/bot/app/shortUrl/sid/{$id}.json", ["url"=>$urlB64, "id"=>$id, "createAt"=>time()]);
    }
    return $id;
}

function DingraiaPHPGetIp(): array {
    static $realip;
    if (isset($_SERVER)){
        if (isset($_SERVER["HTTP_CF_PSEUDO_IPV4"])){
            $realip = ["ip"=>$_SERVER["HTTP_CF_PSEUDO_IPV4"], "ipv6"=>$_SERVER["HTTP_CF_CONNECTING_IP"], "from"=>"CF_PSEUDO_IPV4"];
        } else if (isset($_SERVER["HTTP_CF_CONNECTING_IP"])){
            $realip = ["ip"=>$_SERVER["HTTP_CF_CONNECTING_IP"], "from"=>"CF_CONNECTING_IP"];
        } else if (isset($_SERVER["HTTP_X_FORWARDED_FOR"])){
            $realip = ["ip"=>$_SERVER["HTTP_X_FORWARDED_FOR"], "from"=>"X_FORWARDED_FOR"];
        } else if (isset($_SERVER["HTTP_CLIENT_IP"])) {
            $realip = ["ip"=>$_SERVER["HTTP_CLIENT_IP"], "from"=>"HTTP_CLIENT_IP"];
        } else {
            $realip = ["ip"=>$_SERVER["REMOTE_ADDR"], "from"=>"REMOTE_ADDR"];
        }
    } else {
        $realip = ["ip"=>null, "from"=>"Bad_request"];
    }
    return $realip;
}


function updateMoney($uid, $changeAmount): bool
{
    if (!is_numeric($uid) || !is_numeric($changeAmount)) {
        return false;
    }

    $user_files = glob("data/bot/user/*.json");
    foreach ($user_files as $file) {
        $user_data = json_decode(file_get_contents($file), true);
        if ($user_data['uid'] == $uid) {
            if (!isset($user_data['money'])) {
                $user_data['money'] = 0;
            }
            $user_data['money'] += $changeAmount;
            file_put_contents($file, json_encode($user_data));
            return true;
        }
    }
    return false;
}

function not_require(): bool
{
    if ($_SERVER['PHP_SELF'] === __FILE__) {
        return false;
    } else {
        return true;
    }
}
 
function stringf($string, $a = ' '): array
{
    $spaceParams = explode($a, $string);
    $len = count($spaceParams);
    
    $obj = array(
        'len' => $len - 1,
        'params' => $spaceParams
    );
    
    return $obj;
}

function has_permission($uid, $permission): bool
{
    $userPermissions = read_file_to_array("config/permission/user.json");
    $groupPermissions = read_file_to_array("config/permission/group.json");
    $permissions = $userPermissions[$uid] ?? [];
    foreach ($groupPermissions as $group) {
        if (in_array($uid, $group['user'])) {
            $permissions = array_merge($permissions, $group['permission']);
        }
    }
    if (in_array("*.*", $permissions)) {
        return true;
    }
    if (in_array($permission, $permissions)) {
        return true;
    }
    if (in_array("*.*", $permissions)) {
        return true;
    }
    $permissionParts = explode('.', $permission);
    $currentPermission = '';

    foreach ($permissionParts as $part) {
        $currentPermission .= ($currentPermission ? '.' : '') . $part;

        if (in_array($currentPermission . '.*', $permissions)) {
            return true;
        }
    }
    return false;
}

function permission_check($permission, $uid): bool{    
    $userPermissions = read_file_to_array("config/permission/user.json");
    $groupPermissions = read_file_to_array("config/permission/group.json");
    $permissions = $userPermissions[$uid] ?? [];
    
    foreach ($groupPermissions as $groupName => $group) {
        if (isset($group['user']) && in_array($uid, $group['user'])) {
            $permissions = array_merge($permissions, $group['permission']);
        }
    }
    if (in_array($permission, $permissions)) {
        return true;
    }
    if (in_array("*.*", $permissions)) {
        return true;
    }
    $permissionParts = explode('.', $permission);
    $currentPermission = '';

    foreach ($permissionParts as $part) {
        $currentPermission .= ($currentPermission ? '.' : '') . $part;

        if (in_array($currentPermission . '.*', $permissions)) {
            return true;
        }
    }

    return false;
}
/* 权限组代码尝试重构中 */
function uuid() {
    $chars = md5(uniqid(mt_rand(), true));  
    $uuid = substr ( $chars, 0, 8 ) . '-'
            . substr ( $chars, 8, 4 ) . '-' 
            . substr ( $chars, 12, 4 ) . '-'
            . substr ( $chars, 16, 4 ) . '-'
            . substr ( $chars, 20, 12 );  
    return $uuid ;  
}  
 

function formatBytes($bytes, $precision = 2): string
{
    $units = array('B', 'KB', 'MB', 'GB', 'TB');

    $bytes = max($bytes, 0);
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
    $pow = min($pow, count($units) - 1);

    $bytes /= pow(1024, $pow);

    return round($bytes, $precision) . ' ' . $units[$pow];
}

function requestsDingtalkApi($type, $url, $body, $headers, $timeout = 10, $onlyLog = false) {
    if (!$onlyLog) {
        if ($url[0] == 0) {
            $u = "https://api.dingtalk.com";
        } else {
            $u = "https://oapi.dingtalk.com";
        }
        $res = requests($type, $u.$url[1], $body, $headers, $timeout);
        if (!file_exists("data/bot/app/count.json")) {
            write_to_file_json("data/bot/app/count.json", ["count"=>0]);
        }
    }
    $f = read_file_to_array("data/bot/app/count.json");
    $ym = date("Ym");
    if (isset($f['dingtalkApiCount'][$ym])) {
        $f['dingtalkApiCount'][$ym]++;
    } else {
        $f['dingtalkApiCount'][$ym] = 1;
    }
    write_to_file_json("data/bot/app/count.json", $f);
    return isset($res) ? $res : false;
}

function userinfo($userId, $token) {
    $headers = array(
        "Content-Type" => "application/json"
    );
    
    $data = array(
    	"userid"=>$userId
    );
    
    $res = requestsDingtalkApi("POST", [1,"/topapi/v2/user/get?access_token=$token"], $data, $headers, 20)["body"];
    $res = json_decode($res, true);
    
    return $res;
}

function getbyunionid($userId, $token) {
    $headers = array(
        "Content-Type" => "application/json"
    );
    
    $data = array(
    	"userid"=>$userId
    );
    
    $res = requestsDingtalkApi("POST", [1,"/topapi/user/getbyunionid?access_token=$token"], $data, $headers, 20)["body"];
    $res = json_decode($res, true);
    
    if ($res['code'] == 60121) {
        return false;
    } else {
        return $res;
    }
}

function add_member($token, $c, $u) {
    $headers = array(
        "Content-Type" => "application/json"
    );
    
    $data = array(
    	"user_ids"=>$u,
    	"open_conversation_id"=>$c
    );
    
    $res = requestsDingtalkApi("POST", [1,"/topapi/chat/add_member?access_token=$token"], $data, $headers, 20)["body"];
    
    $res = json_decode($res, true);
    return $res;
}
        
function translate($text, $token, $from="zh", $to="ja") {
    $headers = array(
        "Content-Type" => "application/json"
    );
    
    $data = array(
    	"query"=>$text,
    	"source_language"=>$from,
    	"target_language"=>$to
    );
    
    $res = requestsDingtalkApi("POST", [1,"/topapi/ai/mt/translate?access_token=$token"], $data, $headers, 20)["body"];
    $res = json_decode($res, true);

    return $res;
}  

function mute_user($mutetime, $openConversationId, $user, $token) {
    $headers = array(
        "x-acs-dingtalk-access-token" => $token,
        "Content-Type" => "application/json"
    );
    
    $data = array(
        "muteDuration" => $mutetime,
        "openConversationId" => $openConversationId,
        "userIdList" => $user,
        "muteStatus" => 1
    );
    
    $res = requestsDingtalkApi("POST", [0,"/v1.0/im/sceneGroups/muteMembers/set"], $data, $headers, 20)["body"];
    $res = json_decode($res, true);

    return $res;
}  

function unmute_user($openConversationId, $user, $token) {
    $headers = array(
        "x-acs-dingtalk-access-token" => $token,
        "Content-Type" => "application/json"
    );
    
    $data = array(
        "muteDuration" => 0,
        "openConversationId" => $openConversationId,
        "userIdList" => $user,
        "muteStatus" => 0
    );
    
    $res = requestsDingtalkApi("POST", [0,"/v1.0/im/sceneGroups/muteMembers/set"], $data, $headers, 20)["body"];
    $res = json_decode($res, true);

    return $res;
}  

function get_accessToken(string $key,string $secret) {
    $cacheFilePath = 'data/bot/token.json';
    if (file_exists($cacheFilePath)) {
        $cacheData = json_decode(file_get_contents($cacheFilePath), true);
        if (isset($cacheData[$key]) && time() < $cacheData[$key]['extime']) {
            return $cacheData[$key]['token'];
        }
    }
    $res = requestsDingtalkApi("POST", [0,"/v1.0/oauth2/accessToken"], ["appKey" => $key, "appSecret" => $secret], ["Content-Type" => "application/json"]);
    try {
        $res = json_decode($res["body"], true);
    } catch(Exception $e) {
        return false;
    }

    if (isset($res['accessToken'])) {
        $cacheData[$key] = [
            'token' => $res['accessToken'],
            'extime' => time() + 110 * 60
        ];
        file_put_contents($cacheFilePath, json_encode($cacheData));
        return $res['accessToken'];
    } else {
        return $res;
    }
}


function change_user_name($openConversationId, $userId, $groupNick, $token) {
    $headers = array(
        "x-acs-dingtalk-access-token" => $token,
        "Content-Type" => "application/json"
    );
    
    $data = array(
        "openConversationId" => $openConversationId,
        "userId" => $userId,
        "groupNick" => $groupNick
    );
    
    $res = requestsDingtalkApi("PUT", [0, "/v1.0/im/sceneGroups/members/groupNicks"], $data, $headers, 20)["body"];
    $res = json_decode($res, true);

    return $res;
}

function op($openConversationId, $user, $token) {
    $headers = array(
        "x-acs-dingtalk-access-token" => $token,
        "Content-Type" => "application/json"
    );

    $data = array(
        "openConversationId" => $openConversationId,
        "userIds" => $user,
        "role" => 2
    );
    
    $res = requestsDingtalkApi("PUT", [0, "/v1.0/im/sceneGroups/subAdmins"], $data, $headers, 20)["body"];
    return $res;
}  

function deop($openConversationId, $user, $token) {
    $headers = array(
        "x-acs-dingtalk-access-token" => $token,
        "Content-Type" => "application/json"
    );
    
    $data = array(
        "openConversationId" => $openConversationId,
        "userIds" => $user,
        "role" => 3
    );
    
    $res = requestsDingtalkApi("PUT", [0, "/v1.0/im/sceneGroups/subAdmins"], $data, $headers, 20)["body"];
    $res = json_decode($res, true);

    return $res;
}  

function create_group($token, $icon = '@lADPDfJ6dUX2_FPNAljNAlg', $template_id = null, $title = "新建群", $oid = null, $aid = null,$uids = null) {
    if ($uids == null || $template_id == null) {
        return false;
    }
    $headers = array(
        "Content-Type" => "application/json"
    );
    
    $data = array(
    	"icon"=>$icon,
    	"template_id"=>$template_id,
    	"title"=>$title,
    	"owner_user_id"=>$oid,
    );
    
    $res = requestsDingtalkApi("POST", [1,"/topapi/im/chat/scenegroup/create?access_token=$token"], $data, $headers, 20)["body"];
    $res = json_decode($res, true);

    return $res;
}

function get_downloadfile($token,$dc,$rc) {
    
    $headers = array(
        "Content-Type" => "application/json",
        "x-acs-dingtalk-access-token" => $token
    );
    
    $data = array(
	"downloadCode"=>$dc,
	"robotCode"=>$rc
    );
    
    $res = requestsDingtalkApi("POST", [0,"/v1.0/robot/messageFiles/download"], $data, $headers, 20)["body"];
    $res = json_decode($res, true);
    return $res;
}

function requests_download_file($url, $saveDir, $method = 'GET', $data = null, $header = [], $timeout = 180, $filename = null) {
    $ch = curl_init();

    switch (strtoupper($method)) {
        case "POST":
            curl_setopt($ch, CURLOPT_POST, true);
            break;
        case "PUT":
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
            break;
        default:
            curl_setopt($ch, CURLOPT_HTTPGET, true);
    }

    if ($data && in_array(strtoupper($method), ['POST', 'PUT'])) {
        if (isset($header["Content-Type"]) && $header["Content-Type"] == 'application/json') {
            $data = json_encode($data);
        }
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    }

    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

    if (!empty($header)) {
        $header_arr = [];
        foreach ($header as $key => $value) {
            $header_arr[] = $key . ': ' . $value;
        }
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header_arr);
    }

    $response = curl_exec($ch);
    $status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    if ($response === false) {
        $error_msg = curl_error($ch);
        curl_close($ch);
        return [
            'code' => 500,
            'body' => "cURL error: " . $error_msg
        ];
    }

    curl_close($ch);

    if ($status_code == 200) {
        $url_parts = parse_url($url);
        $path_parts = pathinfo($url_parts['path']);
        if ($filename === null) {
            $filename = $path_parts['basename'];
        }

        $savePath = rtrim($saveDir, '/') . '/' . $filename;

        if (file_put_contents($savePath, $response) === false) {
            return [
                'code' => 500,
                'body' => '文件保存失败'
            ];
        }

        return [
            'code' => $status_code,
            'message' => '文件下载成功',
            'saved_file' => $savePath
        ];
    }

    return [
        'code' => $status_code,
        'body' => $response
    ];
}
function group_info($token, $groupid) {
    $headers = array(
        "Content-Type" => "application/json"
    );
    
    $data = array(
    	"open_conversation_id"=>$groupid
    );
    $res = requestsDingtalkApi("POST", [1,"/topapi/im/chat/scenegroup/get?access_token=$token"], $data, $headers, 20)["body"];
    $res = json_decode($res, true);

    return $res;
}

function change_group_owner($token, $gid, $oid) {
    $headers = array(
        "Content-Type" => "application/json"
    );
    
    $data = array(
    	"open_conversation_id"=>$gid,
    	"owner_user_id"=>$oid,
    );
    $res = requestsDingtalkApi("POST", [1,"/topapi/im/chat/scenegroup/update?access_token=$token"], $data, $headers, 20)["body"];
    $res = json_decode($res, true);

    return $res;
}

function group_add_member($token, $groupIds, $uids) {
    $headers = array(
        "Content-Type" => "application/json"
    );

    if (!is_array($groupIds)) {
        $groupIds = array($groupIds);
    }

    $results = array();

    foreach ($groupIds as $groupId) {
        if (is_array($uids) && count($uids) > 100) {
            $uidChunks = array_chunk($uids, 100);
            foreach ($uidChunks as $uidChunk) {
                $data = array(
                    "user_ids" => implode(",", $uidChunk),
                    "open_conversation_id" => $groupId
                );

                $res = requestsDingtalkApi("POST", [1,"/topapi/im/chat/scenegroup/member/add?access_token=$token"], $data, $headers, 20)["body"];
                $res = json_decode($res, true);
                $results[] = $res;
            }
        } else {
            $data = array(
                "user_ids" => is_array($uids) ? implode(",", $uids) : $uids,
                "open_conversation_id" => $groupId
            );

            $res = requestsDingtalkApi("POST", [1,"/topapi/im/chat/scenegroup/member/add?access_token=$token"], $data, $headers, 20)["body"];
            $res = json_decode($res, true);

            $results[] = $res;
        }
    }
    return $results;
}



function group_member_get($token, $groupid) {
    $headers = array(
        "Content-Type" => "application/json"
    );
    
    $data = array(
    	"cursor"=>"1",
    	"size"=>1000,
    	"open_conversation_id"=>$groupid
    );
    
    $res = requestsDingtalkApi("POST", [1,"/topapi/im/chat/scenegroup/member/get?access_token=$token"], $data, $headers, 20)["body"];
    $res = json_decode($res, true);

    return $res;
}

function org_delete_user($token, $userid) {
    $headers = array(
        "Content-Type" => "application/json"
    );
    
    $data = array(
    	"userid"=>$userid
    );
    
    $res = requestsDingtalkApi("POST", [1,"/topapi/v2/user/delete?access_token=$token"], $data, $headers, 20)["body"];
    $res = json_decode($res, true);

    return $res;
}

function kick($token,$user_ids,$open_conversation_id) {
    
    $headers = array(
        "Content-Type" => "application/json"
    );
    
    $data = array(
    	"user_ids"=>$user_ids,
    	"open_conversation_id"=>$open_conversation_id
    );
    
    $res = requestsDingtalkApi("POST", [1,"/topapi/im/chat/scenegroup/member/delete?access_token={$token}"], $data, $headers, 20)["body"];
    $res = json_decode($res, true);
    return $res;
}

function send_interactiveCards($token, $cardTemplateId, $openConversationId, $robotCode, $conversationType, $cardData=["cardParamMap"=>[]], $receiverUserIdList = null, $outTrackId = null, $callbackRouteKey = "", $cardOptions = ["supportForward" => false]) {
    global $bot_run_as;
    if ($outTrackId === null) {
        $outTrackId = uuid() . "-" . uuid();
    }
    
    $headers = array(
        "Content-Type" => "application/json",
        "x-acs-dingtalk-access-token" => $token
    );
    
    $data = array(
        "cardData"=>$cardData,
        "conversationType"=>$conversationType,
    	"callbackRouteKey"=>$callbackRouteKey,
    	"cardTemplateId"=>$cardTemplateId,
    	"outTrackId"=>$outTrackId,
    	"robotCode"=>$robotCode,
    	"openConversationId"=>$openConversationId,
    	"cardOptions"=>$cardOptions
    );
    
    $res = requestsDingtalkApi("POST", [0,"/v1.0/im/interactiveCards/send"], $data, $headers, 20)["body"];
    $res = json_decode($res, true);
    
    if ($res['success']) {
        $log = read_file_to_array('data/bot/card/card.json');
        $log[$outTrackId] = ['data'=>$data,'webhook'=>$bot_run_as['webhook'],'processQueryKey'=>$res['result']['processQueryKey']];
        write_to_file_json('data/bot/card/card.json',$log);
    }
    
    return $res;
}

function create_AI_interactiveCards($token, $cardData, $outTrackId = null, $cardTemplateId = "8f250f96-da0f-4c9f-8302-740fa0ced1f5.schema", $cardOptions = ["imGroupOpenSpaceModel" => ["supportForward" => false]]) {
    global $bot_run_as;
    if ($outTrackId === null) {
        $outTrackId = uuid() . "-" . uuid();
    }
    
    $headers = array(
        "Content-Type" => "application/json",
        "x-acs-dingtalk-access-token" => $token
    );
    
    $data = array(
        "cardData"=>$cardData,
        "cardTemplateId"=>$cardTemplateId,
        "outTrackId"=>$outTrackId,
        "userIdType"=>1
    );
    foreach ($cardOptions as $k => $v) {
        $data[$k] = $v;
    }
    
    $res = requestsDingtalkApi("POST", [0,"/v1.0/card/instances"], $data, $headers, 20)["body"];
    $res = json_decode($res, true);
    
    return [$res, $outTrackId];
}

function deliver_AI_interactiveCards($token, $outTrackId, $openSpaceId, $cardOptions = []) {
    global $bot_run_as;
    $headers = array(
        "Content-Type" => "application/json",
        "x-acs-dingtalk-access-token" => $token
    );
    
    $data = array(
        "outTrackId"=>$outTrackId,
        "openSpaceId"=>$openSpaceId
    );
    foreach ($cardOptions as $k => $v) {
        $data[$k] = $v;
    }
    
    $res = requestsDingtalkApi("POST", [0,"/v1.0/card/instances/deliver"], $data, $headers, 20)["body"];
    $res = json_decode($res, true);
    
    return [$res, $outTrackId];
}

function streaming_AI_interactiveCards($token, $outTrackId, $key, $content, $guid = null, $isFull = true, $isFinalize = false, $isError = false) {
    global $bot_run_as;
    if ($guid === null) {
        $guid = time()."_".uuid();
    }
    
    $headers = array(
        "Content-Type" => "application/json",
        "x-acs-dingtalk-access-token" => $token
    );
    
    $data = array(
        "outTrackId"=>$outTrackId,
        "key"=>$key,
        "content"=>$content,
        "guid"=>$guid,
        "isFull"=>$isFull,
        "isFinalize"=>$isFinalize,
        "isError"=>$isError
    );
    
    $res = requestsDingtalkApi("PUT", [0,"/v1.0/card/streaming"], $data, $headers, 20)["body"];
    $res = json_decode($res, true);
    
    return [$res, $outTrackId];
}

function update_interactiveCards($token,$cardid, $cardData) {
    $headers = array(
        "Content-Type" => "application/json",
        "x-acs-dingtalk-access-token" => $token
    );
    
    $data = array(
        "outTrackId"=>$cardid,
        "cardData"=>$cardData
    );
    
    $res = requestsDingtalkApi("PUT", [0,"/v1.0/im/interactiveCards"], $data, $headers, 20)["body"];
    $res = json_decode($res, true);
    
    return $res;
}

function requests($method, $url, $data = null, $header = [], $timeout = 20) {
    $ch = curl_init();
    if (strtoupper($method) == "POST") {
        curl_setopt($ch, CURLOPT_POST, true);
        if (is_array($data)) {
            if ($header["Content-Type"] == 'application/json') {
                $data = json_encode($data);
            }
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        }
    }  elseif (strtoupper($method) === "PUT") {
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
        if ($data) {
            if ($header["Content-Type"] == 'application/json') {
                $data = json_encode($data);
            }
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        }
    } else {
        curl_setopt($ch, CURLOPT_HTTPGET, true);
    }
    
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);     //SSL证书验证
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);

    if ($header != null) {
        $header_arr = array();
        foreach ($header as $key => $value) {
            $header_arr[] = $key . ': ' . $value;
        }
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header_arr);
    }

    try {
        $response = curl_exec($ch);
        $status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if ($response === false) {
            //throw new Exception("cURL error: " . curl_error($ch));
            return(curl_error($ch));
        }

        curl_close($ch);

        return array(
            'status_code' => $status_code,
            'body' => $response,
        );
    } catch (Exception $e) {
        curl_close($ch);
        return array(
            'status_code' => 500,
            'body' => $e->getMessage()
        );
    }
}

function DingraiaPHPCheckWarningWord($c) {
    global $bot_run_as;
    $ww = ["这是违禁词功能，请自行添加"];
    foreach ($ww as $w) {
        if (strstr($c, $w)) {
            $lid = tool_log(2, ["content"=>$c, "bot"=>$bot_run_as]);
            return "检测到违禁词。请查看日志id：{$lid}";
        }
    }
    return $c;
}

function get_message($lx, $content, $Atuser, $title, $pic, $link, $x){
    global $globalmessage;
    global $Atitle;
    global $actionURL;
    global $ApicUrl;
    global $conversationType;
    global $bot_run_as;
    
    $endTime = microtime(true);
    $endMemory = memory_get_usage();
    $executionTime = $endTime - $bot_run_as['startTime'];
    $executionTime = round($executionTime, 4);
    $usedMemory = $endMemory - $bot_run_as['startMemory'];
    $usedMemory = formatBytes($usedMemory);
    if ($bot_run_as["config"]['botSendTestInfo'] == 1) {
        $testbot = "time used ".$executionTime."s,memory used ".$usedMemory;
    } else {
        $testbot = null;
    }
    if ($conversationType != "2") {
        $Atuser = 0;
    }
    $isAtAll = false;
    if ($Atuser != 0) {
        if ($Atuser == 1) {
            $isAtAll = true;
        } else {
            if (is_array($content)) {
                $content = json_encode($content,JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE);
            }
            $content = '@'.$Atuser." ".$content;
            $isAtAll = false;
        }
    }

    if (is_array($content)) {
        $content = json_encode($content,JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE);
    } else {
        $content = DingraiaPHPCheckWarningWord($content);
    }
    if ($lx == "text"){
        $message = array(
            "at" => array(
                "atUserIds" => [
                    $Atuser
                ],
                "isAtAll" => $isAtAll
            ),
            "text" => array(
                "content" => $content."\n".$testbot
                //"content" => $content;
            ),
            "msgtype" => "text"
        );
    }elseif ($lx == 'md') {
        $message = [
            "at" => array(
                "atUserIds" => [
                    $Atuser
                ],
                "isAtAll" => $isAtAll
            ),
            "msgtype" => "markdown",
            "markdown" => [
                "title" => $title,
                "text" => $content
            ]
        ];
    }elseif ($lx == "lk") {
        $message = [
            "msgtype" => "link",
            "link" => [
                "title" => $title,
                "text" => $content,
                "picUrl" => $pic,
                "messageUrl" => $link
            ]
        ];
    } elseif ($lx == "sampleText") {
        $message = [
            "msgtype" => "sampleText",
            "sampleText" => [
                "content" => $content
            ]
        ];
    } elseif ($lx == "actionCardB") {
        if (is_array($actionURL) and is_array($Atitle) and count($Atitle) == count($actionURL)) {
            $btns = array();
            
            for ($i = 0; $i < count($Atitle); $i++) {
                if ($pic) {
                    $actionURLA = "dingtalk://dingtalkclient/page/link?url=" . urlencode($actionURL[$i]) . "&pc_slide=true";
                } else {
                    $actionURLA = "dingtalk://dingtalkclient/page/link?url=" . urlencode($actionURL[$i]) . "&pc_slide=false";
                }
                
                $btn = array(
                    "title" => $Atitle[$i],
                    "actionURL" => $actionURLA
                );
            
                array_push($btns, $btn);
            }
            
            $message = array(
                "msgtype" => "actionCard",
                "actionCard" => array(
                    "title" => $title,
                    "text" => $content,
                    "btnOrientation" => "0",
                    "btns" => $btns
                )
            );
        } else {
            return false;
        }
    } elseif ($lx == "actionCardA") {
        if ($x) {
            $link = "dingtalk://dingtalkclient/page/link?url=" . urlencode($link) . "&pc_slide=true";
        } else {
            $link = "dingtalk://dingtalkclient/page/link?url=" . urlencode($link) . "&pc_slide=false";
        }
        $message = array(
            "msgtype" => "actionCard",
            "actionCard" => array(
                "title" => $title,
                "text" => $content,
                "btnOrientation" => "0",
                "singleTitle" => $pic,
                "singleURL" => $link
            )
        );
    } elseif ($lx == "fc") {
        if (is_array($actionURL) and is_array($Atitle) and is_array($ApicUrl) and count($Atitle) == count($actionURL) and count($Atitle) == count($ApicUrl)) {
            $btns = array();
            $links = array();
            
            for ($i = 0; $i < count($Atitle); $i++) {
                $link = array(
                    "title" => $Atitle[$i],
                    "messageURL" => $actionURL[$i],
                    "picURL" => $ApicUrl[$i]
                );
            
                array_push($links, $link);
            }
            $message = array(
                "msgtype" => "feedCard",
                "feedCard" => array(
                    "links" => $links
                )
            );
        } else {
            return false;
        }
    }
    return $message;
}

function convertSpecialChars($string) {
    $string = str_replace("\n", '\n', $string);
    $string = str_replace("\r", '\r', $string);
    return $string;
}

function send($lx, $content, $url, $Atuser, $title = "title", $pic = "", $link = "", $x = false) {
    $webhook1 = $url;
    global $globalmessage;
    global $bot_run_as;
    if ($content == ""){
        return false;
    } elseif (is_array($content)) {
        $content = json_encode($content,JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE);
    }
    $bot_run_as["logger"]["class"]->info("<- ".convertSpecialChars($content));
    if (isset($bot_run_as["sendWebhookSecret"]) && $bot_run_as["sendWebhookSecret"] !== 0) {
        $secret = $bot_run_as["sendWebhookSecret"];
    } else {
        $secret = "";
    }
    // 生成时间戳和加签
    date_default_timezone_set('Asia/Shanghai');
    $timestamp = strval(round(microtime(true) * 1000));
    $string_to_sign = $timestamp . "\n" . $secret;
    $sign = urlencode(base64_encode(hash_hmac('sha256', $string_to_sign, $secret, true)));
    // 拼接 WebHook 地址
    $webhook = $url . "&timestamp=" . $timestamp . "&sign=" . $sign;
    // 构建消息体
    $message = get_message($lx, $content, $Atuser, $title, $pic, $link, $x);
    if (!check_send_lq($webhook1)) {
        return false;
    }
    $header = array(
        "Content-Type: application/json;charset=utf-8"
    );

    $result = DingraiaPHPPostNormalChat($message, $webhook, $header);
    //log_message($webhook1, $message, $content, $result, $globalmessage);
    return $result;
}

function get_groupMessages($lx, $robotCode, $conversationId, $content, $b, $c, $d) {
    if ($lx == "text" or $lx == "text_touser") {
        $content = json_encode(["content"=>$content]);
        $message = array(
            "msgParam" => $content,
            "msgKey" => "sampleText",
            "robotCode" => $robotCode,
            "openConversationId" => $conversationId
        );
    if ($lx == "md" or $lx == "md_touser") {
        $content = json_encode(["content"=>$content]);
        $message = array(
            "text" => $content,
            "msgParam" => $content,
            "title" => "sampleMarkdown",
            "msgKey" => "sampleMarkdown",
            "robotCode" => $robotCode,
            "openConversationId" => $conversationId
        );
    } 
    } elseif ($lx == "img") {
        $photoURL = json_encode(["photoURL"=>$content]);
        $message = array(
            "msgParam" => $photoURL,
            "msgKey" => "sampleImageMsg",
            "robotCode" => $robotCode,
            "openConversationId" => $conversationId
        );
    } elseif ($lx == "file") {
        $msgParam = json_encode(["mediaId"=>$content, "fileName"=>$b, "fileType"=>$c]);
        $message = array(
            "msgParam" => $msgParam,
            "msgKey" => "sampleFile",
            "robotCode" => $robotCode,
            "openConversationId" => $conversationId
        );
    } elseif ($lx == "audio") {
        $msgParam = json_encode(["mediaId"=>$content, "duration"=>$b]);
        $message = array(
            "msgParam" => $msgParam,
            "msgKey" => "sampleAudio",
            "robotCode" => $robotCode,
            "openConversationId" => $conversationId
        );
    } elseif ($lx == "video") {
        $msgParam = json_encode(["duration"=>$c, "videoMediaId"=>$content, "videoType"=>"mp4", "picMediaId"=>$b]);
        $message = array(
            "msgParam" => $msgParam,
            "msgKey" => "sampleVideo",
            "robotCode" => $robotCode,
            "openConversationId" => $conversationId
        );
    }
    
    return $message;
}

function send_groupMessages($lx, $url, $a, $b=0, $c=0, $d=0) {
    global $bot_run_as;
    global $globalmessage;
    global $robotCode;
    global $conversationId;
    global $chatbotCorpId;
    
    $chatbotCorpId = $chatbotCorpId ?? $bot_run_as["data"]["chatbotCorpId"];
    if ($a == ""){
        return false;
    }
    $url2 = $url;
    $webhook = "https://api.dingtalk.com/v1.0/robot/groupMessages/send";
    $url = str_replace('https://oapi.dingtalk.com/robot/sendBySession?session=', '', $url2);
    $cropidkey = read_file_to_array("config/cropid.json")[$chatbotCorpId];
    $token = get_accessToken($cropidkey['AppKey'],$cropidkey['AppSecret']);
    if ($lx == "audio") {
        $a = upload_to_dingtalk_v2("voice", $a, $token);
    }
    if ($lx == "file") {
        $a = upload_to_dingtalk_v2("file", $a, $token);
    }
    if ($lx == "video") {
        $b = upload_to_dingtalk_v2("image", $b, $token);
        $a = upload_to_dingtalk_v2("video", $a, $token);
    }
    $message = get_groupMessages($lx, $robotCode, $conversationId, $a, $b, $c, $d);
    if (strstr($lx, 'text_touser') and $c != null) {
        unset($message['openConversationId']);
        $message['userIds'] = $c;
        $webhook = 'https://api.dingtalk.com/v1.0/robot/oToMessages/batchSend';
    }
    if (strstr($lx, 'md_touser') and $d != null) {
        unset($message['openConversationId']);
        $message['userIds'] = $d;
        $message['title'] = $b;
        $webhook = 'https://api.dingtalk.com/v1.0/robot/oToMessages/batchSend';
    }
    $header = array(
        "x-acs-dingtalk-access-token: $token",
        "Content-Type: application/json"
    );

    $result = DingraiaPHPPostNormalChat($message, $webhook, $header);
    DingraiaPHPAddNormalResponse("group", $result, true);
    write_to_file_json('log2.json', [$result.json_encode($message).json_encode($header), $result]);
    if ($lx == 'text' or $lx == 'img' or $lx == "text_touser") {
        if ($b != 0 and is_numeric($b)) {
            if (isset(json_decode($result, true)['processQueryKey'])) {
                $mk = json_decode($result, true)['processQueryKey'];
                if (strstr($lx, '_touser') and $c != null) {
                    $res = userMessages_recall($token, $url2, $robotCode,$b, $mk);
                    return $result;
                }
                $res = groupMessages_recall($token, $url2, $robotCode,$conversationId,$b, $mk);
            }
        }
    }
    return $result;
}

/**
 * @param array $message
 * @param string $webhook
 * @param array $header
 * @return bool|string
 */
function DingraiaPHPPostNormalChat(array $message, string $webhook, array $header)
{
    $data_string = json_encode($message, JSON_UNESCAPED_UNICODE);

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $webhook);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $result = curl_exec($ch);
    curl_close($ch);
    return $result;
}

function groupMessages_recall($token, $url,$robotCode, $conversationId, $timeout, $mk) {
    sleep($timeout);
    
    $headers = array(
        "x-acs-dingtalk-access-token" => $token,
        "Content-Type" => "application/json"
    );
    
    $data = array(
        "robotCode" => $robotCode,
        "openConversationId" => $conversationId,
        "processQueryKeys" => [$mk]
    );

    return requests("POST", "https://api.dingtalk.com/v1.0/robot/groupMessages/recall", $data, $headers, 20)["body"];
}

function userMessages_recall($token, $url,$robotCode, $timeout, $mk) {
    sleep($timeout);
    
    $headers = array(
        "x-acs-dingtalk-access-token" => $token,
        "Content-Type" => "application/json"
    );
    
    $data = array(
        "robotCode" => $robotCode,
        "openConversationId" => $conversationId,
        "processQueryKeys" => [$mk]
    );

    return requests("POST", "https://api.dingtalk.com/v1.0/robot/otoMessages/batchRecall", $data, $headers, 20)["body"];
}

function groupMessages_recall_v2($token,$robotCode, $conversationId, $timeout, $mk) {
    sleep($timeout);
    
    $headers = array(
        "x-acs-dingtalk-access-token" => $token,
        "Content-Type" => "application/json"
    );
    
    $data = array(
        "robotCode" => $robotCode,
        "openConversationId" => $conversationId,
        "processQueryKeys" => $mk
    );

    return requests("POST", "https://api.dingtalk.com/v1.0/robot/groupMessages/recall", $data, $headers, 20)["body"];
}

function sampleText($content, $url, $timeout = 0, $touser = null){
    global $bot_run_as;
    if ($timeout > 45) {
        send_message('撤回时间不能大于45秒',$url, $staffid);
        return false;
    } else { 
        if ($touser != null) {
            return(send_groupMessages('text_touser', $url,$content, $timeout, $touser));
        }
        return(send_groupMessages('text', $url,$content, $timeout, $touser));
    }
}

function sampleMarkdown($content, $webhook, $title, $staffid) {
    if ($touser != null) {
        return (send_groupMessages('md_touser', $webhook, $content, $title, $timeout, $touser));
    } else {
        return (send_groupMessages('md', $webhook, $content, $title, $staffid));
    }
    return true;
}

function sampleImageMsg($content, $url, $timeout = 0){
    if ($timeout > 45) {
        send_message('撤回时间不能大于45秒',$url, $staffid);
        return false;
    } else {
        send_groupMessages('img', $url, $content, $timeout);
    }
}

function sampleAudio($content, $url, $b, $timeout = 0){
    global $restaffid, $bot_run_as;
    if (filesize($content) > 2000000) {
        send_message($bot_run_as["lang"]["upload_file_big"],$url, $restaffid);
        return false;
    } else {
        if ($b == -1) {
            $b = getOGGDurationInMilliseconds($content);
            $b = intval($b);
        }
        $r = send_groupMessages('audio', $url, $content, $b);
    }
    return $r;
}

function sampleFile($content, $url, $b, $c, $timeout = 0){
    global $restaffid, $bot_run_as;
    if (filesize($content) > 20000000) {
        send_message($bot_run_as["lang"]["upload_file_big"],$url, $restaffid);
        return false;
    } else {
        if (in_array($c, ["dco","docx","xls","xlsx","ppt","pptx","pdf","zip","rar"])) {
            send_groupMessages('file', $url, $content, $b, $c);
            return true;
        } else {
            send_message($bot_run_as["lang"]["upload_file_bad_extension"],$url, $restaffid);
        }
    }
    return false;
}


function sampleVideo($content, $url, $b, $c = "mp4", $timeout = 0){
    //视频 webhook 封面 mp4 
    global $restaffid, $bot_run_as;
    if (filesize($content) > 20000000) {
        send_message($bot_run_as["lang"]["upload_file_big"],$url, $restaffid);
        return false;
    } else {
        if (in_array($c, ["mp4"])) {
            $c = intval(getMp4DurationInMilliseconds($content)) / 1000;
            send_groupMessages('video', $url, $content, $b, $c);
            return true;
        } else {
            send_message($bot_run_as["lang"]["upload_file_bad_extension"],$url, $restaffid);
        }
    }
    return false;
}

function send_message($content, $url, $Atuser = 0){
    return(send('text', $content, $url, $Atuser));
}

function send_test($content, $url, $Atuser = 0){
    return(send('test', $content, $url, $Atuser));
}

function send_markdown($content, $url, $title = "title", $Atuser = 0){
    return send('md', $content, $url, $Atuser, $title);
}

function send_link($content, $url, $Atuser = 0, $title = "title", $pic = "", $link = ""){
    if ($Atuser != 0) {
        return false;
    }
    send('lk', $content, $url, $Atuser, $title, $pic, $link);
}

function send_actionCardA($content, $url, $Atuser = 0, $title = "title", $singleTitle = null, $singleURL = null, $x = false) {
    if ($Atuser != 0) {
        return false;
    }
    send("actionCardA", $content, $url, $Atuser, $title, $singleTitle, $singleURL, $x);
}

function send_actionCardB($content, $url, $Atuser = 0, $title = "title", $open = false) {
    if ($Atuser != 0) {
        return false;
    }
    send("actionCardB", $content, $url, $Atuser, $title, $open);
}

function send_feedcard($content, $url, $Atuser = 0, $title = "title", $open = false) {
    if ($Atuser != 0) {
        return false;
    }
    send("fc", $content, $url, $Atuser, $title, $open);
}


function parseCustomTimeFormat($timeString) {
    $timeUnits = array('y' => 31536000, 'm' => 2592000, 'd' => 86400, 'h' => 3600, 'i' => 60, 's' => 1);
    $totalSeconds = 0;
    
    preg_match_all('/(\d+)([ymdhis])/i', $timeString, $matches, PREG_SET_ORDER);

    foreach ($matches as $match) {
        $value = intval($match[1]);
        $unit = strtolower($match[2]);
        
        if (array_key_exists($unit, $timeUnits)) {
            $totalSeconds += $value * $timeUnits[$unit];
        }
    }
    
    return $totalSeconds;
}

function formatTimeFromSeconds($seconds): string{
    $timeUnits = array('y' => 31536000, 'm' => 2592000, 'd' => 86400, 'h' => 3600, 'i' => 60, 's' => 1);
    $formattedTime = "";
    
    foreach ($timeUnits as $unit => $value) {
        if ($seconds >= $value) {
            $numUnits = floor($seconds / $value);
            $formattedTime .= $numUnits . $unit;
            $seconds %= $value;
        }
    }
    
    return $formattedTime;
}

function userid2uid($userid){
    global $staffid, $rename, $bot_run_as;
    if (!isset($staffid)) {
        return null;
    }
    $name = $rename;
    $file_path = "data/bot/user/{$staffid}.json";

    if (file_exists($file_path)) {
        $user_data = json_decode(file_get_contents($file_path), true);
        if ($user_data['userid'] !== $userid) {
            $user_data['userid'] = $userid;
            file_put_contents($file_path, json_encode($user_data));
        }
        app_json_file_add_list($bot_run_as["RUN_LOG_FILE"], ["time" => microtime(true), "type" => "userid2uid", "userid" => $userid, "result" => $user_data, "userType" => "old"]);
        return $user_data;
    } else {
        $uid = count(glob("data/bot/user/*.json")) + 10001;
        $new_user_data = [
            'userid' => $userid,
            'uid' => $uid,
            'wuid' => 0,
            'staffid' => $staffid,
            'name' => htmlspecialchars($name),
            'money' => 0,
            'ban' => 0
        ];
        file_put_contents($file_path, json_encode($new_user_data));
        app_json_file_add_list($bot_run_as["RUN_LOG_FILE"], ["time" => microtime(true), "type" => "userid2uid", "userid" => $userid, "result" => $new_user_data, "userType" => "new"]);
        return $new_user_data;
    }
}

function uid2userinfo($uid) {
    global $bot_run_as;
    $user_files = glob("data/bot/user/*.json");
    foreach ($user_files as $file) {
        $user_data = json_decode(file_get_contents($file), true);
        if ($user_data['uid'] == $uid) {
            $result = [
                'userid' => $user_data['userid'],
                'uid' => $uid,
                'wuid' => $user_data['wuid'],
                'staffid' => $user_data['staffid'],
                'name' => $user_data['name'],
                'money' => $user_data['money'],
                'ban' => $user_data['ban']
            ];
            app_json_file_add_list($bot_run_as["RUN_LOG_FILE"], ["time" => microtime(true), "type" => "uid2userinfo", "uid" => $uid, "result" => $result]);
            return $result;
        }
    }
    app_json_file_add_list($bot_run_as["RUN_LOG_FILE"], ["time" => microtime(true), "type" => "uid2userinfo", "uid" => $uid, "result" => false]);
    return false;
}

function staffid2userinfo($staffid) {
    global $bot_run_as;
    $file_path = "data/bot/user/{$staffid}.json";
    if (file_exists($file_path)) {
        $user_data = json_decode(file_get_contents($file_path), true);
        $result = [
            'userid' => $user_data['userid'],
            'uid' => $user_data['uid'],
            'wuid' => $user_data['wuid'],
            'staffid' => $staffid,
            'name' => $user_data['name'],
            'money' => $user_data['money'],
            'ban' => $user_data['ban']
        ];
        app_json_file_add_list($bot_run_as["RUN_LOG_FILE"], ["time" => microtime(true), "type" => "staffid2userinfo", "staffid" => $staffid, "result" => $result]);
        return $result;
    }
    app_json_file_add_list($bot_run_as["RUN_LOG_FILE"], ["time" => microtime(true), "type" => "staffid2userinfo", "staffid" => $staffid, "result" => false]);
    return false;
}

function write_to_file_json($filename, $content) {
    global $bot_run_as;
    if (is_array($content)) {
        $content = json_encode($content, JSON_UNESCAPED_UNICODE);
    }
    file_put_contents($filename, $content);
    app_json_file_add_list($bot_run_as["RUN_LOG_FILE"], ["time"=>microtime(true),"type"=>"write_to_file_json","filename"=>$filename,"filesize"=>filesize($filename)]);
}

function read_file_to_array($filename) {
    global $bot_run_as;
    if (isFileExists($filename)) {
        $content = file_get_contents($filename);
        app_json_file_add_list($bot_run_as["RUN_LOG_FILE"], ["time"=>microtime(true),"type"=>"read_file_to_array","filename"=>$filename,"filesize"=>filesize($filename)]);
        return json_decode($content, true);
    } else {
        return false;
    }
}

function app_json_file_add_list_validateInput($t) {
   if (!is_array($t) && !(is_object($t) && $t instanceof ArrayAccess)) {
        throw new InvalidArgumentException("Input must be an array or implement ArrayAccess.");
    }
}

function app_json_file_add_list($fp, $t) {
    global $bot_run_as;
    app_json_file_add_list_validateInput($t);

    if (empty($fp) || !$fp) {
        $fp = $bot_run_as["RUN_LOG_FILE"];
    }

    if (file_exists($fp)) {
        $r = json_decode(file_get_contents($fp), true);
        $r[] = $t;
        file_put_contents($fp, json_encode($r, JSON_UNESCAPED_UNICODE));
    } else {
        return false;
    }
}

function DingraiaPHPAddNormalResponse($key = null,$t = null,$newArray = false) {
    $r = json_decode(file_get_contents("data/bot/app/response.json"), true);
    if ($key == null) {
        $key = "emptyKey";
        $r["result"][$key][] = $t;
    } else {
        if ($newArray) {
            $r["result"][$key][] = $t;
        } else {
            $r["result"][$key] = $t;
        }
    }
    if (is_array($r)) {
        file_put_contents("data/bot/app/response.json", json_encode($r, JSON_UNESCAPED_UNICODE));
    } else {
        return false;
    }
}

function useRobotcode2Corpid($robotCode) {
    $r = read_file_to_array("config/cropid.json");
    if (isset($r['APP'][$robotCode])) {
        return($r['APP'][$robotCode]);
    } else {
        return false;
    }
}

function isFileExists($filePath): bool
{
    return file_exists($filePath);
}

function DingraiaPHPLogaChatGroupMessage($data,$r) {
    $cid = $data['body']["conversationId"];
    $body['msgtype'] = isset($body['msgtype']) ?? "text";
    if ($body['msgtype'] == 'picture') {
        $r = useRobotcode2Corpid($data["body"]['robotCode']);
        $token = get_accessToken($r['AppKey'],$r['AppSecret']);
        
        $logData = ["senderStaffId" => $data['body']['senderStaffId'],"name"=>base64_encode($data['body']['name']),"picture"=>$fp];
    } else {
        $logData = ["senderStaffId" => $data['body']['senderStaffId'],"name"=>base64_encode($data['body']['name']),"message"=>base64_encode($data["body"]["message"])];
    }
    if ($data['body']['conversationType'] == "2") {
        $r[$cid]['chatName'] = base64_encode($data['body']['conversationTitle']);
    } elseif ($data['body']["conversationType"] == "1") {
        $r[$cid]['chatName'] = base64_encode($data['body']['name']);
    }
    $r[$cid]['robotCode'] = $data['body']['robotid'];
    $r[$cid]['chatType'] = $data['body']["conversationType"];
    $r[$cid]['message'][] = $logData;
    write_to_file_json("data/bot/chatUseGroup.json",$r);
}

function DingraiaPHPLogChatGroup($data): bool {
    $data = $data[0];
    $webhookDie = time() + 3540;
    $t = time();
    $r = read_file_to_array("data/bot/chatUseGroup.json");
    if (empty($data['body']["conversationId"])) {
        return false;
    } else {
        $cid = $data['body']["conversationId"];
        if (isset($r[$cid])) {
            $dt = $r[$cid]['webhookDieTime'];
            if ($t > $dt) {
                $r[$cid]['webhook'] = $data['body']['webhook'];
                DingraiaPHPLogaChatGroupMessage($data,$r);
            } else {
                DingraiaPHPLogaChatGroupMessage($data,$r);
            }
        } else {
            $r[$cid]['webhookDieTime'] = $webhookDie;
            $r[$cid]['webhook'] = $data['body']['webhook'];
            DingraiaPHPLogaChatGroupMessage($data,$r);
        }
    }
    
    return false;
}

function log_message($webhook, $message, $lx, $status, $globalmessage): bool
{
    global $rename;
    $filename = "data/send.json";
    $arr = array();

    if (file_exists($filename)) {
        $data = file_get_contents($filename);
        $arr = json_decode($data, true);
    }

    if (!isset($arr['sendwebhook'])) {
        $arr['sendwebhook'] = array();
    }
    if (!isset($arr['sendwebhook'][$webhook])) {
        $arr['sendwebhook'][$webhook] = array(
            "last_send" => time(),
            "allsend" => 0,
            "minute_send" =>0,
            "send_lq" => time()+65,
            "name" => $rename
        );
    }

    if (!isset($arr['message_log'])) {
        $arr['message_log'] = array();
    }
    if (!isset($arr['message_log'][$webhook])) {
        $arr['message_log'][$webhook] = array();
    }

    $logEntry = array(
        "user_message" => $globalmessage,
        "time" => time(),
        "lx" => $message["msgtype"],
        "status" => json_encode($status),
        "reply_message" => $message[$message["msgtype"]]
    );
    // 将新的日志条目添加到现有数组中
    $arr['message_log'][$webhook][] = $logEntry;

    // 更新统计信息
    $arr['sendwebhook'][$webhook]['last_send'] = time();
    $arr['sendwebhook'][$webhook]['allsend'] += 1;
    $arr['sendwebhook'][$webhook]['minute_send'] += 1;

    // 写入文件
    $content = json_encode($arr, JSON_UNESCAPED_UNICODE);
    $result = file_put_contents($filename, $content);
    return $result !== false;
}

function log_group_message($robotCode, $conversationId, $lx, $status, $globalmessage, $reply): bool
{
    global $rename;
    $filename = "data/group_send.json";
    $arr = array();

    if (file_exists($filename)) {
        $data = file_get_contents($filename);
        $arr = json_decode($data, true);
    }

    if (!isset($arr['sendwebhook'])) {
        $arr['sendwebhook'] = array();
    }
    if (!isset($arr['sendwebhook'][$rename])) {
        $arr['sendwebhook'][$rename] = array(
            "last_send" => time(),
            "cid" => $conversationId,
            "rid" => $robotCode,
            "allsend" => 0,
            "minute_send" =>0,
            "name" => $rename
        );
    }

    if (!isset($arr['message_log'])) {
        $arr['message_log'] = array();
    }
    if (!isset($arr['message_log'][$rename])) {
        $arr['message_log'][$rename] = array();
    }

    $logEntry = array(
        "user_message" => $globalmessage,
        "time" => time(),
        "lx" => $lx,
        "status" => json_encode($status),
        "reply_message" => $reply
    );
    $arr['message_log'][$rename][] = $logEntry;
    $arr['sendwebhook'][$rename]['last_send'] = time();
    $arr['sendwebhook'][$rename]['allsend'] += 1;
    $arr['sendwebhook'][$rename]['minute_send'] += 1;
    $content = json_encode($arr, JSON_UNESCAPED_UNICODE);
    $result = file_put_contents($filename, $content);
    return $result !== false;
}

function check_send_lq($webhook): bool
{
    try {
        $filename = "data/send.json";
        $arr = read_file_to_array($filename);
        if (isset($arr['sendwebhook'][$webhook]) && $arr['sendwebhook'][$webhook]['minute_send'] >= 20) {
            if ($arr['sendwebhook'][$webhook]['send_lq'] > time()) {
                return false;
            } else {
                $arr['sendwebhook'][$webhook]['send_lq'] = time() + 65;
                $arr['sendwebhook'][$webhook]['minute_send'] = 0;
                $content = json_encode($arr, JSON_UNESCAPED_UNICODE);
                $result = file_put_contents($filename, $content);
                return true;
            }
        } else {
            return true;
        }
    } catch (Exception $e) {
        return false;
    }
}

function filterByStaffId($array): array
{
    if (!is_array($array)) {
        return [];
    }

    return array_filter($array, function($item) {
        return isset($item['staffId']);
    });
}

function get_dingtalk_post_check_sign($timestamp, $secret, $si): bool
{
    $secret_enc = utf8_encode($secret);
    $string_to_sign = $timestamp . "\n" . $secret;
    $string_to_sign_enc = utf8_encode($string_to_sign);
    $hmac_code = hash_hmac('sha256', $string_to_sign_enc, $secret_enc, true);
    $sign = base64_encode($hmac_code);
    if ($sign == $si) {
        return true;
    } else {
        return false;
    }
}

function get_dingtalk_card_callback($headers, $body) {
    if (isset($body['corpId'])) {
        $c['openConversationId'] = $body['openConversationId'];
        $c['outTrackId'] = $body['outTrackId'];
        $c['userId'] = $body['userId'];
        $c['content'] = $body['content'];
        $c['lxy_mode'] = 'card_callback';
        $c['chatbotCorpId'] = $body['corpId'];
        $c[] = $c;
        return $c; 
    } else {
        return false;
    }
}

function check_dingraiaAuth($corpId,$timestamp, $key): bool
{
    global $bot_run_as;
    $authkey = $bot_run_as['config']['dingraiaAuthKey'];
    $t = time() * 1000;
    $timeout = $bot_run_as['config']['dingraiaAuthTimeout'] * 1000 + $timestamp;
    $dataToHash = $corpId . $bot_run_as['config']['dingraiaAuthKey'] . $timestamp;
    $hashedData = hash('sha256', $dataToHash);
    $bot_run_as['trueDingraiaAuth'] = $hashedData;
    if ($t < $timestamp && $hashedData == $key) {
        return true;
    }
    return false;
}

function get_dingraia_master($headers, $body) {
    global $bot_run_as;
    if (isset($body['dingraiaAuth'])) {
        if (isset($body['chatbot_corp_id'])) {
            $corpId = $body['chatbot_corp_id'];
            $bot_run_as['dingraiaMasterType'] = "message";
        } else {
            $corpId = $body['CorpId'];
            $bot_run_as['dingraiaMasterType'] = "callback";
        }
        if (check_dingraiaAuth($corpId, $body['timeStamp'], $body['dingraiaAuth'])) {
            $c[] = $body;
            return $c;
        } else {
            return false;
        }
    }
}

function request_to_slave($corpId = null, $data = null) {
    if ($corpId == null) {
        $corpId = uuid();
    }
    global $bot_run_as;
    $authkey = $bot_run_as['config']['dingraiaAuthKey'];
    $timestamp = time().rand(100,999);
    $dataToHash = $corpId . $authkey . $timestamp;
    $hashedData = hash('sha256', $dataToHash);
    $headers = ["dingraiaAuth" => $hashedData,"Content-Type" => "application/json"];
    $data['timestamp'] = $timestamp;
    $data['CorpId'] = $corpId;
    $data['dingraiaAuth'] = $hashedData;
    $data['dingraia'] = 'master';
    
    $res = requests("POST", "https://api.dingtalk.com/v1.0/robot/groupMessages/recall", $data, $headers, 20)["body"];
    return $res;
}

function get_dingtalk_callback($timestamp,$nonce,$body) {
    $token = '1';
    $encodingAesKey = '2';
    $suiteKey = '3';
    
    $crypto = new Crypto(
        $token,
        $encodingAesKey,
        $suiteKey
    );
    $ret = $crypto->encryptMsg('success', $timestamp, $nonce);
    header("Content-Type: application/json");
    print_r($ret);
    $ret = $crypto->decryptMsg($_GET['msg_signature'], $timestamp, $nonce, '4');
    echo $ret;
    return json_decode($ret, true);
}

function get_dingtalk_post() {
    global $bot_run_as;
    $conf = $bot_run_as['config'] = read_file_to_array('config/bot.json');
    $ymd = date('Y-m-d');
    $interview = false;
    $headers = [];
    foreach ($_SERVER as $key => $value) {
        if (substr($key, 0, 5) == 'HTTP_') {
            $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($key, 5)))))] = $value;
        }
    }   
    $body = file_get_contents('php://input');
    
    $body = json_decode($body, true);
    write_to_file_json("lastRequest.json",['G'=>$_GET, 'H'=>$headers,'B'=>$body, "request_id"=>$bot_run_as["RUN_ID"]]);
    if (isset($_GET['cronRun'])) {
        DingraiaPHPCron();
    }
    if (isset($_GET['cronStop'])) {
        DingraiaPHPCronStop();
    }
    if (isset($_GET['cronRestart'])) {
        DingraiaPHPCronRestart();
    }
    /*附加模块注册*/
    $requireMoudle = "lxyddice";
    require_once("module/DingraiaPHP/app/autoload.php");
    
    if ($x) {
        return $x;
    }
    /*接入登录阶段A*/
    if (isset($_GET['client_id']) && isset($_GET['state']) && isset($_GET['redirect_uri'])) {
        $state = $_GET['state'];
        $client_id = $_GET['client_id'];
        $c = read_file_to_array("config/cropid.json");
            if (!isset($c['APP'][$client_id])) {
                DingraiaPHPResponseExit(400, "clientId not exists");
            }
        $redirect_uri = urldecode($_GET['redirect_uri']);
        $f = read_file_to_array("data/bot/oauth2.json");
        $key = uuid();
        $f[$key] = ["state"=>$state, "redirect_uri"=>$redirect_uri, "client_id"=>$client_id, "use"=>0];
        $redirect_uri = $conf['host_url'];
        write_to_file_json("data/bot/oauth2.json", $f);
        $url = "https://login.dingtalk.com/oauth2/auth?redirect_uri={$redirect_uri}&response_type=code&client_id={$client_id}&scope=openid&state={$key}&prompt=consent";
        header("Location: $url");
        exit();
    }
    /*接入登录阶段B*/
    if (isset($_GET['authCode']) && isset($_GET['state'])) {
        $f = read_file_to_array("data/bot/oauth2.json");
        $authCode = $_GET['authCode'];
        $state = $_GET['state'];
        if (isset($f[$state]['use']) && $f[$state]['use'] == 0) {
            $client_id = $f[$state]['client_id'];
            $c = read_file_to_array("config/cropid.json");
            if (isset($c['APP'][$client_id])) {
                $appsec = $c['APP'][$client_id]["AppSecret"];
                $headers = ["Content-Type" => "application/json"];
                $data = ["clientSecret" => $appsec,"clientId" => $client_id,"code" => $authCode,"grantType" => 'authorization_code'];
                $res = json_decode(requests("POST", "https://api.dingtalk.com/v1.0/oauth2/userAccessToken", $data, $headers, 20)['body'], true);
                if (isset($res['accessToken'])) {
                    $token = $res['accessToken'];
                    $headers = ["x-acs-dingtalk-access-token" => $token,"Content-Type" => "application/json"];
                    $res = json_decode(requests("GET", "https://api.dingtalk.com/v1.0/contact/users/me", [], $headers, 20)["body"], true);
                    if (isset($res['nick'])) {
                        $ck = DingraiaPHP_serviceBanMain(["userId"=>$res["unionId"]], "dingtalkOauth2");
                        if ($ck["code"] == 0) {
                            $l = read_file_to_array("data/bot/oauth2Login.json");
                            $l[$state] = $res;
                            $l[$state]["authCode"] = $token;
                            $f[$state]['use'] = 1;
                            write_to_file_json("data/bot/oauth2.json",$f);
                            write_to_file_json("data/bot/oauth2Login.json",$l);
                            $url = $f[$state]['redirect_uri'];
                            $parsedUrl = parse_url($url);
                            if (isset($parsedUrl['query'])) {
                                $url .= "&DingraiaPHPState={$state}&state=" . urlencode($f[$state]['state']);
                            } else {
                                $url .= "?DingraiaPHPState={$state}&state=" . urlencode($f[$state]['state']);
                            }
                            header("Location: $url");
                        } else {
                            DingraiaPHPResponseExit($ck["code"], $ck['msg']);
                        }
                    }
                } else {
                    DingraiaPHPResponseExit(500, "Description Failed to obtain user information");
                    tool_log(3, ["state"=>$state,"message"=>$res]);
                }
            } else {
                DingraiaPHPResponseExit(403, "ClientId does not exist");
            }
        } else {
            DingraiaPHPResponseExit(403, "The callback parameter does not exist");
        }
    }
    /*dingraia回调验证*/
    if (isset($body['dingraia'])) {
        if ($body['dingraia'] == 'master' || $headers['dingraia'] == 'master') {
            if ($headers['dingraiaMasterMode'] == 'callback') {
                if ($_SERVER ['REQUEST_METHOD'] =='POST') {
                    require_once('module/DingraiaPHP/functions/getcallback.php');
                    $encrypt = $body['encrypt'];
                    $r = dingraiaDingtalkCallback($encrypt);
                    $c[] = ["chat_mode"=>"mcb", 'callbackContent' => $r];
                    return $c;
                } else {
                    DingraiaPHPResponseExit(405, "Method Not Allowed.Need post request", "Method Not Allowed");
                }
            }
            $r = get_dingraia_master($headers,$body);
            $c[] = $body;
            return $c;
        }
        return false;
    }
    /*正常事件回调*/
    if (isset($_GET['signature']) && isset($_GET['nonce']) && isset($_GET["msg_signature"]) && isset($body["encrypt"])) {
        if ($_SERVER ['REQUEST_METHOD'] =='POST') {
            require_once('module/DingraiaPHP/functions/getcallback.php');
            $encrypt = $body['encrypt'];
            $r = dingraiaDingtalkCallback($encrypt);
            $f = read_file_to_array("data/callback.json");
            $f[$bot_run_as["RUN_ID"]] = $r;
            if ($r) {
                write_to_file_json("data/callback.json",$f);
                write_to_file_json("data/bot/cache/callback,{$ymd},{$bot_run_as['RUN_ID']}.json",[$bot_run_as["RUN_ID"],$r]);
            }
            $c[] = ["chat_mode"=>"cb", 'callbackContent' => $r];
            return $c;
        } else {
            DingraiaPHPResponseExit(405, "Method Not Allowed.Need post request", "Method Not Allowed");
        }
    }
    /*卡片回调*/
    if (isset($body['outTrackId'])) {
        $r = get_dingtalk_card_callback($headers,$body);
        return $r;
    }
    
    if (isset($body['text']) || isset($body['content'])) {
        $cropidkey = read_file_to_array("config/cropid.json");
        $chatbotCorpId = $body["chatbotCorpId"];
        if (isset($cropidkey[$chatbotCorpId])) {
            $ts = isset($headers['Timestamp']) ?? null;
            $r = get_dingtalk_post_check_sign($ts, $cropidkey[$chatbotCorpId]["AppSecret"], isset($headers['Sign']) ?? null);
            if ($r && ($ts / 1000) - time() < -3600) {
                $bot_run_as["chat_mode"] = "dingtalk-message";
            } else {
                DingraiaPHPResponseExit(403, "Signature verification failed");
            }
        } else {
            DingraiaPHPResponseExit(403, "Chatbot CorpId does not exist");
        }
        $body['text']['content'] = ltrim($body['text']['content']);
        $logData[] = [
            'headers' => $headers,
            'body' => [
                'isAdmin' => $body['isAdmin'],
                'atUsers' => normalizeArrayFormat(filterByStaffId($body['atUsers'])),
                'userid' => $body['senderId'],
                'webhook' => $body['sessionWebhook'],
                'message' => $body["text"]["content"],
                'robotid' => $body["robotCode"],
                'senderStaffId' => $body['senderStaffId'],
                'conversationTitle' => $body['conversationTitle'],
                'name' => $body['senderNick'],
                'conversationType' => $body['conversationType'],
                'conversationId' => $body['conversationId'],
                'chatbotCorpId' => $body['chatbotCorpId'],
                'robotCode' => $body['robotCode']
            ],
        ];
        if (isset($body['msgtype']) && $body['msgtype'] == 'picture') {
            $logData[count($logData) - 1]['body']['msgtype'] = 'picture';
            $logData[count($logData) - 1]['body']['downloadCode'] = $body['content']['pictureDownloadCode'];
        }
        if (isset($body['msgtype']) && $body['msgtype'] == 'richText') {
            $logData[count($logData) - 1]['body']['msgtype'] = 'richText';
            $logData[count($logData) - 1]['body']['content'] = $body['content'];
        }
        if (isset($body['msgtype']) && $body['msgtype'] == 'file') {
            $logData[count($logData) - 1]['body']['msgtype'] = 'file';
            $logData[count($logData) - 1]['body']['content'] = $body['content'];
        }
        DingraiaPHPLogChatGroup($logData);
        $bot_run_as["chat_mode"] = "message";
        return $logData;
    } else {
        return false;
    }
}

function customErrorHandler($errno, $errstr, $errfile, $errline) {
    $errorMessage = "Error: [$errno] $errstr in $errfile on line $errline";
    
    if ($errno == E_ERROR || $errno == E_PARSE || $errno == E_USER_ERROR) {
        send_message($errorMessage, $webhook, $staffid);
        echo $errorMessage . "<br>";
    }
}


function log_get_message($logData) {
    $jsonData = file_get_contents("data/get.json");
    $dataArray = json_decode($jsonData, true);
    $currentDateTime = date('Y-m-d H:i:s');
    $dataArray[] = array("time" => $currentDateTime, "data" => $logData);
    $jsonData = json_encode($dataArray, JSON_PRETTY_PRINT);
    file_put_contents("data/get.json", $jsonData);
}

function tool_log($level, $message) {
    global $bot_run_as;
    if (!is_numeric($level) or $level > 4 or $level < 0) {
        return false;
    }
    $levelarr = ["Debug","Info","Warn","Error","Fatal"];
    $levelName = $levelarr[$level];
    $logFilePath = "data/bot/error_log/log.json";
    $logFileDir = "data/bot/error_log/logs/";
    $logData = [
        "level" => $levelName,
        "time" => date('Y-m-d H:i:s'),
        "mircoTime" => microtime(true),
        "logId" => uuid(),
        "data" => $message,
        "runId" => $bot_run_as["RUN_ID"]
    ];
    $logId = uuid();
    write_to_file_json($logFileDir.$logId.".json", $logData);
    return $logId;
}


function getOGGDurationInMilliseconds($oggFilePath) {
    global $bot_run_as;
    $getID3 = new getID3();
    $fileInfo = $getID3->analyze($oggFilePath);
    $durationMilliseconds = $fileInfo['playtime_seconds'] * 1000;
    app_json_file_add_list($bot_run_as["RUN_LOG_FILE"], ["time"=>microtime(true),"type"=>"getOGGDurationInMilliseconds","oggFilePath"=>$oggFilePath,"durationMilliseconds"=>$durationMilliseconds]);
    return $durationMilliseconds;
}

function getMp4DurationInMilliseconds($oggFilePath) {
    global $bot_run_as;
    $getID3 = new getID3();
    $fileInfo = $getID3->analyze($oggFilePath);
    $durationMilliseconds = $fileInfo['playtime_seconds'] * 1000;
    app_json_file_add_list($bot_run_as["RUN_LOG_FILE"], ["time"=>microtime(true),"type"=>"getOGGDurationInMilliseconds","oggFilePath"=>$oggFilePath,"durationMilliseconds"=>$durationMilliseconds]);
    return $durationMilliseconds;
}

function upload_to_dingtalk_v2($type, $file, $token) {
    $midarr = read_file_to_array("data/bot/mediaId.json");
    if (isset($midarr[$file])) {
        $fileCache = $midarr[$file];
        if ($fileCache["exTime"] > time()) {
            return $fileCache["meidaId"];
        }
    }
    if (file_exists($file)) {
        $url = "https://oapi.dingtalk.com/media/upload?access_token=$token";
        if (!file_exists($file)) {
            return ['error' => 'File not found'];
        }
        $ch = curl_init();
        $fileData = new CURLFile($file);
        $postData = ['media' => $fileData, "type"=>$type];
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        $error = curl_error($ch);
        curl_close($ch);
        requestsDingtalkApi("POST", $url, $postData, [], 10, true);
        if ($error) {
            return false;
        }
        $responseData = json_decode($response, true)["media_id"];
        $midarr[$file] = ["exTime"=>time() + 604800, "meidaId"=>$responseData];
        write_to_file_json("data/bot/mediaId.json", $midarr);
        return $responseData;
    }
    return false;
}
/* 弃用
function upload_to_dingtalk($type,$file,$token) {
    require_once("module/dingtalk-sdk/TopSdk.php");
    date_default_timezone_set('Asia/Shanghai');
    $c = new DingTalkClient(DingTalkConstant::$CALL_TYPE_OAPI, DingTalkConstant::$METHOD_POST, DingTalkConstant::$FORMAT_JSON);
    $req = new OapiMediaUploadRequest;
    $req->setType($type);
    $midarr = read_file_to_array("data/meidaid.json");
    if (file_exists($file)) {
        if (isset($midarr[$file])) {
            return $midarr[$file];
        }
        $req->setMedia('@' . $file);
        $resp = $c->execute($req, $token, "https://oapi.dingtalk.com/media/upload");
        $mid = get_object_vars($resp)["media_id"];
        $midarr[$file] = $mid;
        if ($mid != null) {
            file_put_contents("data/meidaid.json", json_encode($midarr));
        }
        return $mid;
    } else {
        return false;
    }
}
*/
function normalizeArrayFormat($arr) {
    if (is_array($arr)) {
        if (isset($arr['1']) && is_array($arr['1'])) {
            return array_values($arr);
        }
    }
    return $arr;
}

function check_group_permission($conversationId,$per): bool
{
    if (in_array($per,$grouparr["permission"][$conversationId])) {
        return true;
    } else {
        return false;
    }
}

function DingraiaPHPCheckUserBlackList($userid): bool
{
    $file = read_file_to_array("data/bot/user/blacklist/data.json");
    if (in_array($userid, $file['global'])) {
        return true;
    } else {
        return false;
    }
}

function DingraiaPHPCron(): bool
{
    global $bot_run_as;
    print_r("<br>request_id:".$bot_run_as["RUN_ID"]);
    $cronAuth = "lxyddice";
    if (file_exists("data/bot/cron/stop.lock") && file_exists("data/bot/cron/alive.lock")) {
        unlink("data/bot/cron/stop.lock");
        unlink("data/bot/cron/alive.lock");
        
        require_once("module/DingraiaPHP/app/cron/main.php");
    } else {
        require_once("module/DingraiaPHP/app/cron/main.php");
    }
    return true;
}

function DingraiaPHPCronStop($stop = true): bool
{
    if (file_exists("data/bot/cron/alive.lock")) {
        unlink("data/bot/cron/alive.lock");
    } else {
        DingraiaPHPResponseExit(400, "Cron is already stop");
    }
    file_put_contents("data/bot/cron/stop.lock","");
    
    DingraiaPHPResponseExit(0, "cron stop success", "OK", $stop);
    return true;
}

function DingraiaPHPCronRestart() {
    $tag = -1;
    if (file_exists("data/bot/cron/alive.lock")) {
        DingraiaPHPCronStop(false);
        $tag = 1;
    }
    sleep(1);
    file_put_contents("data/bot/cron/restart.lock","");
    DingraiaPHPCron();
}

function DingraiaPHPResponseExit($errCode, $message = "Unkown Error", $m = null,$stop = true, $json = false, $jsonCus = null) {
    if ($m == null)  {
        if ($errCode == 403) {
            $m = "Forbidden";
        } elseif ($errCode == 500) {
            $m = "Internal Srver Error";
        } elseif ($errCode == 400) {
            $m = "Bad Request";
        } elseif ($errCode == 405) {
            $m = "Method Not Allowed";
        } elseif ($errCode == 401) {
            $m = "Unauthorized";
        }
    }
    http_response_code($errCode);
    $_GET["format"] = isset($_GET["format"])? $_GET["format"] : "html";
    if ($_GET['format'] == "json" || $json) {
        header('Content-Type:application/json; charset=utf-8');
        $exitMes = json_encode(["success"=>false, "code"=>$errCode,"message"=>$message],JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE);
        if ($stop) {
            exit($exitMes);
        } else {
            echo $exitMes;
        }
    } else {
        if ($stop) {
            exit("<h1><html lang=en><title>DingraiaPHP-{$errCode} {$m}</title><h1>{$errCode} {$m}</h1><p>{$message}</p><small>DingraiaPHP</small></h1>");
        } else {
            echo("<h1><html lang=en><title>DingraiaPHP-{$errCode} {$m}</title><h1>{$errCode} {$m}</h1><p>{$message}</p><small>DingraiaPHP</small></h1>");
        }
    }
}

function lxy_requireRemoteCode($url) {
    $remoteContent = file_get_contents($url);
    $remoteContent = base64_decode(json_decode($remoteContent, true)["code"]);
    $tempFile = tempnam(sys_get_temp_dir(), 'remote_code_');
    file_put_contents($tempFile, $remoteContent);
    require_once $tempFile;
    unlink($tempFile);
}

function DingraiaPHPAddEndModulePlugin($file, $fn_name) {
    if (!file_exists("data/bot/app/endTasks.json")) {
        write_to_file_json("data/bot/app/endTasks.json", []);
    } else {
        $t = read_file_to_array("data/bot/app/endTasks.json");
        $t[] = ["file"=>$file, "fn_name"=>$fn_name];
        write_to_file_json("data/bot/app/endTasks.json", $t);
    }
}

function DingraiaPHP_pdoRunQuery($pdo, $sql, $params = array()) {
    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        return false;
    }
}

function DingraiaPHP_createTempDingtalkLogin($token,$uid) {
    
    $res = uid2userinfo($uid);
    $userid = $res["staffid"];
    if ($userid) {
        $res = userinfo($userid,$token);
        $uuid = uuid();
        
        $f = read_file_to_array("data/bot/tempDingtalkLogin.json");
        $f[$uuid] = $res;
        write_to_file_json("data/bot/tempDingtalkLogin.json", $f);
        
        return $uuid;
    }
    return false;
    
}

function DingraiaPHPSysMsg($msg = '未知的异常',$die = true) {
    ?>  
    <!DOCTYPE html>
    <html xmlns="http://www.w3.org/1999/xhtml" lang="zh-CN">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>站点提示信息</title>
        <style type="text/css">
html{background:#eee}body{background:#fff;color:#333;font-family:"微软雅黑","Microsoft YaHei",sans-serif;margin:2em auto;padding:1em 2em;max-width:700px;-webkit-box-shadow:10px 10px 10px rgba(0,0,0,.13);box-shadow:10px 10px 10px rgba(0,0,0,.13);opacity:.8}h1{border-bottom:1px solid #dadada;clear:both;color:#666;font:24px "微软雅黑","Microsoft YaHei",,sans-serif;margin:30px 0 0 0;padding:0;padding-bottom:7px}#error-page{margin-top:50px}h3{text-align:center}#error-page p{font-size:9px;line-height:1.5;margin:25px 0 20px}#error-page code{font-family:Consolas,Monaco,monospace}ul li{margin-bottom:10px;font-size:9px}a{color:#21759B;text-decoration:none;margin-top:-10px}a:hover{color:#D54E21}.button{background:#f7f7f7;border:1px solid #ccc;color:#555;display:inline-block;text-decoration:none;font-size:9px;line-height:26px;height:28px;margin:0;padding:0 10px 1px;cursor:pointer;-webkit-border-radius:3px;-webkit-appearance:none;border-radius:3px;white-space:nowrap;-webkit-box-sizing:border-box;-moz-box-sizing:border-box;box-sizing:border-box;-webkit-box-shadow:inset 0 1px 0 #fff,0 1px 0 rgba(0,0,0,.08);box-shadow:inset 0 1px 0 #fff,0 1px 0 rgba(0,0,0,.08);vertical-align:top}.button.button-large{height:29px;line-height:28px;padding:0 12px}.button:focus,.button:hover{background:#fafafa;border-color:#999;color:#222}.button:focus{-webkit-box-shadow:1px 1px 1px rgba(0,0,0,.2);box-shadow:1px 1px 1px rgba(0,0,0,.2)}.button:active{background:#eee;border-color:#999;color:#333;-webkit-box-shadow:inset 0 2px 5px -3px rgba(0,0,0,.5);box-shadow:inset 0 2px 5px -3px rgba(0,0,0,.5)}table{table-layout:auto;border:1px solid #333;empty-cells:show;border-collapse:collapse}th{padding:4px;border:1px solid #333;overflow:hidden;color:#333;background:#eee}td{padding:4px;border:1px solid #333;overflow:hidden;color:#333}
        </style>
    </head>
    <body id="error-page">
        <?php echo '<h3>站点提示信息</h3>';
        echo $msg; ?>
    </body>
    </html>
    <?php
    if ($die) {
        exit;
    }
}

?>