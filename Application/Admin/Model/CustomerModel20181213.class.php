<?php


namespace Admin\Model;

use \Think\Model;

/**
 * 客户管理
 */
class CustomerModel extends Model {

	/**
	 * 获取客户列表
	 */
	public function get_lists($data, $page = 0, $row = 0){
		$return['lists'] = array();
		$return['count'] = 0;
		$model = M('customer');
		$users_id = $data['uid'];
		//获取显示列字段
		$show_data = array();
		$show_data[] = array('fields_text'=>'name','title'=>'客户名称','sort'=>0);
		$show_data[] = array('fields_text'=>'mobile','title'=>'手机号','sort'=>0);
		$users_fields_data = M('users_fields')->field('fields_text,sort')->where(array('users_id'=>$users_id,'status'=>1))->order('sort asc')->select();
		if($users_fields_data){
			$show_data = array_merge($show_data,$users_fields_data);
		}
		$fields_data = M('fields')->field('id,type')->where(array('status'=>1,'is_open'=>1))->select();
		$fields_ids = array_column($fields_data,'id');
		foreach ($show_data as $key => $value) {
			if(is_numeric($value['fields_text']) && !in_array($value['fields_text'],$fields_ids)){
				unset($show_data[$key]);
			}
		}
		$fields_type_data = array_column($fields_data,'type','id');
		//获取对应数据
		$map = array();
		if($data['is_my'] == 1){
			//我自己的客户
			$map['c.person_id'] = $users_id;
		}
		//文本筛选
		if($data['search'] && $data['search_title']){
			if(is_numeric($data['search'])){
				//自定义文本
				$map['cf.fields_id'] = $data['search'];
				$map['cf.content'] = array('like','%'.$data['search_title'].'%');
			}else{
				//默认文本
				switch($data['search']){
					case 'users_id':
					case 'person_id':
						$users_ids = M('users')->where(array('name'=>['like','%'.$data['search_title'].'%']))->getField('id',true);
						if(empty($users_ids)){
							return $return;
						}
						$map['c.'.$data['search']] = ['in',$users_ids];
						break;
					default :
						$map['c.'.$data['search']] = array('like','%'.$data['search_title'].'%');
						break;
				}
			}
		}
		//排序
		$order = 'c.create_time desc';
		//单选多选筛选
		$default_data = $this->get_default_fields_config();
		$default_data = array_column($default_data,'fields_text');
		$where = '';
		$customer_ids = array();
		foreach ($data as $key => $value) {
			if(empty($value)){
				continue;
			}
			if(is_numeric($key)){
				//自定义字段
				$customer_fields_map['fields_id'] = $key;
				if($fields_type_data[$key] == 4){
					//多选
					$value = '_'.$value.'_';
					$customer_fields_map['content'] = ['like','%'.$value.'%'];
				}else{
					$customer_fields_map['content'] = $value;
				}
				$one_customer_ids = M('customer_fields')->where($customer_fields_map)->getField('customer_id',true);
				if(empty($one_customer_ids)){
					return $return;
				}
				if(empty($customer_ids)){
					$customer_ids = $one_customer_ids;
				}else{
					$customer_ids = array_intersect($customer_ids,$one_customer_ids);
					if(empty($customer_ids)){
						return $return;
					}
				}
			}elseif(in_array($key,$default_data)){
				switch($key){
					case 'create_time':
					case 'next_follow_time':
					case 'update_time':
						if($value != 7){
							$map['c.'.$key] = get_time_type($value);
						}else{
							$map['c.'.$key] = handle_special_time($data['time_'.$key]);
						}
						break;
					default :
						$map['c.'.$key] = $value;
						break;
				}
			}elseif($key == 'order'){
				$order_value = explode(' ',$value);
				//排序
				if(is_numeric($order_value[0])){
					//自定义
					$where = ' and cfs.fields_id = '.$order_value[0];
					$order = 'cfs.content '.$order_value[1];
				}else{
					//默认
					switch($order_value[0]){
						case 'no_follow_day':
							if($order_value[1] == 'asc'){
								$order_value[1] = 'desc';
							}else{
								$order_value[1] = 'asc';
							}
							$order = 'max(f.create_time) '.$order_value[1];
							break;
						default :
							$order = 'c.'.$value;
							break;
					}
				}
			}
		}
		if($customer_ids){
			$map['c.id'] = ['in',$customer_ids];
		}
		$map['c.status'] = $data['status'];
		$model->alias('c')
			->join('left join sys_customer_fields as cf ON cf.customer_id = c.id')
			->join('left join sys_customer_fields as cfs ON cfs.customer_id = c.id'.$where)
			->join('left join sys_follow as f ON f.customer_id = c.id')
			->field('c.id')
			->where($map)
			->group('c.id');
		$sql = $model->buildSql();
		$sql = " select count(*) as count  from {$sql} as temp";
		$count = $model->query($sql);
		$count = end($count)['count'];
		$fields = 'c.id,c.name,c.mobile,c.type,c.follow_status,c.next_follow_time,c.users_id,c.person_id,c.create_time,c.update_time,max(f.create_time) as follow_time';
		$lists_data = $model->alias('c')
				->join('left join sys_customer_fields as cf ON cf.customer_id = c.id')
				->join('left join sys_customer_fields as cfs ON cfs.customer_id = c.id'.$where)
				->join('left join sys_follow as f ON f.customer_id = c.id')
				->field($fields)
				->where($map)
				->group('c.id')
				->order($order)
				->page($page,$row)
				->select();
		$lists = array();
		if($lists_data){
			$ids = array_column($lists_data,'id');
			//字段所有内容
			$cf_lists = M('customer_fields')->field('customer_id,fields_id,content')->where(array('customer_id'=>['in',$ids]))->select();
			$cf_data = array();
			foreach ($cf_lists as $k => $v) {
				$cf_data[$v['customer_id']][$v['fields_id']] = $v['content'];
			}
			//客户类型配置
			$customer_type_data = D('Setting')->get_customer_type_config();
			$customer_type_data = array_column($customer_type_data,'title','id');
			//跟进状态配置
			$follow_status_data = D('Setting')->get_follow_status_config();
			$follow_status_data = array_column($follow_status_data,'title','id');
			//用户配置
			$users_data = M('users')->field('id,name')->select();
			$users_data = array_column($users_data,'name','id');
			//
			$fields_type_data = M('fields')->field('id,type')->where(array('status'=>1,'is_open'=>1))->select();
			$fields_type_data = array_column($fields_type_data,'type','id');
			foreach ($lists_data as $key => $value) {
				$one_data = array();
				foreach ($show_data as $k => $v) {
					if(is_numeric($v['fields_text'])){
						//自定义字段
						$one_one_data = $cf_data[$value['id']][$v['fields_text']];
						switch($fields_type_data[$v['fields_text']]){
							case 4://多选
								$one_one_data = array_filter(explode('_',$one_one_data));
								$one_one_data = implode('、',$one_one_data);
								break;
						}
					}else{
						//默认字段
						$one_one_data = $value[$v['fields_text']];
						switch($v['fields_text']){
							case 'type':
								$one_one_data = $customer_type_data[$one_one_data];
								break;
							case 'follow_status':
								$one_one_data = $follow_status_data[$one_one_data];
								break;
							case 'users_id':
							case 'person_id':
								$one_one_data = $users_data[$one_one_data];
								break;
							case 'next_follow_time':
							case 'create_time':
							case 'update_time':
								$one_one_data = date('Y-m-d H:i',$one_one_data);
								break;
							case 'no_follow_day':
								//最新跟进时间
								$one_one_data = intval((time()-$value['follow_time']) / 86400);
								break;
						}
					}
					if($one_one_data || $one_one_data === 0){
						$one_data[] = $one_one_data;
					}else{
						$one_data[] = '-';
					}
				}
				$lists[] = array('id'=>$value['id'],'data'=>$one_data);
			}
		}

		$return['lists'] = $lists;
		$return['count'] = $count;
		return $return;
	}

