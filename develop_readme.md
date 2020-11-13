# 系统部署指引
## 更新记录
日期 | 描述 | 作者
---|---|---
20200316| 添加[基本信息](#基本信息)内容 | gui
20200316| 添加[网站部署](#网站部署)内容 | gui
20200409| 添加[开发说明](#开发说明)内容 | gui

##基本信息

### 1.Laravel6.0说明

文档：https://laravel.com/docs/6.x

### 2.目录文件说明
```bash
app/Console                 #定时执行目录或命令行
app/Http/Controllers        #控制器目录
app/Entities                #数据模型目录
app/Exports                 #数据导出类目录
app/Libs                    #类库存放
app/Repositories            #业务处理类目录
app/Services                #服务类目录
app/Validators              #表单验证类目录
app/helpers.php             #函数文件
config                      #配置相关目录
public                      #网站根目录，公共目录
public/upload               #上传文件存放目录
resources/lang              #语言配置目录
resources/views             #模板view文件目录
routes                      #路由目录
storage                     #缓存、日志、临时文件等存储目录
vendor                      #第三方插件目录
.env                        #基础环境变量设置文件
```

## 网站部署
### 1.服务器要求
- PHP >= 7.2.0
- MySql > 5.6
- BCMath PHP Extension
- Ctype PHP Extension
- Fileinfo PHP extension
- JSON PHP Extension
- Mbstring PHP Extension
- OpenSSL PHP Extension
- PDO PHP Extension
- Tokenizer PHP Extension
- XML PHP Extension

### 2.数据库配置

在.env中配置数据库信息


### 3.网站根目录

应该将Web服务器的web根配置为public目录。这个index.php在这个目录中，充当输入应用程序的所有HTTP请求的前端控制器。

### 4.存储目录权限

- storage 缓存等目录需有读写权限

- public/upload 上传目录必须有读写权限

- public/card-bg 卡片背景目录必须有读写权限


### 5.定时执行添加

liunx环境下，在crontab中添加，需将下面命令的path-to-your-project替换成正式服务器的目录地址

```bash
* * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1
```

### 6.访问地址美化

Apache的.htaccess
```bash
Options +FollowSymLinks -Indexes
 RewriteEngine On
 
 RewriteCond %{HTTP:Authorization} .
 RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]
 
 RewriteCond %{REQUEST_FILENAME} !-d
 RewriteCond %{REQUEST_FILENAME} !-f
 RewriteRule ^ index.php [L]
 ```
 
 Nginx
 ```shen
location / {
    try_files $uri $uri/ /index.php?$query_string;
}
```

## 开发说明
### 1.Composer安装

开发第三方类库更新，如不适用，可安装

下载地址：https://getcomposer.org/download/

安装composer后，在.env目录下，使用命令进行更新

```bash
composer update
```

### 2.导出格式字体

composer update 后可能会重置文件，所以需修改

需要列自动宽度，字体设置成：

```php
//vendor/phpoffice/phpspreadsheet/src/PhpSpreadsheet/Style/Font.php:21
protected $name = 'Verdana';
```

### 3.修改验证码支持减法

composer update 后可能会重置文件，所以需修改

vendor/mews/captcha/src/Captcha.php:318

```php
//===================
if(array_random ([1,2]) == 1){
    //减法
    $bag = "$x - $y = ";
    $key = $x - $y;
}else{
    //加法
    $bag = "$x + $y = ";
    $key = $x + $y;
}
//=====================
//原始效果
//            $bag = "$x + $y = ";
//            $key = $x + $y;
//=====================
```

### 4.常用配置文件说明

- config/captcha.php 验证码配置文件

- config/wechat.php 微信配置文件


### 5.public公共目录说明

- public/admin-ui 后台静态文件存放

- public/card-bg 卡片二维码基础文件存放

- public/font 字体文件存放

- public/js 公共js文件存放

- public/member-ui 微信移动端静态文件存放

- public/upload 文件上传目录



### 6.lang语言文字配置

- resources/lang/zh-CN.json 文件替换成html颜色格式，通常状态

- resources/lang/zh-CN/parameter.php 变量标签定义，通常是下拉框、可选项

### 7.views文件说明

- resources/views/admin 总后台模板文件存放

- resources/views/agent 代理商模板文件存放

- resources/views/common 公共模板文件存放

- resources/views/dialogs 弹窗模板文件存放

- resources/views/errors 错误模板文件存放

- resources/views/exports 导出模板文件存放

- resources/views/login 登录模板文件存放

- resources/views/login 微信会员端模板文件存放

- resources/views/region 区域选择模板文件存放





