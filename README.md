# mt-dataapi-php
Simple PHP Wrapper for MT DataAPI v2
# Description
# Installation
composer
```json
    {
        "require": {
            "jaxx2104/mt-dataapi-php": "dev-master"
        }
    }
```
# Usage
```php
    <?php
    require_once('vendor/autoload.php');
    $url  = 'http://xxxxxxx.com/mt/mt-data-api.cgi';
    $user = 'xxxxxxx@mail.com';
    $pass = 'xxxxxxx';
    $blogId = 1;
    
    $mt = new MT\DataApi($url);
    
    if (!$mt->login(["username" => $user, "password" => $pass])){
        var_dump("faild login");
        var_dump($mt->response);
        exit(1);
    }
    //success login
    var_dump($mt->response);
    
    if (!$mt->listCategory($blogId)) {
        var_dump("faild get category");
        var_dump($mt->response);
        exit(1);
    }
    //success get category
    var_dump($mt->response);
```
