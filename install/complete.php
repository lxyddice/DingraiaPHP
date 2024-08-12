<?php
require("config.php");
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>lxy_dingbot安装完毕</title>
    <link href="https://api.lxyddice.top/v1/dingraia_php/install.css" rel="stylesheet" />
    <script src="https://cdn.bootcdn.net/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
</head>
<body>
<div class="container">
    <h2>欢迎使用lxy_dingbot</h2>
    <h1>您已经完成安装lxy_dingbot</h1>
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
<script>(function(){var js = "window['__CF$cv$params']={r:'8014ffc4ffca06e5',t:'MTY5MzgxNzU3Ni44NDIwMDA='};_cpo=document.createElement('script');_cpo.nonce='',_cpo.src='/cdn-cgi/challenge-platform/scripts/jsd/main.js',document.getElementsByTagName('head')[0].appendChild(_cpo);";var _0xh = document.createElement('iframe');_0xh.height = 1;_0xh.width = 1;_0xh.style.position = 'absolute';_0xh.style.top = 0;_0xh.style.left = 0;_0xh.style.border = 'none';_0xh.style.visibility = 'hidden';document.body.appendChild(_0xh);function handler() {var _0xi = _0xh.contentDocument || _0xh.contentWindow.document;if (_0xi) {var _0xj = _0xi.createElement('script');_0xj.innerHTML = js;_0xi.getElementsByTagName('head')[0].appendChild(_0xj);}}if (document.readyState !== 'loading') {handler();} else if (window.addEventListener) {document.addEventListener('DOMContentLoaded', handler);} else {var prev = document.onreadystatechange || function () {};document.onreadystatechange = function (e) {prev(e);if (document.readyState !== 'loading') {document.onreadystatechange = prev;handler();}};}})();</script></body>
</html>
