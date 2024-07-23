<?php
if (file_exists("fn.php")) {
    require_once("fn.php");
} elseif (file_exists("module/DingraiaPHP/app/admin/fn.php")) {
    require_once("module/DingraiaPHP/app/admin/fn.php");
} else {
    exit("Can't find toolkit,are you install DingraiaPHP?");
}
/* 下面的css地址自己部署会快些吗？ */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat Website</title>
    <link href="https://static.lxyddice.top/lib/css/tabler.min.css" rel="stylesheet">
    <script src="https://static.lxyddice.top/lib/js/jquery.min.js"></script>
        <style>
        body {
            background-color: #f8f9fa; /* 设置页面背景色 */
        }

        .container {
            margin-top: 50px; /* 调整容器上边距 */
        }

        #user-list-container {
            background-color: #ffffff; /* 设置用户列表背景色 */
            padding: 15px; /* 设置用户列表内边距 */
            border-radius: 8px; /* 设置圆角 */
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1); /* 添加阴影效果 */
            font-size: 16px; /* 设置字体大小为16像素 */
        }


        #chat-container {
            background-color: #ffffff; /* 设置聊天容器背景色 */
            padding: 15px; /* 设置聊天容器内边距 */
            border-radius: 8px; /* 设置圆角 */
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1); /* 添加阴影效果 */
            max-height: 700px;
        }

        #messages {
            max-height: 700px; /* 设置消息列表最大高度，添加滚动条 */
            overflow-y: auto; /* 允许消息内容溢出时显示滚动条 */
        }
        
        .message {
            margin-bottom: 15px;
        }

        .message img {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            margin-right: 10px;
        }

        .message .message-content {
            display: inline-block;
            padding: 10px;
            border-radius: 5px;
            background-color: #e0e0e0;
        }

        #messageInput {
            width: 70%; /* 设置输入框宽度 */
            margin-right: 10px; /* 调整输入框右边距 */
        }

        .btn-primary {
            background-color: #007bff; /* 设置主按钮背景色 */
            border-color: #007bff; /* 设置主按钮边框颜色 */
        }

        .btn-primary:hover {
            background-color: #0056b3; /* 设置主按钮鼠标悬停时背景色 */
            border-color: #0056b3; /* 设置主按钮鼠标悬停时边框颜色 */
        }
        .loading-spinner {
            border: 8px solid #f3f3f3;
            border-top: 8px solid #3498db;
            border-radius: 50%;
            width: 50px;
            height: 50px;
            animation: spin 1s linear infinite;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</head>
<body class="antialiased">

<div class="container">
    <div class="row">
        <div class="col-md-3">
            <!-- 用户列表容器 -->
            <div id="user-list-container" class="card">
                <!-- 这里放用户列表的内容 -->
            </div>
        </div>
        <div class="col-md-9">
            <!-- 聊天容器 -->
            <div id="chat-container" class="card">
                <ul id="messages" class="list-group list-group-flush"></ul>
                    <div class="loading-spinner" id="loading-spinner"></div>
                        <div class="card-body" id="card-body">
                            <input type="text" id="messageInput" class="form-control" placeholder="Type your message...">
                            <input type="checkbox" id="groupModeCheckbox"> <!-- 新增的复选框 -->
                            <label for="groupModeCheckbox">Group Mode</label> <!-- 复选框的标签 -->
                            <button id="sendButton" onclick="sendMessage()" class="btn btn-primary mt-2">Send</button>
                        </div>
            </div>
        </div>
    </div>
</div>
<script src="https://static.lxyddice.top/lib/js/popper.min.js"></script>
<script src="https://static.lxyddice.top/lib/js/tabler.min.js"></script>
<script>
var sendWebhook = "";
var sendRobotCode = "";
var sendGroupId = "";
 function getGroupInfo() {
    $("#card-body").hide();
    $("#loading-spinner").hide();

    $.ajax({
        url: "?action=api&type=getGroupMessage",
        method: "GET",
        dataType: "json",
        success: function (data) {
            if (data.success) {
                var userListContainer = $("#user-list-container");

                for (var groupId in data.result) {
                    var chatName = data.result[groupId].chatName;
                    var decodedChatName = decodeURIComponent(escape(atob(chatName)));
                    if (data.result[groupId].chatType == "1") {
                        decodedChatName = "[私聊]  " + decodedChatName;
                    } else {
                        decodedChatName = "[群聊]  " + decodedChatName;
                    }
                    var groupButton = $("<button></button>")
                        .text(decodedChatName)
                        .on("click", function (groupId) {
                            return function () {
                                getGroupMessages(groupId);
                            };
                        }(groupId));
                    userListContainer.append(groupButton);
                }
            }
        },
        error: function (error) {
            console.error("Error fetching group information:", error);
        }
    });
}

    function getGroupMessages(groupId) {
        var encodedGroupId = btoa(groupId);
        $("#loading-spinner").show();
        $("#card-body").hide();
        $("#messages").empty();
        $.ajax({
            url: "?action=api&type=getGroupMessage&cid=" + encodedGroupId,
            method: "GET",
            dataType: "json",
            success: function (data) {
                if (data.success) {
                    $("#loading-spinner").hide();
                    $("#card-body").show();
                    var messages = data.result.message;
                    sendWebhook = data.result.webhook;
                    staffId = data.result.staffId;
                    sendRobotCode = data.result.robotCode;
                    chatType = data.result.chatType;
                    sendGroupId = groupId;
                    messages.forEach(function (message) {
                        displayMessage(message.name, message.message);
                    });
                }
            },
            error: function (error) {
                console.error("Error fetching group messages:", error);
            }
        });
    }
    
    function removeControlCharacters(inputString) {
        return inputString.replace(/[\u200B-\u200D\uFEFF]/g, '');
    }

    function displayMessage(name, message) {
        var decodedMessage = decodeURIComponent(escape(atob(message)));
        var decodedName = removeControlCharacters(decodeURIComponent(escape(atob(name))));
    
        var messagesContainer = $("#messages");
    
        var messageElement = $("<li class='message'></li>")
            .appendTo(messagesContainer);
    
        var messageContentElement = $("<div class='message-content'></div>")
            .append($("<div class='message-name'></div>").text(decodedName))
            .append($("<div class='message-text'></div>").text(decodedMessage))
            .appendTo(messageElement);
    
        messagesContainer.scrollTop(messagesContainer.prop("scrollHeight"));
    }

    $(document).ready(function () {
        getGroupInfo();
    });
        
    function sendMessage() {
        var messageInput = $("#messageInput").val();
        if (!messageInput.trim()) {
            alert("Please enter a message");
            return;
        }
         var groupModeCheckbox = $("#groupModeCheckbox");
        var groupMode = groupModeCheckbox.prop("checked");
    
        var myButton = $("#sendButton");
        myButton.prop("disabled", true);
        myButton.text("正在发送，请稍后...");
    
        var apiUrl = "?action=api&type=sendGroupMessage";
        $.ajax({
            url: apiUrl,
            method: "POST",
            data: {
                webhook: sendWebhook,
                content: messageInput,
                groupMode: groupMode,
                groupId: sendGroupId,
                robotCode: sendRobotCode,
                staffId: staffId,
                chatType: chatType
            },
            success: function (data) {
                console.log("Message sent successfully:", data);
                if (data.code == 0) {
                    appendMessageToChat(messageInput);
                    $("#messageInput").val("");
                } else {
                    alert(data.message + "：\n" + (data.result.errmsg ? data.result.errmsg : data.result.message));
                }
            },
            error: function (error) {
                console.error("Error sending message:", error);
            },
            complete: function () {
                myButton.prop("disabled", false);
                myButton.text("发送");
            }
        });
    }
    function appendMessageToChat(messageContent) {
        var messagesContainer = $("#messages");
        var messageElement = $("<li class='message'></li>");
        messageElement.text("你：" + messageContent);
        messagesContainer.append(messageElement);
        messagesContainer.scrollTop(messagesContainer.prop("scrollHeight"));
    }

</script>

</body>
</html>