<?php
// 文章模块
class ArticleAction extends CommonAction {
	//赋值文章可用分类
	public function _before_index() {
		$model	=	M("Category");
		$list	=	$model->where('status=1 AND module="Article" AND pid!=0')->select();
		$this->assign('types',$list);
	}
	//赋值文章可用分类
	public function _before_add() {
		$model	=	M("Category");
		$list	=	$model->where('status=1 AND module="Article" AND pid!=0')->select();
		$this->assign('list',$list);
	}
	//赋值文章可用分类
	public function _before_edit() {
		$model	=	M("Category");
		$list	=	$model->where('status=1 AND module="Article" AND pid!=0')->select();
		$this->assign('list',$list);
	}
	//添加文章
	public function insert() {
		$model = D ('Article');
		if (false === $model->create ()) {
			$this->error ( $model->getError () );
		}
		if(!empty($_FILES['img']['name'])){
			import("ORG.Net.UploadFile");
			$upload = new UploadFile();
			$upload->maxSize  = 3145728 ; 
			$upload->allowExts  = array('jpg', 'gif', 'png', 'jpeg'); 
			$upload->savePath =  './Public/Upload/article/';
			$upload->saveRule = 'uniqid';
			$upload->thumb = true;
			$upload->thumbMaxWidth = 100;
			$upload->thumbMaxHeight = 100;
			$upload->uploadReplace = true;
			$upload->thumbPrefix = '';
			if(!$upload->upload()) { 
				$this->error($upload->getErrorMsg());
			}else{
				$imgs = $upload->getUploadFileInfo(); 
				$model->img = $imgs[0]['savename'];
			}
		}
		//保存当前数据对象
		$list=$model->add ();
		if ($list!==false) {
			//如果有填写Rewrite值,在Router表插入一条记录
			if($_POST['rewrite']){
				$data['rewrite']=$_POST['rewrite'];
				$data['url']='article/view/id/'.$model->getLastInsID();
				D('Router')->add($data);
			}
			echo '<script type="text/javascript">
					var response = {
						"statusCode":"1",
						"message":"\u64cd\u4f5c\u6210\u529f",
						"navTabId":"Article",
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
	//xheditor上传文件保存
	public function upload() {
		header('Content-Type: text/html; charset=UTF-8');
		$inputname='filedata';//表单文件域name
		$attachdir='./Public/Upload/article';//上传文件保存路径，结尾不要带/
		$dirtype=1;//1:按天存入目录 2:按月存入目录 3:按扩展名存目录  建议使用按天存
		$maxattachsize=2097152;//最大上传大小，默认是2M
		$upext='txt,rar,zip,jpg,jpeg,gif,png,swf,wmv,avi,wma,mp3,mid';//上传扩展名
		$msgtype=2;//返回上传参数的格式：1，只返回url，2，返回参数数组
		$immediate=isset($_GET['immediate'])?$_GET['immediate']:0;//立即上传模式，仅为演示用
		ini_set('date.timezone','Asia/Shanghai');//时区
			
		if(isset($_SERVER['HTTP_CONTENT_DISPOSITION']))//HTML5上传
		{
			if(preg_match('/attachment;\s+name="(.+?)";\s+filename="(.+?)"/i',$_SERVER['HTTP_CONTENT_DISPOSITION'],$info))
			{
				$temp_name=ini_get("upload_tmp_dir").'\\'.date("YmdHis").mt_rand(1000,9999).'.tmp';
				file_put_contents($temp_name,file_get_contents("php://input"));
				$size=filesize($temp_name);
				$_FILES[$info[1]]=array('name'=>$info[2],'tmp_name'=>$temp_name,'size'=>$size,'type'=>'','error'=>0);
			}
		}
		
		$err = "";
		$msg = "''";
		
		$upfile=@$_FILES[$inputname];
		if(!isset($upfile)){
			$err='文件域的name错误';
		}elseif(!empty($upfile['error'])){
			switch($upfile['error'])
			{
				case '1':
					$err = '文件大小超过了php.ini定义的upload_max_filesize值';
					break;
				case '2':
					$err = '文件大小超过了HTML定义的MAX_FILE_SIZE值';
					break;
				case '3':
					$err = '文件上传不完全';
					break;
				case '4':
					$err = '无文件上传';
					break;
				case '6':
					$err = '缺少临时文件夹';
					break;
				case '7':
					$err = '写文件失败';
					break;
				case '8':
					$err = '上传被其它扩展中断';
					break;
				case '999':
				default:
					$err = '无有效错误代码';
			}
		}elseif(empty($upfile['tmp_name']) || $upfile['tmp_name'] == 'none'){
			$err = '无文件上传';
		}else{
			$temppath=$upfile['tmp_name'];
			$fileinfo=pathinfo($upfile['name']);
			$extension=$fileinfo['extension'];
			if(preg_match('/'.str_replace(',','|',$upext).'/i',$extension))
			{
				$bytes=filesize($temppath);
				if($bytes > $maxattachsize)$err='请不要上传大小超过'.$maxattachsize.'的文件';
				else
				{
					switch($dirtype)
					{
						case 1: $attach_subdir = 'day_'.date('ymd'); break;
						case 2: $attach_subdir = 'month_'.date('ym'); break;
						case 3: $attach_subdir = 'ext_'.$extension; break;
					}
					$attach_dir = $attachdir.'/'.$attach_subdir;
					if(!is_dir($attach_dir))
					{
						@mkdir($attach_dir, 0777);
						@fclose(fopen($attach_dir.'/index.htm', 'w'));
					}
					PHP_VERSION < '4.2.0' && mt_srand((double)microtime() * 1000000);
					$filename=date("YmdHis").mt_rand(1000,9999).'.'.$extension;
					$target = $attach_dir.'/'.$filename;
					
					rename($upfile['tmp_name'],$target);
					@chmod($target,0755);
					$target=__ROOT__.'/Public/Upload/article/'.$attach_subdir.'/'.$filename;
					if($immediate=='1')$target='!'.$target;
					if($msgtype==1)$msg="'$target'";
					else $msg="{'url':'".$target."','localname':'".preg_replace("/([\\\\\/'])/",'\\\$1',$upfile['name'])."','id':'1'}";
				}
			}
			else $err='上传文件扩展名必需为：'.$upext;		
			@unlink($temppath);			
		}
		echo "{'err':'".preg_replace("/([\\\\\/'])/",'\\\$1',$err)."','msg':".$msg."}";
	}
	//更新文章
	public function update() {
		$model = D ('Article');
		if (false === $model->create ()) {
			$this->error ( $model->getError () );
		}
		if(!empty($_FILES['img']['name'])){
			import("ORG.Net.UploadFile");
			$upload = new UploadFile();
			$upload->maxSize  = 3145728 ; 
			$upload->allowExts  = array('jpg', 'gif', 'png', 'jpeg'); 
			$upload->savePath =  './Public/Upload/article/';
			$upload->saveRule = 'uniqid';
			$upload->thumb = true;
			$upload->thumbMaxWidth = 100;
			$upload->thumbMaxHeight = 100;
			$upload->uploadReplace = true;
			$upload->thumbPrefix = '';
			if(!$upload->upload()) { 
				$this->error($upload->getErrorMsg());
			}else{
				$imgs = $upload->getUploadFileInfo(); 
				$model->img = $imgs[0]['savename'];
			}
		}
		//保存当前数据对象
		$list=$model->save();
		if ($list!==false) {
			//Rewrite值判断
			D('Router')->where("url='article/view/id/{$_POST['id']}'")->delete();
			if($_POST['rewrite']){
				$data['url']="article/view/id/".$_POST['id'];
				$data['rewrite']=$_POST['rewrite'];
				D('Router')->add($data);
			}
			echo '<script type="text/javascript">
					var response = {
						"statusCode":"1",
						"message":"\u64cd\u4f5c\u6210\u529f",
						"navTabId":"Article",
						"forwardUrl":"",
						"callbackType":"closeCurrent"
					};
					if(window.parent.donecallback) {
						 window.parent.donecallback(response);
					}
			    </script>';
		} else {
			//失败提示
			$this->error ('编辑失败!');
		}
	}
	//删除文章时删除预览图片,删除路由规则
	public function _before_foreverdelete() {
		if($_GET['id']){
			$id = $_GET['id'];
			$rewrite = D('Article')->where('id='.$id)->getField('rewrite');
			D('Router')->where('rewrite="'.$rewrite.'"')->delete();
			$src = './Public/Upload/article/'.D('Article')->where('id='.$id)->getField('img');
			if(is_file($src)){
				unlink($src);
			}
		}
	}
	//删除图片
	public function delimg(){
		if($_GET['id']){
			$id = $_GET['id'];			
			$src = './Public/Upload/article/'.D('Article')->where('id='.$id)->getField('img');
			D('Article')->where('id='.$id)->setField('img','');
			if(is_file($src))unlink($src);
		}
		echo '{
				"statusCode":"1",
				"message":"\u64cd\u4f5c\u6210\u529f",
				"navTabId":"_blank",
				"forwardUrl":"",
				"callbackType":""
			}';
	}
}