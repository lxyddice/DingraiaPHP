<?php
ignore_user_abort(true);
if (!file_exists("data/bot/cron/alive.lock")) {
    DingraiaPHPLoadCronFiles();
    require_once("data/bot/cron/register.php");
    while (true) {
        if (file_exists("data/bot/cron/stop.lock")) {
            unlink("data/bot/cron/stop.lock");
            unlink("data/bot/cron/alive.lock");
            exit();
        }
        sleep(1);
        DingraiaPHPRunCron();
    }
} else {
    exit("Cron is running");
}
function DingraiaPHPLoadCronFiles() {
    file_put_contents("data/bot/cron/alive.lock","");
    $folderPath = "data/bot/cron/plugin/";
    foreach (glob($folderPath . '*.php') as $filename) {
        require_once($filename);
    }
}