	/**
	 * 获取客户筛选信息
	 */
	public function get_screen($data){
		$users_id = $data['users_id'];
		//获取显示列字段
		$show_data = array();
		$show_data[] = array('fields_text'=>'name','title'=>'客户名称','sort'=>0);
		$show_data[] = array('fields_text'=>'mobile','title'=>'手机号','sort'=>0);
		$users_fields_data = M('users_fields')->field('fields_text,sort')->where(array('users_id'=>$users_id,'status'=>1))->order('sort asc')->select();
		if($users_fields_data){
			//默认字段
			$default_data = $this->get_default_fields_config();
			$default_data = array_column($default_data,'title','fields_text');
			//自定义字段
			$fields_data = M('fields')->where(array('status'=>1,'is_open'=>1))->field('id,title')->select();
			$fields_data = array_column($fields_data,'title','id');
			foreach ($users_fields_data as $key => $value) {
				if(is_numeric($value['fields_text'])){
					//自定义字段
					$one_data = $fields_data[$value['fields_text']];
					if(empty($one_data)){
						continue;
					}
					$value['title'] = $one_data;
				}else{
					//默认字段
					$value['title'] = $default_data[$value['fields_text']];
				}
				$show_data[] = $value;
			}
		}
		//时间
		$time_ids = array();
		//获取筛选字段
		$screen_data = array();
		$screen_data['text'][] = array('fields_text'=>'name','title'=>'客户名称');
		$screen_data['text'][] = array('fields_text'=>'mobile','title'=>'手机号','sort'=>0);
		$screen_fields_data = M('screen_fields')->field('fields_text,sort')->where(array('users_id'=>$users_id,'status'=>1))->order('sort asc')->select();
		if($screen_fields_data){
			//默认字段
			$default_data = $this->get_default_fields_config();
			$default_data = array_column($default_data,'title','fields_text');
			//自定义字段
			$fields_data = M('fields')->field('id,title,type,extra')->where(array('status'=>1,'is_open'=>1))->select();
			$fields_data = array_column($fields_data,null,'id');
			foreach ($screen_fields_data as $key => $value) {
				if(is_numeric($value['fields_text'])){
					//自定义字段
					$one_data = $fields_data[$value['fields_text']];
					if(empty($one_data)){
						continue;
					}
					if($one_data['type'] == 3){
						//单选
						$value['type'] = 1;
						$value['title'] = $one_data['title'];
						$value['extra'] = json_decode($one_data['extra'],true);
						$screen_data['choice'][] = $value;
					}elseif($one_data['type'] == 4){
						//多选
						$value['type'] = 2;
						$value['title'] = $one_data['title'];
						$value['extra'] = json_decode($one_data['extra'],true);
						$screen_data['choice'][] = $value;
					}else{
						//文本
						$value['title'] = $one_data['title'];
						$screen_data['text'][] = $value;
					}
				}else{
					//默认字段
					switch($value['fields_text']){
						case 'type':
							$value['type'] = 1;
							$value['title'] = $default_data[$value['fields_text']];
							$value['extra'] = D('Setting')->get_customer_type_config();
							$screen_data['choice'][] = $value;
							break;
						case 'follow_status':
							$value['type'] = 1;
							$value['title'] = $default_data[$value['fields_text']];
							$value['extra'] = D('Setting')->get_follow_status_config();
							$screen_data['choice'][] = $value;
							break;
						case 'create_time'://时间
						case 'update_time':
						case 'next_follow_time':
							$value['type'] = 1;
							$value['title'] = $default_data[$value['fields_text']];
							$value['extra'] = D('Setting')->get_time_config();
							$screen_data['choice'][] = $value;
							$time_ids[] = $value['fields_text'];
							break;
						default :
							$value['title'] = $default_data[$value['fields_text']];
							$screen_data['text'][] = $value;
							break;
					}
				}
			}
		}
		$return['show_data'] = $show_data;
		$return['screen_data'] = $screen_data;
		$return['time_ids'] = json_encode($time_ids);
		return $return;
	}

