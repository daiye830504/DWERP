<?php
// 后台用户模块
class UserAction extends CommonAction {
	//列出用户时,对ID进行>2限制,对account进行like匹配
	function _filter(&$map){
		$map['role_id'] = array('egt',2);
	}
	
	// 检查帐号
	public function checkAccount() {
        if(!preg_match('/^[a-z]\w{4,}$/i',$_POST['user_id'])) {
            $this->error( '用户名必须是字母，且5位以上！');
        }
		$User = M("User");
        // 检测用户名是否冲突
        $name  =  $_REQUEST['user_id'];
        $result  =  $User->getByAccount($name);
        if($result) {
        	$this->error('该用户名已经存在！');
        }else {
           	$this->success('该用户名可以使用！');
        }
    }
    
	// 插入数据
	public function insert() {
		// 创建数据对象
		$User	 =	 D("User");
		if(!$User->create()) {
			$this->error($User->getError());
		}else{
			// 写入帐号数据
			if($result	 =	 $User->add()) {
				$this->success('用户添加成功！');
			}else{
				$this->error('用户添加失败！');
			}
		}
	}

    //重置密码
    public function resetPwd()
    {
    	$user_id  =  $_POST['user_id'];
        $user_pwd = $_POST['user_pwd'];
        if(''== trim($user_pwd)) {
        	$this->error('密码不能为空！');
        }
        $User = M('User');
		$User->user_pwd	=	md5($user_pwd);
		$User->user_id =	$user_id;
		$result	=	$User->save();
        if(false !== $result) {
            $this->success("密码修改为$user_pwd");
        }else {
        	$this->error('重置密码失败！');
        }
    }   
 
}