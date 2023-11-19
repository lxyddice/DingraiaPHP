<?php
function DingraiaPHPRunCron() {
    $currentSeconds = date('s');
    if ($currentSeconds % 20 == 0) {
        cronFn_sendA();
    }
}
?>