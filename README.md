此文档最后更新于24/09/09 `v240909.3-Alpha`

[破坏性更新](/doc/破坏性更新.md)

# 这是什么？

这个是一个关于运行钉钉机器人的代码，用来运行Webhook的钉钉机器人、钉钉小程序功能，框架中有部分小工具。
只实现了23.3%的功能，将来可能会完善。

新功能/建议/Bug 请提出 [Issue](https://github.com/lxyddice/DingraiaPHP/issues/new/choose)

# 特点

支持 文字, Markdown FeedCard, ActionCard和文件的发送，简单调用钉钉小程序API，可扩展易开发

`尝试支持PHP8中 v240909.3-Alpha`

## 功能一览

- 应答机制
    - HTTP 回调
- 使用方式
    - 网页服务端
- 群聊功能
    - 发送消息
        - 文字
        - Markdown
        - ActionCard
        - FeedCard
    - 发送文件
        - 普通文件
        - 图片
        - 音频
        - 视频
    - 撤回消息 (钉钉限制仅通过API发送的文件才可以撤回，且由于PHP短连接限制，时间短短的)
    - 发送互动卡片 (自行构造JSON数据)
        - 预设 Markdown 卡片
        - 发送AI卡片
    - 改变卡片内容 (自行构造JSON数据)
    - 创建群
    - 获取群消息
    - 获取用户信息
    - 删除用户 (组织)
    - 更新群信息
        - 同源功能
            - 更新群标题
            - 改变群主
            - 全体禁言/解禁
    - 踢出用户 (群)
    - 添加用户 (群)
    - 设置/取消管理员 (群)
    - 禁言用户/解除禁言用户 (群)
    - 上传文件到钉钉
    - 下载文件
    - 钉钉API调用统计
- 特别功能
    - accessToken缓存
    - 外置插件————智齿使用框架处理非钉钉的信息
    - templeats，像Python的Flask框架一样，自定义页面
    - 即时载入/卸载模块
    - 方便的各种小工具

# 注意

本项目适用于一般聊天机器人开发，不建议用于生产环境

作者自身已经挂着该框架，保证框架确实可用 ~~如果懒癌发作不更新的另当别论~~

**自带文档可能更新不及时！（懒）**

**本项目基于企业内部机器人开发，使用企业内部机器人以获得更好的体验**

**普通Webhook机器人已经于2023年9月1日停用，目前不再支持（请查阅 [这里](https://doc.lxyddice.top/dingraiaphp/dingraiaphp/fa-song-shi-yong-webhook-de-pu-tong-xiao-xi)）**

~~**强烈推荐Stream模式**，安全，快速，随处可用~~  PHP网页端用什麽Stream（

使用 **场景群** 获取最完整体验

# 演示

## 虚拟主机版 固定于v240814.1-Alpha

[首页](https://us1-cf.240702.xyz/bot/)

[API端点](https://us1-cf.240702.xyz/bot/?action=api&type=getIp)

[normalDisplay](https://us1-cf.240702.xyz/bot/?action=p&page=index)

## 服务器版 开发版，开发环境与生产环境结合

[首页](https://api.lxyddice.top/dingbot/php/)

[API：获取图片](https://api.lxyddice.top/dingbot/php/?action=api&type=lxyGetFile&fileType=jpg&file=upload/380da2d3950e3886ffb451833343987c)

[自定义页面：工具网页](https://api.lxyddice.top/dingbot/php/?action=p&page=tools)

# 需求

拥有企业内部开发权限

# 如何使用？

<code>
git clone https://github.com/lxyddice/DingraiaPHP.git
</code>

上传框架到你的HTTP服务器目录

查看 [GitBook文档](https://doc.lxyddice.top/dingraiaphp/dingraiaphp)

完成[配置](https://doc.lxyddice.top/dingraiaphp/dingraiaphp/pei-zhi-ji-qi-ren)

发送 `-v` 即可发送一个 版本 的卡片

[关于ffmpeg](https://github.com/lxyddice/DingraiaPHP/blob/main/doc/%E5%85%B3%E4%BA%8Effmpeg.md)

# 啊天...

越写越像答辩了，一个更完整的php ez开发框架

# 需求

需要公网可访问的服务器，还得拥有企业内部开发权限

# 号外

插件社区准备开发，欢迎来到[https://github.com/lxyddice/DingraiaPHP_offical](https://github.com/lxyddice/DingraiaPHP_offical)！

实现bot内通过指令获取插件，就像pip install 那么简单！还能检查更新、在线更新、自动配置插件帮助、自动配置插件日志！

# 最后要说

性能：<del>国内服务器完全访问加载要200-400ms，cpu使用时间约100ms，一次接收发送大约1-3秒，肯定远不及stream的，而且部署在国外服务器/套Cloudflare等操作还会降低速度，甚至超过5s。请自行选择是否使用，后续也会优化（访问网页慢点正常）。
内存消耗最低60kb，大量数据库操作约110kb，我想是十分够用的</del>

从v240301.1-Alpha 框架大更新后，底层很多原理改变，不少地方重写，还增加了很多逻辑和记录，因此需要的处理时间和内存大幅提升了，根据日志，PHP处理时间400-800ms，内存使用10-15MB（大量文件读取和变量导致的）

本项目是Dingraia的衍生作品，嫌py版本麻烦写的（）

有兴趣的话去点个star吧
