# Export
基于360EntSecGroup-Skylar/excelize/v2与guzzlehttp/guzzle的导出组件

旨在一个解耦的，高效的导出服务，如果您需要更多的定制化服务应该参阅360EntSecGroup-Skylar/excelize/v2来制作自己的组件

## 要求
laravel版本 >= 7.x 或 guzzle版本 >= 7.4
使用ORACLE连接时需要建立ORACLE环境(GCC与client)

## 安装
$ composer require oursdreams/export -vvv

## 服务
此组件默认9722端口，您应该确保您的9722端口未被占用

$ php artisan export:serve

您可以通过 php artisan export 来查看所有可用命令

## 使用
JSON：

        $export = new Export();
        $response = $export->json(["row"],[["list"]]);
        
通过传递row与list来指定表头与表身,注意此处list应为二维数组

## 其他
合并单元格：下方示例将excel的A，B列分别从1行合并至3行

        $export = new Export();
        $export->setMergeFormat(["A","B"],["1"=>"3"]);
        
内部换行：下方示例将excel的A,B列设置为允许内部换行

        $export = new Export();
        $export->setMergeFormat(["A","B"])->setWarpTextFormat();
        
        