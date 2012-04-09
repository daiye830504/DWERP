<?php
/*
* 安装文件
*/
error_reporting(0);
session_start();
define('DWERP_INSTALL', TRUE);
define('DWERP_ROOT', str_replace('\\', '/', substr(dirname(__FILE__), 0, -7)));

$_LCVERSION = '1.0 正式版';

include 'inc/install.func.php';
include 'inc/install.lang.php';

$timestamp				=	time();
$ip						=	getip();
$database			    =	'inc/dwerp.sql';
$dwerp_config_file     =   'config.php';

header('Content-Type: text/html; charset=utf-8');
if(file_exists(DWERP_ROOT."/Install/install.lock")){
	exit($lang['install_is_lock']);
}
if(!is_readable($database))
{
	exit($lang['install_db_error']);
}
$quit = false;
$msg = $alert = $link = $sql = $allownext = '';
$PHP_SELF = addslashes(htmlspecialchars($_SERVER['PHP_SELF'] ? $_SERVER['PHP_SELF'] : $_SERVER['SCRIPT_NAME']));
set_magic_quotes_runtime(0);
if (!get_magic_quotes_gpc())
{
	addS($_POST);
	addS($_GET);
}
@extract($_POST);
@extract($_GET);

$step = $_GET['step'];
switch($step){
	case 1:
		include tpl("step1");
		break;
	case 2:
		$agree = $_POST['agree'];
		if($agree == 1){
			$dirarray = array (
				'Public/',
				'Install/',
			);
			$writeable = array();
			foreach($dirarray as $key => $dir){
				if(writable($dir)){
					$writeable[$key] = $dir.result(1, 0);
				}else{
					$writeable[$key] = $dir.result(0, 0);
					$quit = TRUE;
				}
			}
			include tpl("step2");
		}else{
			exit($lang['install_disagree_license']);
		}
		break;
	case 3:
		include tpl("step3");
		break;
	case 4:
		$data = $_POST;
		if(empty($data['db_host']) || empty($data['db_username']) || empty($data['db_name'])){
			$msg .= '<p>'.$lang['mysql_invalid_configure'].'<p>';
			$quit = TRUE;
		}elseif(!@mysql_connect($data['db_host'], $data['db_username'], $data['db_password'])){
			$msg .= '<p>'.$lang['database_errno_'.mysql_errno()].'</p>';
			$quit = TRUE;
		}
		if(strlen($data['password']) < 6){
			$msg .= '<p>'.$lang['founder_invalid_password'].'</p>';
			$quit = TRUE;
		}elseif($data['password'] != $data['rpassword']){
			$msg .= '<p>'.$lang['founder_invalid_rpassword'].'</p>';
			$quit = TRUE;
		}else{
			$forbiddencharacter = array ("\\","&"," ","'","\"","/","*",",","<",">","\r","\t","\n","#","$","(",")","%","@","+","?",";","^");
			foreach($forbiddencharacter as $value){
				if(strpos($data['username'], $value) !== FALSE){
					$msg .= '<p>'.$lang['forbidden_character'].'</p>';
					$quit = TRUE;
					break;
				}
			}
		}
		if($quit){
			$allownext = 'disabled="disabled"';
			echo $msg;
		}else{
			$config_file_content	=	array();
			$config_file_content['db_host']			 =	$data['db_host'];
			$config_file_content['db_name']			 =	$data['db_name'];
			$config_file_content['db_username']		 =	$data['db_username'];
			$config_file_content['db_password']		 =	$data['db_password'];
			$config_file_content['db_pconnect']		 =	0;
			$config_file_content['db_charset']		 =	'utf8';
			$config_file_content['dbType']			 =	'MySQL';
			
			$default_manager_account	             =	array();
			$default_manager_account['founder_name'] =	$data['founder_name'];
			$default_manager_account['password']	 =	md5($data['password']);

			$_SESSION['config_file_content']		 =	$config_file_content;
			$_SESSION['default_manager_account']	 =	$default_manager_account;
			$_SESSION['site_url']					 =	$data['site_url'];
		}
		//写配置文件
		$fp = fopen(DWERP_ROOT.$dwerp_config_file, 'wb');
		$configfilecontent = <<<EOT
<?php
if(!defined('THINK_PATH')) exit();
return array(
	'DB_TYPE'		=>	'mysql',// 数据库类型	
	'DB_HOST'		=>	'$data[db_host]',// 数据库服务器地址
	'DB_NAME'		=>	'$data[db_name]',// 数据库名称
	'DB_USER'		=>	'$data[db_username]',// 数据库用户名
	'DB_PWD'		=>	'$data[db_password]',// 数据库密码
	'DB_CHARSET'	=>	'utf8',// 网站编码
	'DB_PORT'		=>	'3306',// 数据库端口
	'APP_DEBUG'     =>  false,// 开启调试模式
	'DWERP_URL'     =>  '$data[site_url]',// 网站地址
	
	//网站系统设置
	'SITE_NAME'			=>  '$data[site_name]',
	'SITE_KEYWORDS'		=>  '$data[site_keywords]',
	'SITE_DESCRIPTION'	=>  '$data[site_description]',
	'OFFLINEMESSAGE'	=>	'本站正在维护中，暂不能访问。<br /> 请稍后再访问本站。',
	'ICP_NUM'			=>	'$data[icp_num]',
	'DWERP_VERSION'		=>	'1.0'
);
?>
EOT;
?>
<?php
	chmod(DWERP_ROOT.$dwerp_config_file, 0777);
		$result_1	=	fwrite($fp, trim($configfilecontent));
		@fclose($fp);
		include tpl("step4");
		break;
	case 5:
		$db_config	=	$_SESSION['config_file_content'];
		if(!$db_config['db_host'] && !$db_config['db_name']){
			$msg .= '<p>'.$lang['configure_read_failed'].'</p>';
			$quit = TRUE;
		}else{
			mysql_connect($db_config['db_host'], $db_config['db_username'], $db_config['db_password']);
			$sqlv = mysql_get_server_info();
			if($sqlv < '4.1'){
				$msg .= '<p>'.$lang['mysql_version_402'].'</p>';
				$quit = TRUE;
			}else{
				$db_charset	=	$db_config['db_charset'];
				$db_charset = (strpos($db_charset, '-') === FALSE) ? $db_charset : str_replace('-', '', $db_charset);
				mysql_query(" CREATE DATABASE IF NOT EXISTS `{$db_config['db_name']}` DEFAULT CHARACTER SET $db_charset ");
				if(mysql_errno()){
					$errormsg = $lang['database_errno_'.mysql_errno()];
					$msg .= '<p>'.($errormsg ? $errormsg : $lang['database_errno']).'</p>';
					$quit = TRUE;
				}else{
					mysql_select_db($db_config['db_name']);
				}
				//判断是否有用同样的数据库前缀安装过
				$re		=	mysql_query("SELECT COUNT(1) FROM user");
				$link	=	@mysql_fetch_row($re);
				if(intval($link[0]) > 0){
					$DWERP_rebuild	=	true;
					$msg .= $lang['dwerp_rebuild'];
					$alert = 'onclick="confirm(\''.$lang['dwerp_rebuild'].'\');window.location.href=\'index.php?step=6\'"';
				}else{
                	$alert = 'onclick="window.location.href=\'index.php?step=6\'"';
                }
			}
		}
		if($quit){
			$allownext = 'disabled="disabled"';
			echo $msg;
		}else{
			if($DWERP_rebuild){}
		}
		include tpl("step5");
		break;
	case 6:
		$db_config	=	$_SESSION['config_file_content'];
		$site_url   =   $_SESSION['site_url'];
		mysql_connect($db_config['db_host'], $db_config['db_username'], $db_config['db_password']);
		if(mysql_get_server_info() > '5.0'){
			mysql_query("SET sql_mode = ''");
		}
		$db_config['db_charset'] = (strpos($db_config['db_charset'], '-') === FALSE) ? $db_config['db_charset'] : str_replace('-', '', 		$db_config['db_charset']);
		mysql_query("SET character_set_connection={$db_config['db_charset']}, character_set_results={$db_config['db_charset']}, character_set_client=binary");
		mysql_select_db($db_config['db_name']);
		$tablenum = 0;
		$fp = fopen($database, 'rb');
		$sql = fread($fp, filesize($database));
		fclose($fp);
		$db_charset	=	$db_config['db_charset'];
		$db_prefix	=	$db_config['db_prefix'];
		
		$sql = str_replace("\r", "\n", str_replace('`'.'xp_', '`'.$db_prefix, str_replace('{siteurl}',$site_url,$sql)));
		$ret = array();
		$num = 0;
		foreach(explode(";\n", trim($sql)) as $query){
			$queries = explode("\n", trim($query));
			$sq = "";
			foreach($queries as $query){
				$sq .= $query;
			}
			$ret[$num] = $sq;
			$num ++;
		}
		unset($sql);
		clear();
		include tpl("step6");
		break;
	default:
		include tpl("index");
} 
?>
