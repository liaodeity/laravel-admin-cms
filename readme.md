
### Laravel Admin CMS

<p align="center">
  <a href="https://github.com/laravel/framework">
    <img src="https://img.shields.io/badge/laravel-8.48.2-brightgreen.svg" alt="laravel">
  </a>
  <a href="https://www.layui.com">
    <img src="https://img.shields.io/badge/layui-2.5.5-brightgreen.svg" alt="layui">
  </a>
  <img src="https://img.shields.io/badge/License-MIT-yellow.svg">
</p>

基于Laravel的后台内容管理开发系统，后台基于layui-mini前端主题。能够帮助你节约开发基础系统功能，及一些常规功能插件。是一个快速上手，项目基础开发，外包开发首选CMS。

- 后台系统常用功能

- 支持主题风格设置`单页面`和`iframe多标签`

- 简单上手，快速开发

- 更多插件功能，一键安装扩展

- 完全开源，更好的进行业务开发

- 定期更新laravel主版本

希望本项目可以帮助你，节约开发时间。 多多`Star`是给予我们最大的动力。

### 演示demo

- 地址：[https://cmf.jianbaizhan.com/admin](https://cmf.jianbaizhan.com/admin)

- 账号：admin

- 密码：123456

- 演示说明：理性演示，误删除基础数据，可自行添加数据，自行删除（数据库会不定时重置抹除数据）

### 项目仓库说明

因网络访问原因，目前通过首发`Gitee`版本，一旦有新的发行版将同步到`GitHub`

- 首发Gitee：[https://gitee.com/liaodeiy/laravel-admin-cms](https://gitee.com/liaodeiy/laravel-admin-cms)

- 同步GitHub：[https://github.com/liaodeity/laravel-admin-cms](https://github.com/liaodeity/laravel-admin-cms)


### 如何使用

- 使用前学习

  需对`laravel`有基础了解，以及简单开发经验

- 获取代码
```bash
git clone https://gitee.com/liaodeiy/laravel-admin-cms.git
cd laravel-admin-cms
composer install
```
- 配置数据库`.env`(如没有复制`.env.example`改名)
```bash
php artisan key:generate
```
- 数据库表迁移
```bash
php artisan migrate
```
- 基础数据信息填充
```bash
php artisan db:seed --class=InitSeeder
```
- 上传文件路径符号链接
```bash
php artisan storage:link
```
- 项目启动
```bash
php artisan serve
```
- 项目访问

  后台地址：`http://127.0.0.1:8000/admin`
  
  后台账号：admin
  
  后台密码：123456（生产环境必须修改密码）


### 开发文档

- [https://www.jianbaizhan.com/docs/cms](https://www.jianbaizhan.com/docs/cms)[待完善]

### 基础功能

- 用户管理

- 菜单管理

- 角色权限管理

- 配置管理

- 日志管理

- 插件管理

### 云存储支持

开启云存储后，在上传附件到本地后，同时上传一份到云存储位置，并获取云地址。

查看配置文件`gui.upload_driver`和`filesystems.disks.*`了解配置认证秘钥

- 阿里云：oss（[开通地址](https://www.aliyun.com/product/oss?userCode=hhlk0aji)）
- 七牛云：kodo（[开通地址，免费10G](https://s.qiniu.com/jyQv6v)）


### 插件扩展

- 暂无

### 如何贡献
 
 - 发布[issue](https://gitee.com/liaodeiy/laravel-admin-cms/issues)进行问题反馈和建议
 
 - 通过[Pull Request](https://gitee.com/liaodeiy/laravel-admin-cms/pulls)提交修复
 
 - 完善我们的文档和例子
 
 - 如果你有一个好的想法和需求功能(不成熟建议也行)，告诉我们，一经采用，我们将加入开发计划([项目开发计划进度](https://github.com/liaodeity/laravel-admin-cms/projects/1))


## 捐赠

如果觉得本项目帮助到你，捐赠我们，支持本项目的开发维护(请作者喝杯咖啡吧:coffee:)。
![image](https://www.jianbaizhan.com/home/images/donate.png)

## 其他

`gui-giggle` 基于以下插件或服务（排名不分先后，罗列部分，[更多查看](https://www.jianbaizhan.com/cms)）：

- [Laravel](https://laravel.com/)

- [layui](https://gitee.com/sentsin/layui)

- [layui-mini](https://gitee.com/zhongshaofa/layuimini)

- [spatie/laravel-permission](https://github.com/spatie/laravel-permission)

- [guzzlehttp/guzzle](https://github.com/guzzlehttp/guzzle)

- [mews/captcha](https://github.com/mews/captcha)

- [zanysoft/laravel-zip](https://github.com/zanysoft/laravel-zip)

- ......

## 界面截图
- 单页面效果

![image](public/preview/onepage.png)
  
- 多标签iframe

![image](public/preview/tab_iframe.png)

## 许可证

完全开源免费，请保留项目开源版权说明。本项目遵循开源协议 [MIT license](https://opensource.org/licenses/MIT).
