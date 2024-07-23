<?php
if ($requireMoudle == 'lxyddice') {
    require_once("module/DingraiaPHP/main.php");
    require_once("module/DingraiaPHP/app/log.php");
    DingraiaPHPLogDisposeMainFn($bot_run_as);
}