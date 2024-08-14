<?php
require("config.php");
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>lxy_dingbot安装完毕</title>
    <link href="https://api.lxyddice.top/v1/DingraiaPHP/asset/install.css" rel="stylesheet" />
    <script src="https://cdn.bootcdn.net/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
</head>
<body>
<div class="container">
    <h2>欢迎使用DingraiaPHP</h2>
    <h1>您已经完成安装DingraiaPHP</h1>
    <div id="install-status"></div>
    <?php
    require("config.php");
    
    if (file_exists("install.php")) {
        unlink("install.php");
    }
    if (file_exists("index.php")) {
        unlink("index.php");
        file_put_contents("index.html","<meta http-equiv='refresh' content='0;url=complete.php'>");
    }
    echo $ver;
    ?>
</div>
</body>
</html>
