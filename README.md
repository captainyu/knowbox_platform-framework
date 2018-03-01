# 新增模块
php module.php {模块名称（纯字符组成）}
<br /> 
新增模块后需要重新初始化

# 初始化项目
php init --env={环境名称}
或 php init  然后选择

# 目录结构
```
common                   公共模块
    config/                 公共配置
    models/                 数据模块
        base/                   数据模块公共库 & 数据库基类
        db/                     数据库ActiveRecord 集成common\models\base\BaseActiveRecord
        logic/                  逻辑数据库 存枚举值
console                  命令行模块
    config/                 命令行模块配置
    controllers/            命令行controller
    models/                 命令行models
    runtime/                命令行执行信息 日志等内容 需要777权限
appname                  appname业务模块 【通过php module.php appname创建】
    config/                 配置
    controllers/            基类
    models/                 基础modal
    modules/                业务细分模块
        common                  业务具体细分模块 内部完成controller/model
                                model可分多层 也可继续增加内部config/cache等目录
        ...                 
    runtime/                日志等信息目录 需要777权限
    web/                    nginx根目录 入口文件index.php
```