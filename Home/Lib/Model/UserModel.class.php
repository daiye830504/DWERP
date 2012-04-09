<?php
// 用户模型
class UserModel extends CommonModel {
	public $_validate	=	array(
		array('user_id','/^[a-z]\w{3,}$/i','用户登录名格式错误'),
		array('user_pwd','require','密码必须'),
		array('user_name','require','用户姓名必须'),
		array('repassword','require','确认密码必须'),
		array('repassword','password','确认密码不一致',self::EXISTS_VAILIDATE,'confirm'),
		array('user_id','','帐号已经存在',self::EXISTS_VAILIDATE,'unique',self::MODEL_INSERT),
		);

	public $_auto		=	array(
		array('user_pwd','pwdHash',self::MODEL_BOTH,'callback'),
		);

	protected function pwdHash() {
		if(isset($_POST['user_pwd'])) {
			return pwdHash($_POST['user_pwd']);
		}else{
			return false;
		}
	}
}