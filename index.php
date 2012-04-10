<?php
define('RUNTIME_ALLINONE', true);  // 开启ALLINONE运行模式
define('APP_NAME','Home');      // 定义项目名称
define('APP_PATH','./Home');	//定义项目目录
define('THINK_PATH','./ThinkPHP');	//定义ThinkPHP目录
//define('APP_DEBUG', true);
require('ThinkPHP/ThinkPHP.php');   // 加载入口文件
$App = new App();                   // 实例化项目
$App->run();                       // 执行