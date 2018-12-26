<?php

// +----------------------------------------------------------------------
// |[ 科技以人为本 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.qixibird.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: Everyday <303529990@qq.com> <http://www.qixibird.com>
// | Date : 2016-05-24
// +----------------------------------------------------------------------

namespace Admin\Model;

/**
 * 系统设置
 */
class SettingModel {

	/**
	 * 业务字段列表
	 */
	public function get_fields_lists($data = array(), $page = 0, $row = 0){
		$model = M('fields');
		$fields = 'id,type,category_id,title,sort,is_open,is_must';
		$map['status'] = 1;
		$lists = $model->field($fields)->where($map)->page($page,$row)->order('sort asc,id desc')->select();
		$is_data = array(1 => '是', 2 => '否');
		$type_data = $this->get_fields_type_config();
		$type_data = array_column($type_data,'title','id');
		$category_data = $this->get_fields_category();
		$category_data = array_column($category_data,'title','id');
		foreach ($lists as $key => $value) {
			$value['type_text'] = $type_data[$value['type']];
			$value['category_text'] = $category_data[$value['category_id']];
			$value['is_open'] = $is_data[$value['is_open']];
			$value['is_must'] = $is_data[$value['is_must']];
			$lists[$key] = $value;
		}
		$count = $model->where($map)->count();

		$return['lists'] = $lists;
		$return['count'] = $count;
		return $return;
	}

	/**
	 * 获取字段详情
	 */
	public function get_fields_info($data){
		$info = array();
		if(empty($data['id'])){
			return $info;
		}
		$model = M('fields');
		$map['id'] = $data['id'];
		$map['status'] = 1;
		$info = $model->field('id,type,category_id,title,tips,is_open,is_must,sort,extra')->where($map)->find();
		if($info['extra']){
			$info['extra'] = json_decode($info['extra'],true);
		}
		return $info;
	}

	/**
	 * 新增、编辑字段
	 */
	public function edit_fields($data){
		$data = set_trim($data);
		//判断
		if(empty($data['type'])){
			return ['msg'=>'字段类型不能为空','status'=>400];
		}elseif(empty($data['category_id'])){
			return ['msg'=>'字段分类不能为空','status'=>400];
		}elseif(empty($data['title'])){
			return ['msg'=>'字段名称不能为空','status'=>400];
		}elseif(mb_strlen($data['title'],'utf-8') > 20){
			return ['msg'=>'字段名称不能超过20个字符','status'=>400];
		}elseif(empty($data['tips'])){
			return ['msg'=>'提示语不能为空','status'=>400];
		}elseif(mb_strlen($data['tips'],'utf-8') > 20){
			return ['msg'=>'提示语不能超过20个字符','status'=>400];
		}elseif(empty($data['is_open']) || !in_array($data['is_open'],[1,2])){
			return ['msg'=>'请选择是否启用','status'=>400];
		}elseif(empty($data['is_must']) || !in_array($data['is_must'],[1,2])){
			return ['msg'=>'请选择是否必填','status'=>400];
		}elseif($data['sort'] && !is_numeric($data['sort'])){
			return ['msg'=>'排序序号必须为数字','status'=>400];
		}elseif($data['sort'] && strlen($data['sort']) > 4){
			return ['msg'=>'排序序号不能超过4个数字','status'=>400];
		}
		if($data['type'] != 3 && $data['type'] != 4){
			$extra_data = '';
		}else{
			$extra_data = array();
			foreach ($data['extra_data']['title'] as $key => $value) {
				if(empty($value)){
					continue;
				}
				$sort = $data['extra_data']['sort'][$key];
				$sort = $sort ? $sort : 0;
				$extra_data[] = array('title'=>$value,'sort'=>$sort);
			}
			if(empty($extra_data)){
				return ['msg'=>'请增加选择项','status'=>400];
			}
			//二维数字排序
			$sorts = array_column($extra_data,'sort');
			array_multisort($sorts,SORT_ASC,$extra_data);
			//数组转json
			$extra_data = json_encode($extra_data);
		}
		$model = M('fields');
		$edit_data['type'] = $data['type'];
		$edit_data['category_id'] = $data['category_id'];
		$edit_data['title'] = $data['title'];
		$edit_data['tips'] = $data['tips'];
		$edit_data['sort'] = $data['sort'];
		$edit_data['is_open'] = $data['is_open'];
		$edit_data['is_must'] = $data['is_must'];
		$edit_data['extra'] = $extra_data;
		if($data['id']){
			//编辑
			//判断
			$check = $model->field('id')->where(array('id'=>$data['id'],'status'=>1))->find();
			if(empty($check)){
				return ['msg'=>'数据不存在','status'=>400];
			}
			$check = $model->field('id')->where(array('id'=>['neq',$data['id']],'title'=>$data['title'],'status'=>1))->find();
			if($check){
				return ['msg'=>'已有该信息，请勿重复添加','status'=>400];
			}
			//保存数据
			$edit_data['id'] = $data['id'];
			$res = $model->save($edit_data);
			if($res === false){
				return ['msg'=>'保存失败','status'=>400];
			}
		}else{
			//新增
			//判断
			$check = $model->field('id')->where(array('title'=>$data['title'],'status'=>1))->find();
			if($check){
				return ['msg'=>'已有该信息，请勿重复添加','status'=>400];
			}
			//新增数据
			$edit_data['create_time'] = NOW_TIME;
			$res = $model->add($edit_data);
			if($res === false){
				return ['msg'=>'保存失败','status'=>400];
			}
		}
		return ['msg'=>'保存成功','status'=>200];
	}

