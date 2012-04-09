<?php
// 日记模块
class DiaryAction extends CommonAction {
	//查询时对content字段执行like匹配操作
	function _filter(&$map){
		$map['content'] = array('like',"%".$_POST['content']."%");
	}
}