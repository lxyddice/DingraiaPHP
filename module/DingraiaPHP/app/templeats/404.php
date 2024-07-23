<?php
if (file_exists("fn.php")) {
    require_once("fn.php");
} elseif (file_exists("module/DingraiaPHP/app/admin/fn.php")) {
    require_once("module/DingraiaPHP/app/admin/fn.php");
} else {
    exit("Can't find toolkit,are you install DingraiaPHP?");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 Page Not Found</title>
    <style>
        body {
            background-color: #f2f2f2;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100vh;
            margin: 0;
            font-family: Arial, sans-serif;
        }
        .content {
            text-align: center;
        }
        .image {
            cursor: pointer;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
        }
        .title {
            font-size: 24px;
            margin-top: 20px;
        }
        .subtitle {
            font-size: 18px;
            margin-top: 10px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="content">
        <a href="?action=admin">
            <img class="image" src="https://pic.lxyddice.top/i/2023/10/13/kn80av.webp" alt="404" width="900" height="700">
        </a>
        <h1 class="title">Page Not Found</h1>
        <p class="subtitle">The page you are looking for does not exist.</p>
    </div>
</body>
</html>
    </div>
</div>
</html>
