<?php
// 音乐模块
class MusicAction extends CommonAction {
	//赋值音乐可用分类
	public function _before_add() {
		$model	=	M("Category");
		$list	=	$model->where('status=1 AND module="Music" AND pid!=0')->select();
		$this->assign('list',$list);
        $this->assign('files',$this->list_file());
	}
	//赋值音乐可用分类	
	public function _before_edit() {
		$model	=	M("Category");
		$list	=	$model->where('status=1 AND module="Music" AND pid!=0')->select();
		$this->assign('list',$list);
        $this->assign('files',$this->list_file());
	}
	//对音乐地址进行判断
	public function view(){
		if($_GET['id']){
			$info = D('Music')->find($_GET['id']);
			if(!strstr($info['url'],'http')) $info['url']='__PUBLIC__/Upload/music/'.$info['url'];
			$this->assign('info',$info);
			$this->display();
		}
	}
	//删除音乐时同时删除站内音乐
	public function _before_foreverdelete() {
		if($_GET['id']){
			$id = $_GET['id'];			
			$url = D('Music')->where('id='.$id)->getField('url');
			if(!strstr($url,'http')){
				$src = './Public/Upload/music/'.$url;
				if(is_file($src))unlink($src);
			}
		}
	}
	//列出站内音乐文章
	public function list_file(){
		$dir = './Public/Upload/music';
		if ($handle = opendir($dir)) {
			while (false !== ($file = readdir($handle))) {
		        if ($file != "." && $file != "..") {
		        	$files[]=$file;
		        }
		    }
		    return $files;			
		}
	}
}