<?php

function DingraiaPHP_serviceBanMain($checkArr, $type) {
    global $bot;
    
    if ($type == "dingtalkOauth2") {
        $back = DingraiaPHP_serviceBan_dingtalkOauth2($checkArr);
    }
    
    return $back;
}

function DingraiaPHP_serviceBan_dingtalkOauth2($arr) {
    global $bot;
    
    $f = read_file_to_array(__DIR__."/config/dingtalkOauth2Ban.json");
    if (in_array($arr["userId"], $f)) {
        $log_id = tool_log(1,["dingtalkOauth2Ban"=>$arr]);
        return ["code"=>403, "msg"=>"你已被禁止使用lxyの钉钉oauth2服务。如果你认为这是错误：<br>请联系管理员提供反馈并附带日志ID：{$log_id}"];
    }
    
    return ["code"=>0, "msg"=>"ok"];
}