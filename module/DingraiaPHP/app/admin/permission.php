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
    <link href="https://cdn.bootcdn.net/ajax/libs/layui/2.9.13/css/layui.min.css" rel="stylesheet">
    <script src="https://cdn.bootcdn.net/ajax/libs/layui/2.9.13/layui.js"></script>
    <script src="https://cdn.bootcdn.net/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <title>权限管理</title>
    <style>
        body {
            padding: 20px;
        }
        
        h1 {
            text-align: center;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div style="margin: 20px;">
        <button id="addUser" class="layui-btn">添加用户</button>
        <button id="addGroup" class="layui-btn">添加组</button>
    </div>
    <div style="margin: 20px;">
        <table class="layui-table" id="userTable"></table>
        <table class="layui-table" id="groupTable"></table>
    </div>

    <!-- 添加用户表单 -->
    <div id="userFormContainer" style="display:none;">
        <form class="layui-form" id="userForm">
            <div class="layui-form-item">
                <label class="layui-form-label">用户ID</label>
                <div class="layui-input-block">
                    <input type="text" name="userId" required lay-verify="required" placeholder="请输入用户ID" autocomplete="off" class="layui-input">
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">权限组</label>
                <div class="layui-input-block">
                    <input type="text" name="uid" required lay-verify="required" placeholder="请输入权限组" autocomplete="off" class="layui-input">
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">群权限</label>
                <div class="layui-input-block">
                    <input type="text" name="permission" required lay-verify="required" placeholder="请输入群权限" autocomplete="off" class="layui-input">
                </div>
            </div>
            <button class="layui-btn" lay-submit lay-filter="saveUser">保存</button>
        </form>
    </div>

    <!-- 添加组表单 -->
    <div id="groupFormContainer" style="display:none;">
        <form class="layui-form" id="groupForm">
            <div class="layui-form-item">
                <label class="layui-form-label">组名</label>
                <div class="layui-input-block">
                    <input type="text" name="groupName" required lay-verify="required" placeholder="请输入组名" autocomplete="off" class="layui-input">
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">权限</label>
                <div class="layui-input-block">
                    <input type="text" name="permissions" required lay-verify="required" placeholder="请输入权限" autocomplete="off" class="layui-input">
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">用户</label>
                <div class="layui-input-block">
                    <input type="text" name="uid" required lay-verify="required" placeholder="请输入用户" autocomplete="off" class="layui-input">
                </div>
            </div>
            <button class="layui-btn" lay-submit lay-filter="saveGroup">保存</button>
        </form>
    </div>

    <script>
        $(document).ready(function() {
            layui.use(['form', 'layer', 'table'], function() {
                var form = layui.form;
                var layer = layui.layer;
                var table = layui.table;

                // 添加用户按钮点击事件
                $('#addUser').on('click', function() {
                    layer.open({
                        type: 1,
                        title: '添加用户',
                        content: $('#userFormContainer'),
                        area: ['500px', '400px'],
                        success: function(layero, index) {
                            form.render();
                        }
                    });
                });

                // 添加组按钮点击事件
                $('#addGroup').on('click', function() {
                    layer.open({
                        type: 1,
                        title: '添加组',
                        content: $('#groupFormContainer'),
                        area: ['500px', '400px'],
                        success: function(layero, index) {
                            form.render();
                        }
                    });
                });

                // 保存用户
                form.on('submit(saveUser)', function(data) {
                    var formData = data.field;
                    $.ajax({
                        type: "POST",
                        url: "?action=api&type=add_user",
                        data: formData,
                        success: function(response) {
                            layer.msg(response.message);
                            if (response.success) {
                                layer.closeAll();
                                // 重新加载用户表格数据
                                loadUserData();
                            }
                        }
                    });
                    return false;
                });

                // 保存组
                form.on('submit(saveGroup)', function(data) {
                    var formData = data.field;
                    $.ajax({
                        type: "POST",
                        url: "?action=api&type=add_group",
                        data: formData,
                        success: function(response) {
                            layer.msg(response.message);
                            if (response.success) {
                                layer.closeAll();
                                // 重新加载组表格数据
                                loadGroupData();
                            }
                        }
                    });
                    return false;
                });

                function loadUserData() {
                    $.ajax({
                        url: '?action=api&type=get_users',
                        type: 'GET',
                        success: function(data) {
                            var result = data.result;
                            var userData = [];
                            $.each(result, function(key, value) {
                                userData.push({
                                    userId: key,
                                    permissions: value.join(", ")
                                });
                            });
                            table.render({
                                elem: '#userTable',
                                cols: [[
                                    {field: 'userId', title: '用户ID'},
                                    {field: 'permissions', title: '权限'}
                                ]],
                                data: userData
                            });
                        }
                    });
                }

                function loadGroupData() {
                    $.ajax({
                        url: '?action=api&type=get_groups',
                        type: 'GET',
                        success: function(data) {
                            var result = data.result;
                            var groupData = [];
                            $.each(result, function(key, value) {
                                groupData.push({
                                    groupName: key,
                                    permissions: value.permission.join(", "),
                                    users: value.user ? value.user.join(", ") : ""
                                });
                            });
                            table.render({
                                elem: '#groupTable',
                                cols: [[
                                    {field: 'groupName', title: '组名'},
                                    {field: 'permissions', title: '权限'},
                                    {field: 'users', title: '用户'}
                                ]],
                                data: groupData
                            });
                        }
                    });
                }

                // 页面加载时初始化数据
                loadUserData();
                loadGroupData();
            });
        });
    </script>
</body>
</html>