<h1 align="center">Laravel DB Backup</h1>
<p align="center"><a href="https://packagist.org/packages/haruncpi/laravel-db-backup"><img src="https://badgen.net/packagist/v/haruncpi/laravel-db-backup" /></a>
    <a href="https://creativecommons.org/licenses/by/4.0/"><img src="https://badgen.net/badge/licence/CC BY 4.0/23BCCB" /></a>
     <a href=""><img src="https://badgen.net/packagist/dt/haruncpi/laravel-db-backup"/></a>
    <a href="https://twitter.com/laravelarticle"><img src="https://badgen.net/badge/twitter/@laravelarticle/1DA1F2?icon&label" /></a>
    <a href="https://facebook.com/laravelarticle"><img src="https://badgen.net/badge/facebook/laravelarticle/3b5998"/></a>
</p>

<p align="center">Easily backup your Laravel application database!</p>

<br>
<p>DB Support: MySQL<br>
Disk Support: local, s3</p>

## Documentation
Installation
```
composer require haruncpi/laravel-db-backup
```

Backup to local
```
php artisan db:backup
```
Backup to S3
```
php artisan db:backup s3
```

## Backup Location
- local disk: storage/app/backups
- s3 bucket: backups

## Tutorial
[Schedule laravel database backup - cPanel, VPS!](https://laravelarticle.com/laravel-database-backup-tutorial)

## Other Packages
- [Laravel Simple Filemanager](https://github.com/haruncpi/laravel-simple-filemanager) - A simple filemanager for Laravel.
- [Laravel H](https://github.com/haruncpi/laravel-h) - A helper package for Laravel Framework.
- [Laravel ID generator](https://github.com/haruncpi/laravel-id-generator) - A laravel package for custom database ID generation.
- [Laravel Option Framework](https://github.com/haruncpi/laravel-option-framework) - Option framework for Laravel.
- [Laravel User Activity](https://github.com/haruncpi/laravel-user-activity) - Monitor your user activity easily! 