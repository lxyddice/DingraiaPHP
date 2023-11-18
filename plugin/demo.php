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