	/**
	 * 删除业务字段(假删除)
	 */
	public function del_fields($data){
		//判断
		if(empty($data['id'])){
			return ['msg'=>'数据不存在','status'=>400];
		}
		$model = M('fields');
		$map = array();
		$map['id'] = $data['id'];
		$map['status'] = 1;
		$check = $model->field('id')->where($map)->find();
		if(empty($check)){
			return ['msg'=>'数据不存在','status'=>400];
		}
		$map['status'] = 2;
		$res = $model->save($map);
		if($res === false){
			return ['msg'=>'删除失败','status'=>400];
		}
		return ['msg'=>'删除成功','status'=>200];
	}

	/**
	 * 编辑公海
	 */
	public function edit_waters($data){
		$data = set_trim($data);
		if(empty($data['type']) || !in_array($data['type'],[1,2])){
			return ['msg'=>'请选择规则类型','status'=>400];
		}
		if($data['type'] == 1){
			//统一规则
			if(empty($data['time'][0]) || !is_numeric($data['time'][0])){
				return ['msg'=>'请输入正确的数字','status'=>400];
			}
			unset($data['time'][1],$data['time'][2],$data['time'][3],$data['time'][4],$data['time'][5]);
		}else{
			if(empty($data['time'][1]) || empty($data['time'][2]) || empty($data['time'][3]) || empty($data['time'][4]) || empty($data['time'][5])){
				return ['msg'=>'请输入正确的数字','status'=>400];
			}elseif(!is_numeric($data['time'][1]) || !is_numeric($data['time'][2]) || !is_numeric($data['time'][3]) || !is_numeric($data['time'][4]) || !is_numeric($data['time'][5])){
				return ['msg'=>'请输入正确的数字','status'=>400];
			}
			unset($data['time'][0]);
		}
		$name = 'water_rule';
		$model = M('setting');
		$info = $model->field('id,value')->where(array('name'=>$name))->find();
		$content = array();
		$content['type'] = $data['type'];
		$content['time'] = $data['time'];
		$content = json_encode($content);
		if(empty($info)){
			//新增
			$add_data['name'] = $name;
			$add_data['value'] = $content;
			$add_data['create_time'] = NOW_TIME;
			$res = $model->add($add_data);
		}else{
			//编辑
			$res = $model->where(array('id'=>$info['id']))->save(array('value'=>$content));
		}
		if($res === false){
			return ['msg'=>'保存失败','status'=>400];
		}
		return ['msg'=>'保存成功','status'=>200];
	}

