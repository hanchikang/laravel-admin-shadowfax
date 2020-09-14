# Adapter for encore/laravel-admin + huang-yi/shadowfax
使laravel-admin用上swoole。

## Require
- encore/laravel-admin

## Installation
禁止laravel发现laravel-admin扩展包，在composer.json中添加：

```php
    "extra": {
        "laravel": {
            "dont-discover": [
                "encore/laravel-admin"
            ]
        }
    },
```

运行下面的命令安装:

    "composer require  ckhan/laravel-admin-shadowfax"
    
执行适配命令:

    "php artisan las:adapter"    

## License
`ckhan/laravel-admin-shadowfax` is licensed under [The MIT License (MIT)](LICENSE).
