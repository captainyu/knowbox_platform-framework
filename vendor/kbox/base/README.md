# 作业盒子平台组  任务流SDK 

## composer自动安装：
composer.json repositories下增加
{
    "type": "path",
    "url":"/xxxxx/kbox/base"
},
composer require kbox/base

## composer手动安装：
复制/kbox/task 到 vender/kbox/base
vender/composer/autoload_psr4 增加 'kbox\\base\\' => array($vendorDir . '/kbox/base'),

