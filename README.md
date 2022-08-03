# easy-builder - 一款高效的Laravel综合服务/架构构建包

 Power By Abnermouke <abnermouke@outlook.com>

 此工具包由 Abnermouke <abnermouke@outlook.com> 开发并维护。

----

最后更新时间：2022年08月05日，持续更新中！！！

---


## Requirement - 环境要求

1. PHP >= 7.2（建议安装7.4）
2. **[Composer](https://getcomposer.org/)**
3. Laravel Framework 6+



## Installation - 安装方法

```shell
$ composer require "abnermouke/easy-builder"
```

## Configuration - 配置

- 在`config/app.php`的`providers`注册服务提供者

```php
Abnermouke\EasyBuilder\EasyBuilderServiceProvider::class,
```
- 如果你想只在非`production`的模式中使用构建器功能，可在`AppServiceProvider`中进行`register()`配置

```php
public function register()
{
  if ($this->app->environment() !== 'production') {
      $this->app->register(\Abnermouke\EasyBuilder\EasyBuilderServiceProvider::class);
  }
  // ...
}
```

-  构建工具提供一配置文件帮助开发者自行配置自己的构建配置，导出命令：

```shell
php artisan vendor:publish --provider="Abnermouke\EasyBuilder\EasyBuilderServiceProvider"
```

- 添加通用中间件至  app/Http/Kernel.php (如需在指定路由使用中间件，请将内容填充至 $routeMiddleware 内，并标记标识):


```php
protected $middleware = [
    
    ///
   
    \App\Http\Middleware\Abnermouke\EasyBuilderBaseMiddleware::class,
];
```

-  执行初始化构建器命令：

```shell
php artisan builder:init
```

- 添加辅助函数自动加载至 composer.json

```php
     "autoload": {
       
       // 
        
        "files": [
            "app/Helpers/functions.php",
            "app/Helpers/helpers.php",
            "app/Helpers/auth.php",
            "app/Helpers/response.php",
            "app/Helpers/projects.php"
        ]
    },
```

- 执行 Composer Autoload 以生效辅助函数

```shell
composer dump-autoload
```

- 更改数据库严格模式（兼容GroupBy查询 Mysql5.7）configs/database.php

```php

     "mysql" => [
        
        //关闭严格模式
        "strict" => false
        
     ],

```


### How to use it - 怎么使用

Abnermouke 提供了一些高效的构建命令帮助开发者快速使用构建器

```shell
$ php artisan builder:package {your-table-name-without-db-prefix}
```

例如：

```shell
$ php artisan builder:package accounts
```

生成`accounts`相关的系列文件信息。


### Extends Tool - 更多高效工具

Abnermouke 致力于规范开发标准、减少编码量，每一串代码都能成为可观的艺术品，在 easybuilder 中Abnermouke已将自身经验所得开发应用目录结构录入，并希望对使用此扩展包的开发人员也能起到一定的建议/引导作用。

---

##### [ Command ]  builder:interface

```shell
$ php artisan builder:interface {your-table-name-without-db-prefix}
```

builder:interface 为 package 的延伸工具，主要使用于多端适配目录结构，例如：当前项目共有小程序、APP、H5、管理后台等前端入口，数据库都为公用，以往我们或许会在Http/Controllers中进行目录区分，但仍需手动创建区分目录，更有甚者多端controller都是公用的情况

由于package构建后将在Http/Controllers中创建默认controller文件，目录结构已存在，为避免误解以及后期维护难度增大，现建议将各端入口更换至app/Interfaces中操作，可自定义目录结构，同时根据数据库表信息可以生成多个多端的controller以及service

其使用方法与package执行一致，但流程更加简化，只需录入数据库表名、表注释以及你要存储的目录即可，执行成功后将在指定目录生成：

```

app/Interfaces/指定目录/Controllers/YourTableController

app/Interfaces/指定目录/Services/YourTableInterfaceService

```

Controller将用于接受该端路由请求，Interface为该端业务逻辑处理器，作用等同于Service

多端开发中建议将app\Services中逻辑服务容器作为公共逻辑处理器，各端由自身Interface中service处理

命令如下：

```shell
$ php artisan builder:interface accounts
```


---

##### [ Tool ]  SearchableTool  关键词检索类

SearchableTool 是 easybuilder 对外提供的高效工具，仅对外开放set/search两方法，set为设置关键词，search为搜索，在调用过程中，easybuilder 将在项目中自动生成 acb_search_keywords 表用于储存关键词信息，数据库信息支持无限扩展，可同时对多个对象进行检索结构预存。

操作如下：

```

// 设置关键词（goods代表储存对象为商品，GOODS_ID为对应商品ID）
SearchableTool::set('goods', GOODS_ID, ['keyword_1', 'keyword_2']);

// set方法传参可理解为：设置商品的GOODS_IDS这个商品需绑定keyword_1、keyword_2两个关键词，在执行search时，只要是搜索商品，存在keyword_1、keyword_2任意一项时将输出GOODS_ID


//搜索关键词（goods代表储存对象为商品，搜索词检索后将返回满足任意关键词的所有商品ID）
SearchableTool::search('goods', ['keyword_1', 'keyword_3']);

```

无限扩展，可对文章、店铺等等一系列需要全文检索进行储存，关键词文本建议使用 easybuilder 中 JiebaLibrary 处理为多个关键词，以增加检索成功率

---


##### [ Tool ]  SentenceTool  语录/句子构建处理工具

SentenceTool 是 easybuilder 提供的一个小工具，仅需执行run方法即可获取金山词霸每日一句数据，包含中文英文鸡汤，创意小工具，根据自身需要选择使用即可

调用时，同样 easybuilder 将在项目中自动生成 acb_sentences 表用于储存此条信息，开发者可使用 app/Repository/Abnermouke/Builders/SentenceRepository.php 查询


---

##### [Tool]  InterfaceCryptographyTool  接口加密处理工具

InterfaceCryptographyTool 是一套完整的php端加解密解决方案，除验签加密外同时嵌套非对称加密，所有验证/加密过程均为自动完成，仅需配置指定APP与RSA2密钥即可


### 更新记录

2020.10.16 - 新增结巴分词相关处理逻辑（Abnermouke\EasyBuilder\Library\Currency\JiebaLibrary），请在使用前执行命令：

```shell
composer require fukuball/jieba-php
```

2020.10.16 - 新增php-DFA-filterWord相关处理逻辑（Abnermouke\EasyBuilder\Library\Currency\SensitiveFilterLibrary），请在使用前执行命令：

```shell
composer require lustre/php-dfa-sensitive
```

2021.09.16 - 修复已知BUG，重构builder组件，支持多层级目录（不限层级）并新增部分常用验证规则（Abnermouke\EasyBuilder\Library\Currency\ValidateLibrary），新增RSA非对称加解密方法，仅需配置内部私钥与外部公钥即可自动进行RSA加解密（可无损更新）,请在使用前确保openssl可用：

2021.10.22 - 修复加解密浮点数/数字等加密结果有误问题，新增 JSON_NUMERIC_CHECK|JSON_PRESERVE_ZERO_FRACTION 两种flag处理

```shell
composer require ext-openssl
```

2022.03.12 - 新增诸多功能

- 新增 AesLibrary Aes加解密公共类，解析表单加密结果
- 新增 SignatureLibrary 验签公共类，提供create（创建）、verify（验证）方法快捷生成/验证签名
- 新增更多实用辅助函数
- 新增abort_error辅助函数，快速响应错误页面
- 新增 Repository 公共方法 uniqueCode 可生成唯一类型编码（md5、string、number等）
- 新增 SearchableTool 工具类，用于关键词检索，文本录入后将关键词与文本对象关联，可实现多对多高效检索（自动过滤违禁词），自带学习功能，根据项目需求自动调整和记录检索对象

2022.03.31 - 新增诸多功能

- 新增AmapLibrary 高德地图处理公共类，获取高德相关接口
- 新增DeviceLibrary 设备检测公共类，快捷检测当前设备类型
- 新增QrLibrary 基于 "simplesoftwareio/simple-qrcode" 快速生成指定二维码文件至指定storage目录
- 新增StorageFileLibrary Storage文件处理公共类，快捷处理Storage文件等操作

2022.04.05 - 主要增加与abnermouke/console-builder的适配

- 新增对Laravel6的支持，LTS版本已完美适配，Laravel9及其之后的版本待官方稳定后兼容适配
- 新增 七牛SDK为默认加载包，并在 StorageFileLibrary 中新增对七牛云的快捷操作（上传、删除）
- 新增默认 TestCommand，可在 app/Console/Commands/TestCommand 添加开发测试，命令：`php artisan test:test`

2022.06.18 - 修复已知问题

- 修复所有涉及GroupBy查询无效的问题
- 新增count查询携带groupBy查询条件

2022.07.23 - 修复已知问题并带来更多更新

- 修复Storage文件处理时可能存在的文件名问题，杜绝空格等因素存在
- 新增builder命令：builder:interface，适用于多设备应用开发，可将统一表针对不同端自动生成service以及controller
- 新增 SentenceTool 工具类，用于爬取每日词条使用，鸡汤文本
- 新增与 abnermoke/pros 构建框架的适配
- 新增 InterfaceCryptographyTool 工具类，用于接口端加密/解密使用，内含基本验签加密、RSA加密

2022.07.25 - 修复问题

- 修复批量执行 builder:package 时中途更换db_prefix失效的问题，单一执行不影响
- 修复 SentenceTool 本地localhost请求失败问题

2022.08.05 - 优化并提供更多支持

- 优化 builder:interface 配合 abnermouke/pros 可快速生成控制台接口逻辑、路由以及blade模版，增效降码
- 新增 ValidateLibrary 检测是否包含HTML方法
- 新增 BaseRepository 自动生成全大写唯一编码
- 进一步适配 abnermouke/pros 包

## License

MIT