	/**
	 * 获取客户详情数据
	 */
	public function get_info($data){
		$info = array();
		if(empty($data['id'])){
			return $info;
		}
		$model = M('customer');
		$map['id'] = $data['id'];
		$info = $model->field('id,name,mobile,type,follow_status,next_follow_time,person_id,remark')->where($map)->find();
		if($info){
			$info['next_follow_time'] = date('Y-m-d H:i:s',$info['next_follow_time']);
			//字段数据
			$cf_data = M('customer_fields')->field('fields_id,content')->where(array('customer_id'=>$data['id']))->order('id desc')->select();
			$cf_data = array_column($cf_data,'content','fields_id');
			if($cf_data){
				$fields_data = M('fields')->field('id,type')->where(array('status'=>1,'is_open'=>1))->select();
				$fields_data = array_column($fields_data,'type','id');
				foreach ($cf_data as $key => $value) {
					if($fields_data[$key] == 4){
						//多选
						$value = explode('_',$value);
						$value = array_filter($value);
						$cf_data[$key] = $value;
					}elseif($fields_data[$key] == 6){
						//图片
						$value = json_decode($value,true);
//						$value = getFileUrl($value,'image');
						$cf_data[$key] = $value;
					}
				}
				$info = $info + $cf_data;
			}
			//附件
			$sha1 = M('customer_file')->where(array('customer_id'=>$info['id']))->getField('sha1',true);
			if($sha1){
				$file_data = M('file')->field('name,sha1')->where(array('sha1'=>['in',$sha1]))->select();
			}
			$info['file_data'] = $file_data;
		}
		return $info;
	}

	/**
	 * 详情展示文本
	 */
	public function get_info_text($data){
		$info = array();
		if(empty($data['id'])){
			return $info;
		}
		for($i=1;$i <= 4;$i++){
			$lists[$i]['type'] = $i;
			$lists[$i]['data'] = array();
			switch($i){
				case 1:
					$title = '基本信息';
					break;
				case 2:
					$title = '跟进信息';
					break;
				case 3:
					$title = '联系方式';
					break;
				case 4:
					$title = '其他信息';
					break;
			}
			$lists[$i]['title'] = $title;
		}
		$model = M('customer');
		$map['id'] = $data['id'];
		$info = $model->field('id,name,mobile,type,follow_status,next_follow_time,users_id,person_id,remark,create_time')->where($map)->find();
		if($info){
			//用户配置
			$users_data = M('users')->field('id,name')->select();
			$users_data = array_column($users_data,'name','id');
			//基本信息
			$lists[1]['data'][] = array('title'=>'客户名称','content'=>$info['name']);
			//跟进信息
			$follow_status_text = D('Setting')->getOneConfig('get_follow_status_config',$info['follow_status']);
			$lists[2]['data'][] = array('title'=>'跟进状态','content'=>$follow_status_text);
			$lists[2]['data'][] = array('title'=>'负责人','content'=>$users_data[$info['person_id']]);
			$lists[2]['data'][] = array('title'=>'下次跟进时间','content'=>date('Y-m-d H:i:s',$info['next_follow_time']));
			//联系方式
			$lists[3]['data'][] = array('title'=>'手机号','content'=>$info['mobile']);
			//其他信息
			$type_text = D('Setting')->getOneConfig('get_customer_type_config',$info['type']);
			$lists[4]['data'][] = array('title'=>'客户类型','content'=>$type_text);
			$lists[4]['data'][] = array('title'=>'创建人','content'=>$users_data[$info['users_id']]);
			$lists[4]['data'][] = array('title'=>'创建时间','content'=>date('Y-m-d H:i:s',$info['create_time']));
			if($info['remark']){
				$lists[4]['data'][] = array('title'=>'备注信息','content'=>$info['remark']);
			}
			//字段数据
			$cf_data = M('customer_fields')->field('fields_id,content')->where(array('customer_id'=>$data['id']))->order('id desc')->select();
			$cf_data = array_column($cf_data,'content','fields_id');
			//字段名称
			$fields_data = M('fields')->field('id,title,category_id,type')->where(array('status'=>1,'is_open'=>1))->order('sort asc,id desc')->select();
			foreach ($fields_data as $key => $value) {
				$one_data = $cf_data[$value['id']];
				if(empty($one_data)){
					continue;
				}else{
					$one_content = $cf_data[$value['id']];
					if($value['type'] == 4){
						//多选
						$one_content = array_filter(explode('_',$one_content));
						$one_content = implode('、',$one_content);
					}elseif($value['type'] == 6){
						//图片
						$value['other_type'] = 'image';
						$one_content = json_decode($one_content,true);
						$one_content = getFileUrl($one_content,'image');
					}
					$value['content'] = $one_content;
					$lists[$value['category_id']]['data'][] = $value;
				}
			}
			//附件
			$sha1 = M('customer_file')->where(array('customer_id'=>$data['id']))->getField('sha1',true);
			if($sha1){
				$file_data = M('file')->field('id,name,sha1 as url')->where(array('sha1'=>['in',$sha1]))->order('id desc')->select();
				foreach ($file_data as $key => $value) {
					$value['url'] = getFileUrl($value['url'],'file');
					$file_data[$key] = $value;
				}
				$lists[4]['data'][] = array('title'=>'附件','content'=>$file_data,'other_type'=>'file');
			}

		}
		$info['lists'] = $lists;
		return $info;
	}

