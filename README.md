# 诶，我回来了

###### 新版本正在逐步上传Github...助力更简单使用

## 24.7.xx 可以继续开发是好的~

## 24.05.13 开发服务器没了，人在外地无法回去处理，暂时停更

[关于ffmpeg](https://github.com/lxyddice/DingraiaPHP/blob/main/doc/%E5%85%B3%E4%BA%8Effmpeg.md)

![image](https://github.com/lxyddice/DingraiaPHP/assets/95132858/34dd09c7-ad59-4503-88c4-5b8f6afa135b)

越写越像答辩了，一个更完整的php ez开发框架

# 这是什么？

这个是一个关于运行钉钉机器人的代码，用来运行Webhook的钉钉机器人功能，框架中有部分小工具。
只实现了23.3%的功能，将来可能会完善。
作者是PHP废物，请不要催，你行你上。

# 特点

使用简单，插件好写（php是这样的），支持At

|  消息   | 支持  |
|  ----  | ----  |
| webhookMessage  | √ |
| webhookMarkdown  | √ |
| webhookLink  | √ |
| webhookActioncard  | √ |
| webhookFeedcard  | √ |
| 内部群普通消息  | √ |
| 内部群图片消息  | √ |
| 内部群文件消息  | √ |
| 内部群音频消息  | √ |
| 内部群视频消息  | x |
| 互动卡片  | 部分支持 |

# 注意

本项目适用于一般聊天机器人、娱乐机器人的开发，不建议用于生产环境

At需要传入userid

作者自身已经挂着该框架，保证框架确实可用 ~~如果懒癌发作不更新的另当别论~~

**自带文档可能更新不及时！（懒）**

**本项目基于企业内部机器人开发，使用企业内部机器人以获得更好的体验**

# 需求

需要公网可访问的服务器，还得拥有企业内部开发权限

# 如何使用？

在插件文件夹下编写插件，可以在plugin/demo查看示例

查看 [文档](https://doc.lxyddice.top/dingraia_php/dingraia_php)

由于Cloudflare和L服务器的原因，博客和文档可能无法使用，如需帮助请联系我

## 安装

[使用前工作与安装](https://doc.lxyddice.top/dingraia_php/dingraia_php/shi-yong-qian-gong-zuo-yu-an-zhuang)

# TODO

让我想想...

# 内测招募

框架还未开源，因为问题比较多。

如果可以，请帮助我测试和完善框架，谢谢喵...

请提交issue或在钉钉联系我...邮箱也行，lxy@lxyddice.top

# 号外

插件社区准备开发，欢迎来到[https://github.com/lxyddice/DingraiaPHP_offical](https://github.com/lxyddice/DingraiaPHP_offical)！

实现bot内通过指令获取插件，就像pip install 那么简单！还能检查更新、在线更新、自动配置插件帮助、自动配置插件日志！

# 最后要说

性能：<del>国内服务器完全访问加载要200-400ms，cpu使用时间约100ms，一次接收发送大约1-3秒，肯定远不及stream的，而且部署在国外服务器/套Cloudflare等操作还会降低速度，甚至超过5s。请自行选择是否使用，后续也会优化（访问网页慢点正常）。
内存消耗最低60kb，大量数据库操作约110kb，我想是十分够用的</del>

从v240301.1-Alpha 框架大更新后，底层很多原理改变，不少地方重写，还增加了很多逻辑和记录，因此需要的处理时间和内存大幅提升了，根据日志，PHP处理时间400-800ms，内存使用10-15MB（大量文件读取和变量导致的）

本项目是DingraiaPY的衍生作品，嫌py版本麻烦写的（）
有兴趣的话去点个star吧
