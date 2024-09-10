<?php
if ($requireMoudle == 'lxyddice') {
    require_once("module/DingraiaPHP/app/log.php");
    require_once("module/DingraiaPHP/app/serviceBan.php");
    require_once("module/DingraiaPHP/app/thread.php");
    require_once("module/DingraiaPHP/app/cron/main.php");
    DingraiaPHPLogDisposeMainFn();
}