	/**
	 * 客户转移
	 */
	public function edit_customer_transfer($data){
		$data = set_trim($data);
		if(empty($data['customer_id']) || empty($data['users_id'])){
			return ['msg'=>'数据不存在','status'=>400];
		}elseif(empty($data['person_id'])){
			return ['msg'=>'请选择新负责人','status'=>400];
		}
		$data['customer_id'] = explode('_',$data['customer_id']);
		$model = M('customer');
		$model->startTrans();
		foreach ($data['customer_id'] as $key => $value) {
			$customer_id = $value;
			//判断
			$customer_data = $model->field('id,person_id')->where(array('id'=>$customer_id))->find();
			if(empty($customer_data)){
				$model->rollback();
				return ['msg'=>'数据不存在','status'=>400];
			}
			if($data['person_id'] == $customer_data['person_id']){
				$model->rollback();
				continue;
				//return ['msg'=>'请选择新负责人','status'=>400];
			}
			$res = $model->where(array('id'=>$customer_id))->save(array('person_id'=>$data['person_id']));
			if($res === false){
				$model->rollback();
				return ['msg'=>'转移失败1','status'=>400];
			}
			$users_name = M('users')->where(array('id'=>$data['users_id']))->getField('name');
			$person_name = M('users')->where(array('id'=>$data['person_id']))->getField('name');
			//增加日志
			$log_data = array();
			$log_data['users_id'] = $data['users_id'];
			$log_data['customer_id'] = $customer_id;
			$log_data['type'] = 4;
			$log_data['content'] = $users_name.'将客户转移给'.$person_name;
			$res = $this->add_customer_logs($log_data);
			if($res['status'] != 200){
				$model->rollback();
				return ['msg'=>'转移失败2','status'=>400];
			}
		}
		$model->commit();
		return ['msg'=>'转移成功','status'=>200];
	}

	/**
	 * 客户动态日志
	 */
	public function get_customer_logs($data){
		$lists = array();
		if(empty($data['customer_id'])){
			return $lists;
		}
		$model = M('customer_logs');
		$map['customer_id'] = $data['customer_id'];
		$lists = $model->field('id,type,content,create_time,users_name')->where($map)->order('create_time desc,id desc')->select();
		if($lists){
			foreach ($lists as $key => $value) {
				$value['create_time'] = date('Y-m-d H:i:s',$value['create_time']);
				if(in_array($value['type'],[1,2])){
					$value['content'] = json_decode($value['content'],true);
				}
				switch($value['type']){
					case 1:
						$title = $value['content']['follow_type'];
						break;
					case 2:
						$title = '编辑客户';
						break;
					case 3:
						$title = '新增客户';
						break;
					case 4:
						$title = '客户转移';
						break;
					case 5:
						$title = '掉入公海';
						break;
					case 6:
						$title = '捕捞';
						break;
					case 7:
						$title = '抢到客户';
						break;
				}
				$value['type_text'] = $title;
				$lists[$key] = $value;
			}
		}
		return $lists;
	}

	/**
	 * 增加客户动态日志
	 */
	public function add_customer_logs($data){
		$data = set_trim($data);
		if(empty($data['customer_id']) || empty($data['users_id']) || empty($data['type']) || !in_array($data['type'],[1,2,3,4,5])){
			return ['msg'=>'数据不存在','status'=>400];
		}elseif(empty($data['content'])){
			return ['msg'=>'数据不能为空','status'=>400];
		}
		$customer_id = $data['customer_id'];
		$check = M('customer')->field('id')->where(array('id'=>$customer_id))->find();
		if(empty($check)){
			return ['msg'=>'数据不存在','status'=>400];
		}
		$add_data = array();
		$add_data['users_id'] = $data['users_id'];
		$add_data['users_name'] = M('users')->where(array('id'=>$data['users_id']))->getField('name');
		$add_data['customer_id'] = $customer_id;
		$add_data['type'] = $data['type'];
		$add_data['create_time'] = NOW_TIME;
		if(in_array($data['type'],[1,2])){
			$add_data['content'] = json_encode($data['content']);
		}else{
			$add_data['content'] = $data['content'];
		}
		$res = M('customer_logs')->add($add_data);
		if($res === false){
			return ['msg'=>'增加日志失败','status'=>400];
		}
		return ['msg'=>'增加日志成功','status'=>200];
	}

