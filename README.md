# FastSupport 快客服项目

> 基于 Laravel & Nuxt 的客服系统

## 特性

- Nuxt 2.11
- Laravel 6
- SPA or SSR
- Socialite integration
- VueI18n + ESlint + Bootstrap 4 + Font Awesome 5
- Login, register, email verification and password reset

## 安装

```
composer install
cp .env.example .env

#修改数据库、Redis密码和
code .env

php artisan key:generate
php artisan jwt:secret
php artisan migrate
```


```
#初始化管理员
php artisan db:seed --class=Database\\Seeders\\PerissionSeeder
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