	/**
	 * 获取公海设置
	 */
	public function get_waters(){
		$name = 'water_rule';
		$model = M('setting');
		$info = $model->where(array('name'=>$name))->getField('value');
		if($info){
			$info = json_decode($info,true);
		}
		return $info;
	}

	/**
	 * 字段类型配置
	 */
	public function get_fields_type_config(){
		$data = array(
			array('id' => 1, 'title' => '单行文本'),
			array('id' => 2, 'title' => '多行文本'),
			array('id' => 3, 'title' => '单选下拉列表'),
			array('id' => 4, 'title' => '多选下拉列表'),
			array('id' => 5, 'title' => '数字'),
			array('id' => 6, 'title' => '图片'),
			array('id' => 7, 'title' => '时间')
		);
		return $data;
	}

	/**
	 * 跟进方式配置
	 */
	public function get_follow_type_config(){
		$data = array(
			array('id' => 1, 'title' => '电话'),
			array('id' => 2, 'title' => 'QQ'),
			array('id' => 3, 'title' => '微信'),
			array('id' => 4, 'title' => '拜访'),
			array('id' => 5, 'title' => '邮件'),
			array('id' => 6, 'title' => '短信'),
			array('id' => 7, 'title' => '其他')
		);
		return $data;
	}

	/**
	 * 跟进状态配置
	 */
	public function get_follow_status_config(){
		$data = array(
			array('id' => 1, 'title' => '初访'),
			array('id' => 2, 'title' => '意向'),
			array('id' => 3, 'title' => '报价'),
			array('id' => 4, 'title' => '成交'),
			array('id' => 5, 'title' => '暂时搁置'),
			array('id' => 6, 'title' => '撤单'),
			array('id' => 7, 'title' => '未成交')
		);
		return $data;
	}

	/**
	 * 客户类型配置
	 */
	public function get_customer_type_config(){
		$data = array(
			array('id' => 1, 'title' => 'A类'),
			array('id' => 2, 'title' => 'B类'),
			array('id' => 3, 'title' => 'C类'),
			array('id' => 4, 'title' => 'D类'),
			array('id' => 5, 'title' => '无效')
		);
		return $data;
	}

	/**
	 * 时间配置
	 */
	public function get_time_config(){
		$data = array(
			array('id' => 1, 'title' => '今日'),
			array('id' => 2, 'title' => '昨日'),
			array('id' => 3, 'title' => '本周'),
			array('id' => 4, 'title' => '上周'),
			array('id' => 5, 'title' => '本月'),
			array('id' => 6, 'title' => '上月'),
			array('id' => 7, 'title' => '自定义时间段','other_type'=>'time')
		);
		return $data;
	}

	/**
	 * 获取负责人
	 */
	public function get_person(){
		$data = M('users')->field('id,name as title')->where(array('type'=>2,'status'=>1))->select();
		return $data;
	}

	/**
	 * 字段分类
	 */
	public function get_fields_category(){
		$data = array(
			array('id' => 1, 'title' => '基本信息'),
			array('id' => 2, 'title' => '跟进信息'),
			array('id' => 3, 'title' => '联系方式'),
			array('id' => 4, 'title' => '其他信息')
		);
		return $data;
	}

	/**
	 * 获取配置单项
	 */
	public function getOneConfig($method, $ids = [], $ext = ',') {
		if (!is_array($ids)) {
			$ids = explode(',', $ids);
		}
		$data = $this->$method();
		$title = [];
		foreach ($data as $key => $value) {
			if (in_array($value['id'], $ids)) {
				$title[] = $value['title'];
			}
		}
		$title = implode($ext, $title);
		return $title;
	}

}