	/**
	 * 新增、编辑客户
	 */
	public function edit($data){
		$data = set_trim($data);
		//判断
		if(empty($data['users_id'])){
			return ['msg'=>'数据不存在','status'=>400];
		}elseif(empty($data['name'])){
			return ['msg'=>'客户名称不能为空','status'=>400];
		}elseif(empty($data['mobile'])){
			return ['msg'=>'手机号不能为空','status'=>400];
		}elseif(empty($data['type'])){
			return ['msg'=>'客户类型不能为空','status'=>400];
		}elseif(empty($data['follow_status'])){
			return ['msg'=>'跟进状态不能为空','status'=>400];
		}elseif(empty($data['next_follow_time'])){
			return ['msg'=>'下次跟进时间不能为空','status'=>400];
		}elseif(empty($data['person_id'])){
			return ['msg'=>'负责人不能为空','status'=>400];
		}elseif(empty($data['remark'])){
			return ['msg'=>'备注信息不能为空','status'=>400];
		}elseif(mb_strlen($data['remark'],'utf-8') > 200){
			return ['msg'=>'备注信息不能超过200个字符','status'=>400];
		}
		//字段数据判断
		$new_fields_data = array();
		$fields_data = $data['fields_data'];
		$fields_lists = M('fields')->field('id,type,title,is_must')->where(array('status'=>1,'is_open'=>1))->order('sort asc,id desc')->select();
		if($fields_lists){
			foreach ($fields_lists as $key => $value) {
				$one_fields_data = $fields_data[$value['id']];
				if($value['is_must'] == 1 && empty($one_fields_data)){
					return ['msg'=>'信息填写不完整','status'=>400];
				}
				if($one_fields_data){
					$new_fields_data[$value['id']] = $one_fields_data;
				}
			}
		}
		$model = M('customer');
		//开启事务
		$model->startTrans();
		$edit_data = array();
		$edit_data['name'] = $data['name'];
		$edit_data['mobile'] = $data['mobile'];
		$edit_data['type'] = $data['type'];
		$edit_data['follow_status'] = $data['follow_status'];
		$edit_data['next_follow_time'] = strtotime($data['next_follow_time']);
		$edit_data['person_id'] = $data['person_id'];
		$edit_data['remark'] = $data['remark'];
		$edit_data['update_time'] = NOW_TIME;
		$edit_data['users_id'] = $data['users_id'];
		if($data['id']){
			//编辑
			$msg = '保存';
			$customer_data = $model->field('id,name,mobile,type,follow_status,next_follow_time,person_id,remark')->where(array('id'=>$data['id']))->find();
			if(empty($customer_data)){
				return ['msg'=>'数据不存在','status'=>400];
			}
			$check = $model->field('id')->where(array('id'=>['neq',$data['id']],'mobile'=>$data['mobile']))->find();
			if($check){
				return ['msg'=>'该手机号已存在，请仔细核实客户信息','status'=>400];
			}
			//判断是否有修改
			$log_content = array();
			$default_data = $this->get_default_fields_config();
			$default_data = array_column($default_data,'title','fields_text');
			unset($customer_data['id']);
			foreach ($customer_data as $key => $value) {
				$new_value = $edit_data[$key];
				if($value != $new_value){
					switch($key){
						case 'name':
							$title = '客户名称';
							break;
						case 'remark':
							$title = '备注信息';
							break;
						case 'person_id':
							$title = $default_data[$key];
							$value = M('users')->where(array('id'=>$value))->getField('name');
							$new_value = M('users')->where(array('id'=>$new_value))->getField('name');
							break;
						case 'type':
							$title = $default_data[$key];
							$value = D('Setting')->getOneConfig('get_customer_type_config',$value);
							$new_value = D('Setting')->getOneConfig('get_customer_type_config',$new_value);
							break;
						case 'follow_status':
							$title = $default_data[$key];
							$value = D('Setting')->getOneConfig('get_follow_status_config',$value);
							$new_value = D('Setting')->getOneConfig('get_follow_status_config',$new_value);
							break;
						default :
							$title = $default_data[$key];
							break;
					}
					$log_content[] = array('fields_text'=>$key,'title'=>$title,'content'=>$value.'→'.$new_value);
				}
			}
			$old_fields_data = M('customer_fields')->field('fields_id,content')->where(array('customer_id'=>$data['id']))->select();
			$old_fields_data = array_column($old_fields_data,'content','fields_id');
			foreach ($fields_lists as $key => $value) {
				if($value['type'] == 6){
					continue;
				}
				$new_one_data = $fields_data[$value['id']];
				$old_one_data = $old_fields_data[$value['id']];
				if($value['type'] == 4){
					//多选
					$old_one_data = explode('_',$old_one_data);
					$old_one_data = array_filter($old_one_data);
					if($new_one_data != $old_one_data){
						$old_one_data = implode('、',$old_one_data);
						$new_one_data = implode('、',$new_one_data);
					}else{
						continue;
					}
				}
				if($old_one_data && $new_one_data && $new_one_data != $old_one_data){
					$log_content[] = array('fields_text'=>$value['id'],'title'=>$value['title'],'content'=>$old_one_data.'→'.$new_one_data);
				}elseif(empty($new_one_data) && $old_one_data){
					$log_content[] = array('fields_text'=>$value['id'],'title'=>$value['title'],'content'=>$old_one_data.'→'.$new_one_data);
				}elseif(empty($old_one_data) && $new_one_data){
					$log_content[] = array('fields_text'=>$value['id'],'title'=>$value['title'],'content'=>$old_one_data.'→'.$new_one_data);
				}
			}
			$customer_id = $data['id'];
//			$old_file_data = M('customer_file')->where(array('customer_id'=>$customer_id))->getField('sha1',true);
//			$old_file_data = $old_file_data ? $old_file_data : array();
//			sort($old_file_data);
//			$new_file_data = $data['file_data'];
//			$new_file_data = $new_file_data ? $new_file_data : array();
//			sort($new_file_data);
//			if($old_file_data == $new_file_data && empty($log_content)){
//				return ['msg'=>'数据没做任何修改','status'=>400];
//			}
			//保存客户信息
			$edit_data['id'] = $customer_id;
			$res = $model->save($edit_data);
			if($res === false){
				$model->rollback();
				return ['msg'=>$msg.'失败','status'=>400];
			}
			//删除关联的文件数据
			$res = M('customer_fields')->where(array('customer_id'=>$customer_id))->delete();
			if($res === false){
				$model->rollback();
				return ['msg'=>$msg.'失败','status'=>400];
			}
			//删除附件
			$res = M('customer_file')->where(array('customer_id'=>$customer_id))->delete();
			if($res === false){
				$model->rollback();
				return ['msg'=>$msg.'失败','status'=>400];
			}
			if($log_content){
				//增加日志
				$log_data = array();
				$log_data['users_id'] = $data['users_id'];
				$log_data['customer_id'] = $customer_id;
				$log_data['type'] = 2;
				$log_data['content'] = $log_content;
				$res = $this->add_customer_logs($log_data);
				if($res['status'] != 200){
					$model->rollback();
					return ['msg'=>$res['msg'],'status'=>400];
				}
			}
		}else{
			//新增
			$msg = '新增';
			$check = $model->field('id')->where(array('mobile'=>$data['mobile']))->find();
			if($check){
				return ['msg'=>'该手机号已存在，请仔细核实客户信息','status'=>400];
			}
			$edit_data['create_time'] = NOW_TIME;
			$customer_id = $model->add($edit_data);
			if($customer_id === false){
				$model->rollback();
				return ['msg'=>$msg.'失败','status'=>400];
			}

			//增加日志
			$log_data = array();
			$log_data['users_id'] = $data['users_id'];
			$log_data['customer_id'] = $customer_id;
			$log_data['type'] = 3;
			$log_data['content'] = '新增客户';
			$res = $this->add_customer_logs($log_data);
			if($res['status'] != 200){
				$model->rollback();
				return ['msg'=>'保存失败','status'=>400];
			}
			//增加跟进记录
			$add_follow = array();
			$add_follow['customer_id'] = $customer_id;
			$add_follow['users_id'] = $data['users_id'];
			$add_follow['type'] = 0;
			$add_follow['follow_time'] = NOW_TIME;
			$add_follow['content'] = $data['users_id'];
			$add_follow['status'] = $data['follow_status'];
			$add_follow['next_follow_time'] = $data['next_follow_time'];
			$add_follow['create_time'] = NOW_TIME;
			$add_follow['other_type'] = 1;
			$res = M('follow')->add($add_follow);
			if($res === false){
				$model->rollback();
				return ['msg'=>'保存失败','status'=>400];
			}
		}
		//保存字段数据
		if($new_fields_data){
			$add_new_fields_data = array();
			$fields_lists = array_column($fields_lists,'type','id');
			foreach ($new_fields_data as $key => $value) {
				$one_type = $fields_lists[$key];
				if($one_type == 6){
					//图片
					$value = json_encode($value);
				}elseif($one_type == 4){
					//多选
					$value = '_'.implode('_',$value).'_';
				}
				$add_new_fields_data[] = array('customer_id'=>$customer_id,'fields_id'=>$key,'content'=>$value);
			}
			$res = M('customer_fields')->addAll($add_new_fields_data);
			if($res === false){
				$model->rollback();
				return ['msg'=>$msg.'失败','status'=>400];
			}
		}
		//保存附件
		if($data['file_data']){
			$add_file_data = array();
			foreach ($data['file_data'] as $key => $value) {
				$add_file_data[] = array('customer_id'=>$customer_id,'sha1'=>$value);
			}
			if($add_file_data){
				$res = M('customer_file')->addAll($add_file_data);
				if($res === false){
					$model->rollback();
					return ['msg'=>$msg.'失败','status'=>400];
				}
			}
		}
		$model->commit();
		return ['msg'=>$msg.'成功','status'=>200];
	}

