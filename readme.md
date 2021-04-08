
### Laravel Admin CMS

基于Laravel的后台内容管理开发系统，后台基于layui-mini前端主题。能够帮助你节约开发基础系统功能，及一些常规功能插件。是一个快速上手，项目基础开发，外包开发首选CMS。

- 后台系统常用功能

- 简单上手，快速开发

- 更多插件功能，一键安装扩展

- 完全开源，更好的进行业务开发

- 定期更新laravel主版本

希望本项目可以为你，节约开发时间。 

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
- 项目启动
```bash
php artisan serve
```
- 项目访问

  后台地址：`http://127.0.0.1:8000/admin`
  
  后台账号：admin
  
  后台密码：123456（生产环境必须修改密码）

### 开发文档
[https://www.jianbaizhan.com/cms](https://www.jianbaizhan.com/cms)

### 如何贡献
 
 - 发布[issue](https://gitee.com/liaodeiy/laravel-admin-cms/issues)进行问题反馈和建议
 
 - 通过[Pull Request](https://gitee.com/liaodeiy/laravel-admin-cms/pulls)提交修复
 
 - 完善我们的文档和例子

### 基础功能

- 用户管理

- 菜单管理

- 角色权限管理

- 配置管理

- 日志管理

- 插件管理

## 插件扩展

- 暂无

## 捐赠

如果觉得本项目帮助到你，捐赠我们，支持本项目的开发维护(请作者喝杯咖啡吧:coffee:)。
![image](https://www.jianbaizhan.com/home/images/donate.png)

## 其他

`gui-giggle` 基于以下插件或服务（罗列部分，[更多查看](https://www.jianbaizhan.com/cms)）：

- **[Laravel](https://laravel.com/)**
- **[layui](https://gitee.com/sentsin/layui)**
- **[layui-mini](https://gitee.com/zhongshaofa/layuimini)**

## 许可证

本项目遵循开源协议 [MIT license](https://opensource.org/licenses/MIT).
