# 功能

- 加载php配置文件
- 加载php配置文件夹
- 批量加载php配置文件

# 示例

```
require 'vendor/autoload.php';

// 加载文件夹
$path = '/rootpath/config';
$config = new Config($path);

// 加载单个文件
$path = '/path/to/config/databases.php';
$config = new Config($path);

// 加载多个文件
$paths = [
    '/rootpath/config',
    '/path/to/config/databases.php',
];
$config = new Config($paths);

// 取值
$host = $config->get('databases.mysql.host');
$host = $config['databses.mysql.host'];
```