	/**
	 * 获取客户默认字段
	 */
	public function get_default_fields_config(){
		$data = array(
			array('fields_text'=>'type','title'=>'客户类型','sort'=>0),
			array('fields_text'=>'follow_status','title'=>'跟进状态','sort'=>0),
			array('fields_text'=>'next_follow_time','title'=>'下次跟进时间','sort'=>0),
			array('fields_text'=>'users_id','title'=>'创建人','sort'=>0),
			array('fields_text'=>'person_id','title'=>'负责人','sort'=>0),
			array('fields_text'=>'create_time','title'=>'创建时间','sort'=>0),
			array('fields_text'=>'update_time','title'=>'更新时间','sort'=>0),
			array('fields_text'=>'no_follow_day','title'=>'未跟进天数','sort'=>0)
		);
		return $data;
	}

	/**
	 * 列表字段的显示
	 */
	public function get_fields_show($data){
		$lists = array();
		if(empty($data['users_id'])){
			return $lists;
		}
		$fields_data = M('fields')->field('id as fields_text,sort,title')->where(array('status'=>1,'is_open'=>1))->order('sort asc,id desc')->select();
		$lists = M('users_fields')->field('fields_text,sort,status')->where(array('users_id'=>$data['users_id']))->order('sort asc')->select();
		//默认排序
		$default_data = $this->get_default_fields_config();
		if(empty($lists)){
			$lists = $default_data;
			if($fields_data){
				$lists = array_merge($lists,$fields_data);
			}
		}else{
			//默认的字段
			$default_data = array_column($default_data,null,'fields_text');
			//用户已重新勾选过排序
			$fields_data = array_column($fields_data,null,'fields_text');
			foreach ($lists as $key => $value) {
				$one_fields_data = $fields_data[$value['fields_text']];
				if(is_numeric($value['fields_text'])){
					//是数字-自定义的字段
					if(empty($one_fields_data)){
						unset($lists[$key]);
					}else{
						$lists[$key]['title'] = $one_fields_data['title'];
						unset($fields_data[$value['fields_text']]);
					}
				}else{
					$lists[$key]['title'] = $default_data[$value['fields_text']]['title'];
					unset($default_data[$value['fields_text']]);
				}
			}
			if($default_data){
				$lists = array_merge($lists,$default_data);
			}
			if($fields_data){
				$lists = array_merge($lists,$fields_data);
			}
		}
		return $lists;
	}

	/**
	 * 编辑自定义显示列
	 */
	public function edit_fields_show($data){
		$data = set_trim($data);
		if(empty($data['users_id'])){
			return ['msg'=>'数据不存在','status'=>400];
		}elseif(empty($data['sorts']) || !is_array($data['sorts'])){
			return ['msg'=>'数据不存在','status'=>400];
		}
		$users_id = $data['users_id'];
		$choices = $data['choices'];
		$model = M('users_fields');
		$model->startTrans();
		$add_data = array();
		$one_data = array();
		$one_data['users_id'] = $users_id;
		$sort = 1;
		foreach ($data['sorts'] as $key => $value) {
			$one_data['fields_text'] = $value;
			$one_data['status'] = 2;
			if(in_array($value,$choices)){
				$one_data['status'] = 1;
			}
			$one_data['sort'] = $sort;
			$sort++;
			$add_data[] = $one_data;
		}
		//先删除数据
		$res = $model->where(array('users_id'=>$users_id))->delete();
		if($res === false){
			$model->rollback();
			return ['msg'=>'保存失败','status'=>400];
		}
		//再保存新的数据
		$res = $model->addAll($add_data);
		if($res === false){
			$model->rollback();
			return ['msg'=>'保存失败','status'=>400];
		}
		$model->commit();
		return ['msg'=>'保存成功','status'=>200];
	}

