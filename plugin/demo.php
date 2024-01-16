<?php
if ($globalmessage == "/hi"){
    send_message('hi!', $webhook, $staffid);
}

if ($globalmessage == "/t1"){
    $result = stringf($globalmessage);
    send_message($result, $webhook);
}

if (strpos($globalmessage, "/test1") === 0) {
    $result = stringf($globalmessage);
    send_message($result, $webhook, $staffid);
}

if (strpos($globalmessage, "/test2") === 0) {
    $res = requests("POST", "https://api.lxyddice.top/api/gk", $data);
    send_message($res['body'], $webhook);
}

if ($globalmessage == "/test:atall") {
    send_message('qwq', $webhook, 1);
}

if ($globalmessage == "/lktest") {
    send_link("原神，启动！", $webhook, 0, "测试", "https://img.onlinedown.net/download/202008/161910-5f48bdfe3930b.jpg", "https://ys.mihoyo.com");
}

if ($globalmessage == "/acatest") {
    $Atitle = ["明日方舟","怎么","你了"];
    $actionURL = ["https://ys.mihoyo.com", "https://sr.mihoyo.com", "https://mihoyo.com"];
    send_actionCardB("![1](https://th.bing.com/th/id/OIP.YLp3-S0sVSOMX-o6PLE9bgHaEK?pid=ImgDet&rs=1) \n\n #### 这是一条测试 \n\n 这是一条测试", $webhook, 0, "测试", true);
}

if ($globalmessage == "/acatest2") {
    send_actionCardA('![1](https://i0.hdslb.com/bfs/article/dd6e714815db2cbd855160393c2d3212a9c578bc.png)
    
    ### 快来玩明日方舟
    
    来试试明日方舟吧！', $webhook, 0, "测试标题", "查看更多", "https://ak.hypergryph.com/");
}

if ($globalmessage == "/feedcardtest") {
    $Atitle = ["原神","怎么","你了"];
    $actionURL = ["https://ys.mihoyo.com", "https://sr.mihoyo.com", "https://mihoyo.com"];
    $ApicUrl = ["https://upload-bbs.miyoushe.com/upload/2021/02/23/190961740/f1cf2102f0b80d656683049eec90d421_500237588484630240.jpg", "https://ts1.cn.mm.bing.net/th/id/R-C.3f6257b108fabbdf8f9b33ab85852f1f?rik=TcwJqLka5niUPw&riu=http%3a%2f%2fimg001.dailiantong.com%2fNews%2f20210104%2fzty_20210104103418258.jpg&ehk=o%2f2ZnLPB1KG9XkmRlP1MzYGqxDwQ8h%2fSp4YMtaxCTos%3d&risl=&pid=ImgRaw&r=0", "https://ts1.cn.mm.bing.net/th/id/R-C.c95c6e93fc16d5cbe66f496b1773492d?rik=MiRhheUiuSXgXA&riu=http%3a%2f%2fi0.hdslb.com%2fbfs%2farticle%2f51ca10b8df006709b5c91ad0c712050540cdf8e8.jpg&ehk=MABlilSyOIe23cyung1gApN6pa6QgG5RcmVz33Rx7rs%3d&risl=&pid=ImgRaw&r=0"];
    send_feedcard("![1](https://th.bing.com/th/id/OIP.YLp3-S0sVSOMX-o6PLE9bgHaEK?pid=ImgDet&rs=1) \n\n #### 这是一条测试 \n\n 这是一条测试", $webhook, 0, "测试", true);
}

if (strpos($globalmessage, "我喜欢你") === 0) {
    $userarr = userid2uid($userid);
    $uid = $userarr['uid'];
    if ($uid == 10203 or $uid == 10001){
        send_message("我也喜欢你，每天都要开心哦！",$webhook,$staffid);
    }
}

if (strpos($globalmessage, "/rand") === 0) {
    $mes = stringf($globalmessage);
    if ($mes['len'] == 0) {
        send_message(rand(1,6),$webhook,$staffid);
    } elseif ($mes['len'] == 2) {
        if (is_numeric($mes['params'][1]) and is_numeric($mes['params'][2])) {
            send_message(rand($mes['params'][1],$mes['params'][2]),$webhook,$staffid);
        } else {
            send_message("非法数据",$webhook,$staffid);
        }
    } elseif ($mes['len'] == 3) {
        if (is_numeric($mes['params'][1]) and is_numeric($mes['params'][2]) and is_numeric($mes['params'][3])) {
            if ($mes['params'][3] > 10 or $mes['params'][3] < 1) {
                send_message("数量只允许1-10",$webhook,$staffid);
                exit();
            }
            for ($i = 0; $i < $mes['params'][3]-1; $i++) {
                $text .= rand($mes['params'][1],$mes['params'][2])."--";
            }
            $text .= rand($mes['params'][1],$mes['params'][2]);
            send_message($text,$webhook,$staffid);
        } else {
            send_message("非法数据",$webhook,$staffid);
        }
    } else {
        send_message("用法错误，/rand [最小] [最大] [次数]  （最小和最大需同时填，不写参数则1-6）",$webhook,$staffid);
    }
}

if ($globalmessage == "/涩图") {
    if (!in_array("涩图",$grouparr["permission"][$conversationId])) {//群权限测试
        send_message("该群无权使用该指令，请使用/join_lxe 加入测试群组",$webhook,$staffid);
        exit();
    }
    $res = requests("GET","？")['body'];
    send_markdown("![$res}](".$res.")",$webhook,"涩图：$res",0);
}

if ($globalmessage == "/join_lxe") {
    if (permission_check("group.whitelist.lxe", $guserarr["uid"])) {//用户权限
        $cropidkey = read_file_to_array("config/cropid.json")[$chatbotCorpId];
        $token = get_accessToken($cropidkey['AppKey'],$cropidkey['AppSecret']);
        $res = group_add_member($token, '？', [$staffid]);
        send_message($res, $webhook, $staffid);
    } else {
        send_message('你没有group.whitelist.lxe权限', $webhook, $staffid);
    }
}