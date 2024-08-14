<?php
if (file_exists("fn.php")) {
    require_once("fn.php");
} elseif (file_exists("module/DingraiaPHP/app/admin/fn.php")) {
    require_once("module/DingraiaPHP/app/admin/fn.php");
} else {
    exit("Can't find toolkit,are you install DingraiaPHP?");
}
?>
header("Location: https://api.lxyddice.top/dingbot/php/?action=p&page=apiDoc");