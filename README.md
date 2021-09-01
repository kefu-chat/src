# 闪客服后端

[![单元测试](https://github.com/fastsupport-cn/src/workflows/build/badge.svg)](https://github.com/fastsupport-cn/src/actions?query=workflow%3Abuild)

> 基于 Laravel 的客服系统: [www.kefu.chat](https://www.kefu.chat).

## 安装

```bash
composer install
cp .env.example .env

#修改数据库、Redis密码和
code .env

php artisan key:generate
php artisan jwt:secret
php artisan migrate
```

```bash
#导入省市区街道数据库
php artisan pca:refreshData -d jd
```

```bash
#初始化套餐信息
php artisan db:seed --class=Database\\Seeders\\PlansTableSeeder

#初始化管理员
php artisan db:seed --class=Database\\Seeders\\PermissionSeeder
php artisan db:seed --class=Database\\Seeders\\AdminSeeder

#待分配对话
php artisan db:seed --class=Database\\Seeders\\ConversationTableSeeder
php artisan db:seed --class=Database\\Seeders\\MessageTableSeeder

#已分配对话
php artisan db:seed --class=Database\\Seeders\\ConversationTableSeeder
php artisan db:seed --class=Database\\Seeders\\MessageTableSeeder
php artisan db:seed --class=Database\\Seeders\\AssignSeeder

#全新企业
php artisan db:seed --class=Database\\Seeders\\InstitutionsTableSeeder

#全新客服
php artisan db:seed --class=Database\\Seeders\\UsersTableSeeder

#全新访客
php artisan db:seed --class=Database\\Seeders\\VisitorsTableSeeder
```

## Socket 服务器启动

配置文件
```bash
cp laravel-echo-server.json.example laravel-echo-server.json
```

启动服务
```
sh ./laravel-echo-server-start.sh
```

## 单元测试
```bash
php artisan test
```

## 授权协议

[MIT License](LICENSE)
