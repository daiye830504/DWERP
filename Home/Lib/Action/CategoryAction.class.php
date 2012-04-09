<?php
// 分类模块
class CategoryAction extends CommonAction {
	//赋值可用模块
	public function _before_index() {
		$model	=	D("Category");
		$this->assign('module',$model->getModule());
	}
	//赋值可用模块
	public function _before_add() {
		$model	=	D("Category");
		$list	=	$model->where('status=1 AND pid=0')->select();
		$this->assign('list',$list);
		$this->assign('module',$model->getModule());
	}
	//赋值可用模块
	public function _before_edit() {
		$model	=	D("Category");
		$list	=	$model->where('status=1 AND pid=0')->select();
		$this->assign('list',$list);
		$this->assign('module',$model->getModule());
	}
	//添加分类
	public function insert(){
		$model = D ('Category');
		if (false === $model->create ()) {
			$this->error ( $model->getError () );
		}
		//保存当前数据对象
		$list=$model->add ();
		if ($list!==false) { //保存成功
			if($_POST['rewrite']){
				$data['rewrite']=$_POST['rewrite'];
				$data['url']=strtolower($_POST['module']).'/index/id/'.$model->getLastInsID();
				D('Router')->add($data);
			}
			$this->success ('新增成功!');
		} else {
			//失败提示
			$this->error ('新增失败!');
		}
	}
	//编辑分类
	public function update() {
		$model = D ( 'Category' );
		$category = $model->find($_POST['id']);		
		if (false === $model->create ()) {
			$this->error ( $model->getError () );
		}
		// 更新数据
		$list=$model->save ();
		if (false !== $list) {
			//成功提示
			D('Router')->where("url='".strtolower($category['module'])."/index/id/".$_POST['id']."'")->delete();
			if($_POST['rewrite']){
				$data['url']=strtolower($_POST['module'])."/index/id/".$_POST['id'];
				$data['rewrite']=$_POST['rewrite'];
				D('Router')->add($data);
			}
			$this->success ('编辑成功!');
		} else {
			//错误提示
			$this->error ('编辑失败!');
		}
	}	
	//树形结构数据组装
	public function tree(){
		$model = D("Category");
		$list = $model->where('pid=0')->select();
		if($list){
			foreach ($list as $key=>$val){
				$list[$key]['sub_category'] = $model->where('pid='.$val['id'])->select(); 
			}
		}
		$this->assign('list',$list);
		$this->display();
	}
	//删除分类的同时，删除路由规则
	public function _before_foreverdelete() {
		if($_GET['id']){
			$id = $_GET['id'];
			$rewrite = D('Category')->where('id='.$id)->getField('rewrite');
			D('Router')->where('rewrite="'.$rewrite.'"')->delete();
		}
	}
}