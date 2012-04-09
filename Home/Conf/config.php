<?php
$config	=	require './config.php';
$admin_config	= array(
    'URL_MODEL'					=>1, 				// 如果你的环境不支持PATHINFO 请设置为3
	'TMPL_CACHE_ON'				=>true, 			//开启模板缓存
	'URL_CASE_INSENSITIVE'  	=>true, 			//URL不区分大小写
	
	
	'VAR_PAGE'=>'pageNum',

	'USER_AUTH_ON'=>true,
	'USER_AUTH_TYPE'			=>1,				// 默认认证类型 1 登录认证 2 实时认证
	'USER_AUTH_KEY'				=>'authId',			// 用户认证SESSION标记
    'ADMIN_AUTH_KEY'			=>'administrator',
	'USER_AUTH_MODEL'			=>'User',			// 默认验证数据表模型
	'AUTH_PWD_ENCODER'			=>'md5',			// 用户认证密码加密方式
	'USER_AUTH_GATEWAY'			=>'/Public/login',	// 默认认证网关
	'NOT_AUTH_MODULE'			=>'Public',			// 默认无需认证模块
	'REQUIRE_AUTH_MODULE'		=>'',				// 默认需要认证模块
	'NOT_AUTH_ACTION'			=>'',				// 默认无需认证操作
	'REQUIRE_AUTH_ACTION'		=>'',				// 默认需要认证操作
    'GUEST_AUTH_ON'          	=> false,    		// 是否开启游客授权访问
    'GUEST_AUTH_ID'           	=>0,     			// 游客的用户ID

    'DB_LIKE_FIELDS'			=>'title|remark',	//搜索Like匹配字段
	'RBAC_ROLE_TABLE'			=>'role',
	'RBAC_USER_TABLE'			=>'role_user',
	'RBAC_ACCESS_TABLE' 		=>'access',
	'RBAC_NODE_TABLE'			=>'node',

);
return array_merge($config,$admin_config);