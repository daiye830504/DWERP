<?php
//公开类
class PublicAction extends Action {
	// 检查用户是否登录
	protected function checkUser() {
		if(!isset($_SESSION[C('USER_AUTH_KEY')])) {
			$this->assign('jumpUrl','Public/login');
			$this->error('没有登录');
		}
	}

	// 菜单页面
	public function menu() {
        $this->checkUser();
        if(isset($_SESSION[C('USER_AUTH_KEY')])) {
            //显示菜单项
            $menu  = array();
            
        	//读取数据库模块列表生成菜单项
			$node    =   M("Node");
			$id	=	$node->getField("id");
			$where['level']=2;
			$where['status']=1;
			$where['pid']=$id;
			$list	=	$node->where($where)->field('id,name,group_id,title')->order('sort asc')->select();
			$accessList = $_SESSION['_ACCESS_LIST'];
			foreach($list as $key=>$module) {
			     if(isset($accessList[strtoupper(APP_NAME)][strtoupper($module['name'])]) || $_SESSION['administrator']) {
				//设置模块访问权限
				$module['access'] =   1;
				$menu[$key]  = $module;
			    }
			}
              
            if(!empty($_GET['tag'])){
                $this->assign('menuTag',$_GET['tag']);
            }
            $this->assign('menu',$menu);
		}
		C('SHOW_RUN_TIME',false);			// 运行时间显示
		C('SHOW_PAGE_TRACE',false);
		$this->display();
	}

    // 后台首页 查看系统信息
    public function main() {
        $info = array(
            '操作系统'=>PHP_OS,
            '运行环境'=>$_SERVER["SERVER_SOFTWARE"],
            'PHP运行方式'=>php_sapi_name(),
            'ThinkPHP版本'=>THINK_VERSION.' [ <a href="http://thinkphp.cn" target="_blank">查看最新版本</a> ]',
            '执行时间限制'=>ini_get('max_execution_time').'秒',
            '服务器时间'=>date("Y年n月j日 H:i:s"),
            '北京时间'=>gmdate("Y年n月j日 H:i:s",time()+8*3600),
            '服务器域名/IP'=>$_SERVER['SERVER_NAME'].' [ '.gethostbyname($_SERVER['SERVER_NAME']).' ]',
            '剩余空间'=>round((@disk_free_space(".")/(1024*1024)),2).'M',
            'register_globals'=>get_cfg_var("register_globals")=="1" ? "ON" : "OFF",
            'magic_quotes_gpc'=>(1===get_magic_quotes_gpc())?'YES':'NO',
            'magic_quotes_runtime'=>(1===get_magic_quotes_runtime())?'YES':'NO',
            );
        $this->assign('info',$info);
        $this->display();
    }

	// 用户登录页面
	public function login() {
		if(!isset($_SESSION[C('USER_AUTH_KEY')])) {
			$this->display();
		}else{
			$this->redirect('Index/index');
		}
	}

	public function index()
	{
		//如果通过认证跳转到首页
		redirect(__APP__);
	}

	// 用户登出
    public function logout()
    {
        if(isset($_SESSION[C('USER_AUTH_KEY')])) {
			unset($_SESSION[C('USER_AUTH_KEY')]);
			unset($_SESSION);
			session_destroy();
            $this->assign("jumpUrl",__URL__.'/login/');
            $this->success('登出成功！');
        }else {
            $this->error('已经登出！');
        }
    }

	// 登录检测
	public function checkLogin() {
		if(empty($_POST['user_id'])) {
			$this->error('帐号错误！');
		}elseif (empty($_POST['user_pwd'])){
			$this->error('密码必须！');
		}elseif (empty($_POST['verify'])){
			$this->error('验证码必须！');
		}
        //生成认证条件
        $map            =   array();
		// 支持使用绑定帐号登录
		$map['user_id']	= $_POST['user_id'];
		if($_SESSION['verify'] != md5($_POST['verify'])) {
			$this->error('验证码错误！');
		}
		import ( 'ORG.Util.RBAC' );
        $authInfo = RBAC::authenticate($map);
        //使用用户名、密码和状态的方式进行认证
        if(false === $authInfo) {
            $this->error('帐号不存在或已禁用！');
        }else {
            if($authInfo['user_pwd'] != md5($_POST['user_pwd'])) {
            	$this->error('密码错误！');
            }
            $_SESSION[C('USER_AUTH_KEY')]	=	$authInfo['user_id'];
            $_SESSION['loginUserName']		=	$authInfo['user_name'];
            if($authInfo['user_id']=='admin') {
            	$_SESSION['administrator']		=	true;
            }
            //保存登录信息
			//$User	=	M('User');
			//$ip		=	get_client_ip();
			//$time	=	time();
            //$data = array();
			//$data['id']	=	$authInfo['id'];
			//$data['last_login_time']	=	$time;
			//$data['login_count']	=	array('exp','login_count+1');
			//$data['last_login_ip']	=	$ip;
			//$User->save($data);
			// 缓存访问权限
            RBAC::saveAccessList();
			$this->success('登录成功！');
		}
	}
	
    // 更换密码
    public function changePwd()
    {
		$this->checkUser();
        //对表单提交处理进行处理或者增加非表单数据
		if(md5($_POST['verify'])	!= $_SESSION['verify']) {
			$this->error('验证码错误！');
		}
		$map	=	array();
        $map['user_pwd']= pwdHash($_POST['oldpassword']);
        if(isset($_POST['user_id'])) {
            $map['user_id']	 =	 $_POST['account'];
        }elseif(isset($_SESSION[C('USER_AUTH_KEY')])) {
            $map['user_id']		=	$_SESSION[C('USER_AUTH_KEY')];
        }
        //检查用户
        $User    =   M("User");
        if(!$User->where($map)->field('user_id')->find()) {
            $this->error('旧密码不符或者用户名错误！');
        }else {
			$User->user_pwd	=	pwdHash($_POST['user_pwd']);
			$User->save();
			$this->success('密码修改成功！');
         }
    }
	
	//验证码
	public function verify()
    {
		$type = isset($_GET['type'])?$_GET['type']:'gif';
        import("ORG.Util.Image");
        Image::buildImageVerify(4,1,$type,48,20);
    }
}