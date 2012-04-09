<?php
// 视频模块
class VideoAction extends CommonAction {
	//赋值视频可用分类
	public function _before_add() {
		$model	=	M("Category");
		$list	=	$model->where('status=1 AND module="Video" AND pid!=0')->select();
		$this->assign('list',$list);
		$this->assign('files',$this->list_file());
	}
	//赋值视频可用分类
	public function _before_edit() {
		$model	=	M("Category");
		$list	=	$model->where('status=1 AND module="Video" AND pid!=0')->select();
		$this->assign('list',$list);
		$this->assign('files',$this->list_file());
	}
	//添加视频,上传视频图片
	public function insert() {
		$model = D ('Video');
		if (false === $model->create ()) {
			$this->error ( $model->getError () );
		}
		if(!empty($_FILES['img']['name'])){
			import("ORG.Net.UploadFile");
			$upload = new UploadFile();
			$upload->maxSize  = 3145728 ; 
			$upload->allowExts  = array('jpg', 'gif', 'png', 'jpeg'); 
			$upload->savePath =  './Public/Upload/video/';
			$upload->saveRule = 'uniqid';
			$upload->thumb = true;
			$upload->thumbMaxWidth = 128;
			$upload->thumbMaxHeight = 84;
			$upload->uploadReplace = true;
			$upload->thumbPrefix = '';
			if(!$upload->upload()) { 
				$this->error($upload->getErrorMsg());
			}else{
				$imgs = $upload->getUploadFileInfo(); 
				$model->img = $imgs[0]['savename'];
			}
			//保存当前数据对象
			$list=$model->add ();
			if ($list!==false) { //保存成功
				if($_POST['rewrite']){
					$data['rewrite']=$_POST['rewrite'];
					$data['url']='video/view/id/'.$model->getLastInsID();
					D('Router')->add($data);
				}
				echo '<script type="text/javascript">
						var response = {
							"statusCode":"1",
							"message":"\u64cd\u4f5c\u6210\u529f",
							"navTabId":"Video",
							"forwardUrl":"",
							"callbackType":"closeCurrent"
						};
						if(window.parent.donecallback) {
							 window.parent.donecallback(response);
						}
				    </script>';
			} else {
				//失败提示
				$this->error ('新增失败!');
			}
		}
	}
	//更新视频
	public function update(){
		$model = D ( 'Video' );
		if (false === $model->create ()) {
			$this->error ( $model->getError () );
		}
		// 更新数据
		$list=$model->save ();
		if (false !== $list) {
			//成功提示
			D('Router')->where("url='video/view/id/{$_POST['id']}'")->delete();
			if($_POST['rewrite']){
				$data['url']="video/view/id/".$_POST['id'];
				$data['rewrite']=$_POST['rewrite'];
				D('Router')->add($data);
			}
			$this->success ('编辑成功!');
		} else {
			//错误提示
			$this->error ('编辑失败!');
		}
	}
	//删除视频的同时,删除路由规则，删除视频预览图片,如果是本地视频,将视频也删除
	public function _before_foreverdelete() {
		if($_GET['id']){
			$id = $_GET['id'];
			$rewrite = D('Video')->where('id='.$id)->getField('rewrite');
			D('Router')->where('rewrite="'.$rewrite.'"')->delete();
			$src = './Public/Upload/video/'.D('Video')->where('id='.$id)->getField('img');
			if(is_file($src))unlink($src);
			$url = D('Video')->where('id='.$id)->getField('url');
			if(!strstr($url,'http')){
				$src = './Public/Upload/video/'.$url;
				if(is_file($src))unlink($src);
			}
		}
	}
	//查看视频时,根据URL判断是否是本地视频,调用不同播放器
	public function view(){
		if($_GET['id']){
			$info = D('Video')->find($_GET['id']);
			if(!strstr($info['url'],'http'))$this->assign('local',true);
			$this->assign('info',$info);
			$this->display();
		}
	}
	//读取Video目标下所有.flv视频文件
	public function list_file(){
		$dir = './Public/Upload/video';
		if ($handle = opendir($dir)) {
			while (false !== ($file = readdir($handle))) {
		        if ($file != "." && $file != ".." && (strstr($file,'.flv')||strstr($file,'.mp3'))) {
		        	$files[]=$file;
		        }
		    }
		    return $files;			
		}
	}
}