	/**
	 * 列表字段的筛选
	 */
	public function get_fields_screen($data){
		$lists = array();
		if(empty($data['users_id'])){
			return $lists;
		}
		$fields_data = M('fields')->field('id as fields_text,sort,title')->where(array('status'=>1,'is_open'=>1))->order('sort asc,id desc')->select();
		$lists = M('screen_fields')->field('fields_text,sort,status')->where(array('users_id'=>$data['users_id']))->order('sort asc')->select();
		//默认排序
		$default_data = $this->get_default_fields_config();
		if(empty($lists)){
			$lists = $default_data;
			if($fields_data){
				$lists = array_merge($lists,$fields_data);
			}
		}else{
			//默认的字段
			$default_data = array_column($default_data,null,'fields_text');
			//用户已重新勾选过排序
			$fields_data = array_column($fields_data,null,'fields_text');
			foreach ($lists as $key => $value) {
				if(is_numeric($value['fields_text'])){
					$one_fields_data = $fields_data[$value['fields_text']];
					//是数字-自定义的字段
					if(empty($one_fields_data)){
						unset($lists[$key]);
					}else{
						$lists[$key]['title'] = $one_fields_data['title'];
						unset($fields_data[$value['fields_text']]);
					}
				}else{
					$lists[$key]['title'] = $default_data[$value['fields_text']]['title'];
					unset($default_data[$value['fields_text']]);
				}
			}
			if($default_data){
				$lists = array_merge($lists,$default_data);
			}
			if($fields_data){
				$lists = array_merge($lists,$fields_data);
			}
		}
		return $lists;
	}

	/**
	 * 编辑自定义筛选字段
	 */
	public function edit_fields_screen($data){
		$data = set_trim($data);
		if(empty($data['users_id'])){
			return ['msg'=>'数据不存在','status'=>400];
		}elseif(empty($data['sorts']) || !is_array($data['sorts'])){
			return ['msg'=>'数据不存在','status'=>400];
		}
		$users_id = $data['users_id'];
		$choices = $data['choices'];
		$model = M('screen_fields');
		$model->startTrans();
		$add_data = array();
		$one_data = array();
		$one_data['users_id'] = $users_id;
		$sort = 1;
		foreach ($data['sorts'] as $key => $value) {
			$one_data['fields_text'] = $value;
			$one_data['status'] = 2;
			if(in_array($value,$choices)){
				$one_data['status'] = 1;
			}
			$one_data['sort'] = $sort;
			$sort++;
			$add_data[] = $one_data;
		}
		//先删除数据
		$res = $model->where(array('users_id'=>$users_id))->delete();
		if($res === false){
			$model->rollback();
			return ['msg'=>'保存失败','status'=>400];
		}
		//再保存新的数据
		$res = $model->addAll($add_data);
		if($res === false){
			$model->rollback();
			return ['msg'=>'保存失败','status'=>400];
		}
		$model->commit();
		return ['msg'=>'保存成功','status'=>200];
	}

	/**
	 * 列表字段的编辑
	 */
	public function edit_field_show($data){
		$data = set_trim($data);
		//判断
		if(empty($data['users_id'])){
			return ['msg'=>'数据不存在','status'=>400];
		}
		$users_id = $data['users_id'];
		$model = M('users_fields');
		$model->startTrans();
		$res = $model->where(array('users_id'=>$users_id))->delete();
		if($res === false){
			$model->rollback();
			return ['msg'=>'保存失败','status'=>400];
		}
		$fields_data = $data['fields_data'];
		$add_data = array();
		foreach ($fields_data as $key => $value) {
			$add_data[] = array('users_id'=>$users_id,'fields_text'=>$value['fields_text'],'sort'=>$value['sort'],'status'=>$value['status']);
		}
		if($add_data){
			$res = $model->addAll($add_data);
			if($res === false){
				$model->rollback();
				return ['msg'=>'保存失败','status'=>400];
			}
		}
		$model->commit();
		return ['msg'=>'保存成功','status'=>200];
	}

