# 框架地址
https://gitee.com/knowbox-platform/kbox-platform-framework

# yii参考手册
http://www.yiichina.com/doc

# 新增模块
php module.php {模块名称（纯字符组成）}
<br /> 
新增模块后需要重新初始化

# 初始化项目
php init --env={环境名称}
或 php init  然后选择

# composer管理
php composer.phar require xxxxx
<br/>
php composer.phar require kbox/base dev-master (开发分支)
<br/>
如果本机已经安装composer,也可用composer require

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
docs                     文档
    sql                     版本提交变更sql 名称类似1.0.0.sql
    crontab                 版本提交变更定时任务 名称类似 1.0.0.cron
environments             环境配置 通过php init生效
    dev                     开发环境
    beta                    测试环境
    prod                    线上环境
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
vender                    依赖包文件 composer管理
```

# 接口文档

#### 接口文档采用apidoc
#### 安装
npm install apidoc -g
#### 生成doc
apidoc -i appname/ -o apidoc/
#### apidoc 文档
http://apidocjs.com
```
 /**
     * @api {POST} /common/example/text 示例接口
     * @apiName 示例接口名称
     * @apiGroup 示例接口分组
     * @apiVersion 1.0.0
     * @apiDescription 示例接口描述
     * @apiParam tmp_parms 示例参数
     * @apiSuccess success_date 示例返回值
     * @apiParamExample {json} 请求参数示例:
     * { "content1": "This is an example content" }
     * @apiSuccessExample {json} 成功返回值示例
     * { "code" : 0, "message":"success" ,"data":[]}
     * @apiErrorExample {json} 失败返回值示例
     * { "code" : 500, "message":"error_message" ,"data":[]}
     */
```

