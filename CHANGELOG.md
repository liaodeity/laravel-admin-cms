# Release Notes

## [Unreleased](https://github.com/liaodeity/laravel-admin-cms/compare/v8.3.0...8.x)

## [v8.3.0(2021-06-30)](https://github.com/liaodeity/laravel-admin-cms/compare/v8.2.5...v8.3.0)

### Added
- 增加Validator类用于表单认证（[#12](https://github.com/liaodeity/laravel-admin-cms/pull/12)）

### Changed
- 修改注释
- composer update


## [v8.2.5(2021-06-28)](https://github.com/liaodeity/laravel-admin-cms/compare/v8.2.4...v8.2.5)

### Added
- 添加会员管理导出记录功能（[#11](https://github.com/liaodeity/laravel-admin-cms/pull/11)）

### Changed
- 更新路由归类
- 调整视图文件操作按钮判断
- 添加导出询问确认

## [v8.2.4(2021-06-23)](https://github.com/liaodeity/laravel-admin-cms/compare/v8.2.3...v8.2.4)

### Added
- 添加附件管理列表（[#10](https://github.com/liaodeity/laravel-admin-cms/pull/10)）
- 添加支持图片附件上传

### Changed
- 变更route目录，添加admin.php路由（[47411f0](https://github.com/liaodeity/laravel-admin-cms/pull/10/commits/47411f0eea800ef0cfc9551090bccc84fcb05cd1)）
- 更新菜单和权限，添加基础表配置

## [v8.2.3(2021-06-10)](https://github.com/liaodeity/laravel-admin-cms/compare/v8.2.2...v8.2.3)

### Fixed
- 修复发现问题

## [v8.2.2(2021-06-09)](https://github.com/liaodeity/laravel-admin-cms/compare/v8.2.1...v8.2.2)

### Fixed
- 修复发现问题

## [v8.2.1(2021-06-09)](https://github.com/liaodeity/laravel-admin-cms/compare/v8.2.0...v8.2.1)

### Added
- 添加单页面管理，添加富文本编辑器，修改图片上传
- 更新wangEditor到最新4.7.2、修改富文本图片上传
- 添加开发进度，修改说明

## [v8.2.0(2021-06-03)](https://github.com/liaodeity/laravel-admin-cms/compare/v8.1.14...v8.2.0)

### Added
- 更新用户表结构，将登录次数及登录时间字段加入主表users（[#5](https://github.com/liaodeity/laravel-admin-cms/pull/5)）
- 新增变更日志`CHANGELOG.md`记录
### Changed
- 修改官网和文档地址
- 调整字段长度限制
- 添加演示站点代码判断，防止修改超级权限账号

## [v8.1.4 (2021-05-28)](https://github.com/liaodeity/laravel-admin-cms/compare/v8.1.3...v8.1.4)

### Changed
- 优化菜单管理模块，上级菜单、添加、修改（[#4](https://github.com/liaodeity/laravel-admin-cms/pull/4)）
- 修改已知问题优化

## [v8.1.3 (2021-05-21)](https://github.com/liaodeity/laravel-admin-cms/compare/v8.1.2...v8.1.3)

### Added
-记录浏览来源（[#3](https://github.com/liaodeity/laravel-admin-cms/pull/3)）

### Fixed
-修复资源路径

## [v8.1.2 (2021-05-19)](https://github.com/liaodeity/laravel-admin-cms/compare/v8.1.1...v8.1.2)

### Fixed
- Fixed基础表备份目录不存在，修改说明（[#2](https://github.com/liaodeity/laravel-admin-cms/pull/2)）

## [v8.1.1 (2021-05-14)](https://github.com/liaodeity/laravel-admin-cms/compare/v8.1.0...v8.1.1)

### Changed
- 修改说明、Fixed参数 ([#1](https://github.com/liaodeity/laravel-admin-cms/pull/1))


## [v8.1.0 (2021-05-13)](https://github.com/liaodeity/laravel-admin-cms/compare/v8.0.1...v8.0.2)

### Added
- 添加后台主题单页面模式 ([2d3930b](https://github.com/liaodeity/laravel-admin-cms/commit/2d3930b3b1feca0331f07908f28c82c40c2c16fe),[968d5ae](https://github.com/liaodeity/laravel-admin-cms/commit/968d5ae95cdf6eef37c9974dd896fb3b6acfad04))

### Fixed
- 修改渲染 ([d070358](https://github.com/liaodeity/laravel-admin-cms/commit/d0703586b58cbe59bb7688cfdc5d2d389fa3e5d6))

## [v8.0.1 (2021-05-13)](https://github.com/liaodeity/laravel-admin-cms/compare/v8.0.0...v8.0.1)

### Added
- 用户管理>用户列表添加Tab多列表切换展示 ([cb7c6de](https://github.com/liaodeity/laravel-admin-cms/commit/cb7c6deb861d33f6e8b55b0f659001c7b0eecfff))
- 更新函数助手([e107b3f](https://github.com/liaodeity/laravel-admin-cms/commit/e107b3f9b03915f7fc6f5c0d2256237a004d9410), [54c66ee](https://github.com/liaodeity/laravel-admin-cms/commit/54c66eecc85462d9bd7865073167817eeacdd61d))


## [v8.0.0 (2021-05-13)](https://github.com/liaodeity/laravel-admin-cms/compare/v8.0.0)
版本发布AdminCms基本功能

### Added
- 系统基础框架功能
- 基本信息配置
- 账号管理
- 日志管理
- 菜单管理
- 角色管理
- 用户信息修改
- more...
