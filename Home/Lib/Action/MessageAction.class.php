<?php
// 留言评论模块
class MessageAction extends CommonAction {
	//查询操作对content字段做like操作
	function _filter(&$map){
		$map['content'] = array('like',"%".$_POST['content']."%");
	}
	//后台回复
	public function insert(){
		$model = D ('Message');
		$data = $_POST;
		$user = D('User')->find($_POST['adder_id']);
		$data['adder_name'] = $user['nickname'];
		$data['adder_email'] = $user['email'];
		if (false === $model->create ($data)) {
			$this->error ( $model->getError () );
		}
		//保存当前数据对象
		$list=$model->add ();
		if ($list!==false) { //保存成功
			$this->assign ( 'jumpUrl', Cookie::get ( '_currentUrl_' ) );
			$this->success ('新增成功!');
		} else {
			//失败提示
			$this->error ('新增失败!');
		}
	}
}