<?php
// 分类模型
class CategoryModel extends CommonModel {
	public function getModule(){
		$modules = array('Article'=>'文章模块','Music'=>'音乐模块','Video'=>'视频模块','Photo'=>'图片模块');
		return $modules;
	}
}