	/**
	 * 新增跟进
	 */
	public function add_follow($data){
		$data = set_trim($data);
		//判断
		if(empty($data['users_id']) || empty($data['customer_id'])){
			return ['msg'=>'数据不存在','status'=>400];
		}elseif(empty($data['type'])){
			return ['msg'=>'跟进方式不能为空','status'=>400];
		}elseif(empty($data['follow_time'])){
			return ['msg'=>'跟进时间不能为空','status'=>400];
		}elseif(empty($data['content'])){
			return ['msg'=>'跟进内容不能为空','status'=>400];
		}elseif(mb_strlen($data['content'],'utf-8') > 200){
			return ['msg'=>'跟进内容不能超过200个字符','status'=>400];
		}elseif($data['pics'] && count($data['pics']) > 9){
			return ['msg'=>'图片最多9张','status'=>400];
		}elseif(empty($data['status'])){
			return ['msg'=>'跟进状态不能为空','status'=>400];
		}elseif(empty($data['next_follow_time'])){
			return ['msg'=>'下次跟进时间不能为空','status'=>400];
		}
		$model = M('follow');
		//开启事务
		$model->startTrans();
		//判断
		$customer_data = M('customer')->field('id,follow_status,next_follow_time')->where(array('id'=>$data['customer_id']))->find();
		if(empty($customer_data)){
			return ['msg'=>'数据不存在','status'=>400];
		}
		$add_data['customer_id'] = $data['customer_id'];
		$add_data['users_id'] = $data['users_id'];
		$add_data['type'] = $data['type'];
		$add_data['follow_time'] = strtotime($data['follow_time']);
		$add_data['content'] = $data['content'];
		$add_data['status'] = $data['status'];
		$add_data['next_follow_time'] = strtotime($data['next_follow_time']);
		$add_data['create_time'] = NOW_TIME;
		$follow_id = $model->add($add_data);
		if($follow_id === false){
			$model->rollback();
			return ['msg'=>'保存失败','status'=>400];
		}
		//跟进图片
		$add_follow = array();
		$pics = $data['pics'];
		foreach ($pics as $key => $value) {
			$add_follow[] = array('follow_id'=>$follow_id,'pic'=>$value);
		}
		if($add_follow){
			$res = M('follow_pic')->addAll($add_follow);
			if($res === false){
				$model->rollback();
				return ['msg'=>'保存失败','status'=>400];
			}
		}
		$save_customer_data = array();
		//修改客户跟进状态
		if($customer_data['follow_status'] != $data['status']){
			$save_customer_data['follow_status'] = $data['status'];
		}
		if($add_data['next_follow_time'] != $customer_data['next_follow_time']){
			$save_customer_data['next_follow_time'] = $add_data['next_follow_time'];
		}
		if($save_customer_data){
			$res = M('customer')->where(array('id'=>$data['customer_id']))->save($save_customer_data);
			if($res === false){
				$model->rollback();
				return ['msg'=>'保存失败','status'=>400];
			}
		}
		//增加日志
		//跟进用户
		$follow_user = M('users')->where(array('id'=>$data['users_id']))->getField('name');
		$follow_type = D('Setting')->get_follow_type_config();
		//跟进方式
		$follow_type = array_column($follow_type,'title','id');
		$follow_type = $follow_type[$data['type']];
		$log_data = array();
		$log_data['users_id'] = $data['users_id'];
		$log_data['customer_id'] = $data['customer_id'];
		$log_data['type'] = 1;
		$log_data_content = array();
		$log_data_content['follow_id'] = $follow_id;
		$log_data_content['content'] = $data['content'];
		$log_data_content['follow_time'] = $data['follow_time'];
		$log_data_content['follow_user'] = $follow_user;
		$log_data_content['follow_type'] = $follow_type;
		if($pics){
			$log_data_content['follow_pic'] = $pics;
		}
		$log_data['content'] = $log_data_content;
		$res = $this->add_customer_logs($log_data);
		if($res['status'] != 200){
			$model->rollback();
			return ['msg'=>'保存失败','status'=>400];
		}
		$model->commit();
		return ['msg'=>'保存成功','status'=>200];
	}


	/**
	 * 批量抢单
	 */
	public function grab($data){
		$data = set_trim($data);
		//判断
		if(empty($data['users_id']) || empty($data['id'])){
			return ['msg'=>'数据不存在','status'=>400];
		}
		$model = M('customer');
		//开启事务
		$model->startTrans();
		$info = $model->field('id,status')->where(array('id'=>$data['id']))->find();
		if(empty($info)){
			return ['msg'=>'数据不存在','status'=>400];
		}elseif($info['status'] == 1){
			return ['msg'=>'该客户已被其他用户抢到','status'=>400];
		}
		$save_data['status'] = 1;
		$save_data['person_id'] = $data['users_id'];
		$check = $model->where(array('id'=>$info['id'],'status'=>2))->save($save_data);
		if(empty($check)){
			return ['msg'=>'该客户已被其他用户抢到','status'=>400];
		}
		//增加日志
		$log_data = array();
		$log_data['users_id'] = $data['users_id'];
		$log_data['customer_id'] = $info['id'];
		$log_data['type'] = 7;
		$log_data['content'] = '';
		$res = $this->add_customer_logs($log_data);
		if($res['status'] != 200){
			$model->rollback();
			return ['msg'=>'抢单失败','status'=>400];
		}
		$model->commit();
		return ['msg'=>'抢单成功','status'=>400];
	}

	/**
	 * 批量抢单（暂时不用到）
	 */
	public function batch_grab($data){
		$data = set_trim($data);
		//判断
		if(empty($data['users_id']) || empty($data['id'])){
			return ['msg'=>'数据不存在','status'=>400];
		}
		$ids = explode('_',$data['id']);
		$count = count($ids);
		$error_count = 0;//失败数量
		$success_count = 0;//成功数量
		$model = M('customer');
		//开启事务
		$model->startTrans();
		$msg = '';
		foreach ($ids as $value) {
			$info = $model->field('id,status')->where(array('id'=>$value))->find();
			if(empty($info)){
				$msg = '数据不存在';
				$error_count++;
				continue;
			}elseif($info['status'] == 1){
				$msg = '该客户已被其他用户抢到';
				$error_count++;
				continue;
			}
			$save_data['status'] = 1;
			$save_data['person_id'] = $data['users_id'];
			$save_data['waters_id'] = 0;
			$check = $model->where(array('id'=>$info['id'],'status'=>2))->save($save_data);
			if(empty($check)){
				$msg = '该客户已被其他用户抢到';
				$error_count++;
				continue;
			}
			//增加日志
			$log_data = array();
			$log_data['users_id'] = $data['users_id'];
			$log_data['customer_id'] = $info['id'];
			$log_data['type'] = 7;
			$log_data['content'] = '';
			$res = $this->add_customer_logs($log_data);
			if($res['status'] != 200){
				$model->rollback();
				return ['msg'=>'抢单失败','status'=>400];
			}
			$success_count++;
		}
		$model->commit();
		if($error_count == 0){
			//全部抢单成功
			return ['msg'=>'抢单成功','status'=>200];
		}elseif($success_count == 0 && $count == 1){
			//只有一个抢单，并成功
			return ['msg'=>$msg,'status'=>400];
		}elseif($success_count == 0){
			//全部抢单失败
			return ['msg'=>'抢单失败','status'=>400];
		}else{
			//有部分抢单成功，有部分抢单失败
			return ['msg'=>'抢单成功<br/>'.$success_count.'个抢单成功<br/>'.$error_count.'个抢单失败','status'=>200];
		}
	}

}
