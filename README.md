### 配置
`/config/app.php` 添加以下配置
```php
    'upload' => [
        'root_path' => './uploads/',
        'base_url' => "http://{$_SERVER['HTTP_HOST']}/uploads/",
        'size' => 512000,
        'ext' => 'jpg,jpeg,png,gif',
    ],

    'wxmp' => [
        'appid' => '小程序 APP ID',
        'secret' => '小程序 APP Secret',
    ]
```

### 路由
`/route/route.php` 添加以下路由
```php
Route::any('api/upload/:subdir', '\\tt\\UploadController@index');
```