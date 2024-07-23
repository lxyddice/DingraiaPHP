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
	<title>登录到后台-DingraiaPHP</title>
	<style>
		* {
			margin: 0;
			padding: 0;
		}

		a {
			text-decoration: none;
		}

		input,
		button {
			background: transparent;
			border: 0;
			outline: none;
		}

		body {
			height: 100vh;
			background-size: 100% 100%;
			background-image:url("https://pic.lxyddice.top/api/lxy/api-gancheng.php");
			display: flex;
			justify-content: center;
			align-items: center;
			font-size: 16px;
			color: #03e9f4;
		}

		.loginBox {
		    opacity: 0.75;
			width: 400px;
			height: 455px;
			background-color: #0c1622;
			margin: 100px auto;
			border-radius: 10px;
			box-shadow: 0 15px 25px 0 rgba(0, 0, 0, .6);
			padding: 40px;
			box-sizing: border-box;
		}

		h2 {
			text-align: center;
			color: aliceblue;
			margin-bottom: 30px;
			font-family: 'Courier New', Courier, monospace;
		}

		.item {
			height: 45px;
			border-bottom: 1px solid #fff;
			margin-bottom: 40px;
			position: relative;
		}

		.item input {
			width: 100%;
			height: 100%;
			color: #fff;
			padding-top: 20px;
			box-sizing: border-box;
		}

		.item input:focus+label,
		.item input:valid+label {
			top: 0px;
			font-size: 2px;
		}

		.item label {
			position: absolute;
			left: 0;
			top: 12px;
			transition: all 0.5s linear;
		}

		.btn {
			padding: 10px 20px;
			margin-top: 30px;
			color: #03e9f4;
			position: relative;
			overflow: hidden;
			text-transform: uppercase;
			letter-spacing: 5px;
			left: 20%;
		}

		.btn:hover {
			border-radius: 5px;
			color: #fff;
			background: #03e9f4;
			box-shadow: 0 0 5px 0 #03e9f4,
				0 0 25px 0 #03e9f4,
				0 0 50px 0 #03e9f4,
				0 0 100px 0 #03e9f4;
			transition: all 1s linear;
		}

		.btn>span {
			position: absolute;
		}

		.btn>span:nth-child(1) {
			width: 100%;
			height: 2px;
			background: -webkit-linear-gradient(left, transparent, #03e9f4);
			left: -100%;
			top: 0px;
			animation: line1 1s linear infinite;
		}

		@keyframes line1 {

			50%,
			100% {
				left: 100%;
			}
		}

		.btn>span:nth-child(2) {
			width: 2px;
			height: 100%;
			background: -webkit-linear-gradient(top, transparent, #03e9f4);
			right: 0px;
			top: -100%;
			animation: line2 1s 0.25s linear infinite;
		}

		@keyframes line2 {

			50%,
			100% {
				top: 100%;
			}
		}

		.btn>span:nth-child(3) {
			width: 100%;
			height: 2px;
			background: -webkit-linear-gradient(left, #03e9f4, transparent);
			left: 100%;
			bottom: 0px;
			animation: line3 1s 0.75s linear infinite;
		}

		@keyframes line3 {

			50%,
			100% {
				left: -100%;
			}
		}

		.btn>span:nth-child(4) {
			width: 2px;
			height: 100%;
			background: -webkit-linear-gradient(top, transparent, #03e9f4);
			left: 0px;
			top: 100%;
			animation: line4 1s 1s linear infinite;
		}

		@keyframes line4 {

			50%,
			100% {
				top: -100%;
			}
		}
	</style>
</head>
<script src="https://cdn.bootcdn.net/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<body>
    <div class="loginBox">
        <h2>欢迎喵~</h2>
        <form>
            <div class="item">
                <input type="text" id="username" required>
                <label for="username">用户名</label>
            </div>
            <div class="item">
                <input type="password" id="password" required>
                <label for="password">密码</label>
            </div>
            <div class="item">
                <input type="text" id="2fa" required>
                <label for="2fa">验证码</label>
            </div>
            <button class="btn" id="loginBtn">登录
                <span></span>
                <span></span>
                <span></span>
                <span></span>
            </button>
            <button class="btn" id="oauth2">钉钉登录
                <span></span>
                <span></span>
                <span></span>
                <span></span>
            </button>
        </form>
    </div>

    <script>
        $(document).ready(function() {
            $("#loginBtn").click(function(event) {
                event.preventDefault();
                var username = $("#username").val();
                var password = $("#password").val();
                var x2fa = $("#2fa").val();
                if (!username || !password) {
                    alert("用户名和密码不能为空");
                    return;
                }
                $.ajax({
                    type: "POST",
                    url: "?action=api&type=htmlAdminLogin",
                    data: { type: "htmlLogin", username: username, password: password, code: x2fa },
                    success: function(response) {
                        if (response.success === true) {
                            loginUuid = response.result.loginUuid;
                            alert("登录成功，登录签名请勿泄露：" + loginUuid);
                            window.location.href = "?action=admin&page=index&sign=" + loginUuid;
                        } else {
                            alert(response.message + "\n" + response.tips);
                        }
                    },
                    error: function() {
                        alert("请求失败，请重试。");
                    }
                });
            });
            $("#oauth2").click(function(event) {
                window.location.href = "?action=admin&login=oauth2";
            });
        });
    </script>
</body>

