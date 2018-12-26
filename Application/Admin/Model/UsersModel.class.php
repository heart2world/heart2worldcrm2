<?php

namespace Admin\Model;

use \Think\Model;

/**
 * 员工管理
 */
class UsersModel extends Model {

	/**
	 * 用户登录
	 */
	public function login($data){
		$data = set_trim($data);
		if(empty($data['login_name'])){
			return ['msg'=>'账号不能为空','status'=>400];
		}elseif(empty($data['password'])){
			return ['msg'=>'密码不能为空','status'=>400];
		}elseif(empty($data['code'])){
			return ['msg'=>'验证码不能为空','status'=>400];
		}
		$info = M('users')->field('id,password,name,type,role_id,status')->where(array('login_name'=>$data['login_name']))->find();
		if(empty($info)){
			return ['msg'=>'账号不存在','status'=>400];
		}
		if($info['status'] == 2){
			return ['msg'=>'你的账号已被冻结','status'=>400];
		}
		if($info['password'] != think_ucenter_md5($data['password'],UC_AUTH_KEY)){
			return ['msg'=>'密码不正确','status'=>400];
		}
		$verify = new \Think\Verify();
		if($verify->check($data['code']) === false){
			return ['msg'=>'验证码不正确','status'=>400];
		}
		$info['role_text'] = M('role')->where(array('id'=>$info['role_id']))->getField('name');
		unset($info['password']);
		session('user_auth', $info, time() + 3600 * 24);
		return ['msg'=>'登录成功','status'=>200];
	}

	/**
	 * 获取员工列表
	 */
	public function get_users_lists($data,$page = 0,$row = 0){
		$model = M('users');
		$fields = 'id,name,role_id,login_name,mobile,status,type';
		$lists = $model->field($fields)->page($page,$row)->select();
		if($lists){
			$role_ids = array_column($lists,'role_id');
			$role_data = M('role')->field('id,name')->where(array('id'=>['in',$role_ids]))->select();
			$role_data = array_column($role_data,'name','id');
			foreach ($lists as $key => $value) {
				$value['role_text'] = $role_data[$value['role_id']];
				$lists[$key] = $value;
			}
		}
		$count = $model->count();

		$return['lists'] = $lists;
		$return['count'] = $count;
		return $return;
	}

	/**
	 * 获取员工详情
	 */
	public function get_users_info($data){
		$info = array();
		if(empty($data['id'])){
			return $info;
		}
		$model = M('users');
		$map['id'] = $data['id'];
		$info = $model->field('id,name,mobile,login_name,role_id')->where($map)->find();
		return $info;
	}

	/**
	 * 新增、编辑员工
	 */
	public function edit_users($data){
		$data = set_trim($data);
		//判断
		if(empty($data['name'])){
			return ['msg'=>'请完善信息','status'=>400];
		}elseif(mb_strlen($data['name'],'utf-8') > 20){
			return ['msg'=>'角色名称不能超过20个字符','status'=>400];
		}elseif(empty($data['mobile'])){
			return ['msg'=>'请完善信息','status'=>400];
		}elseif(!is_numeric($data['mobile']) || strlen($data['mobile']) != 11){
			return ['msg'=>'请填写正确的手机号','status'=>400];
		}elseif(empty($data['login_name'])){
			return ['msg'=>'请完善信息','status'=>400];
		}elseif(preg_match('/[\x{4e00}-\x{9fa5}]/u', $data['login_name']) === 1){
			return ['msg'=>'登录账号不能有中文','status'=>400];
		}elseif(strlen($data['login_name']) > 20 || strlen($data['login_name']) < 5){
			return ['msg'=>'登录账号必须在5-20个字符之间','status'=>400];
		}elseif(empty($data['role_id'])){
			return ['msg'=>'请完善信息','status'=>400];
		}
		$role_data = M('role')->field('id,type')->where(array('id'=>$data['role_id'],'type'=>2))->find();
		if(empty($role_data)){
			return ['msg'=>'所属角色不存在','status'=>400];
		}
		$model = M('users');
		if($data['id']){
			//保存
			$user_data = $model->field('id,type')->where(array('id'=>$data['id']))->find();
			if(empty($user_data)){
				return ['msg'=>'数据不存在','status'=>400];
			}
			if($user_data['type'] == 1){
				return ['msg'=>'操作有误','status'=>400];
			}
			//判断角色名称是否相同
			$check = $model->field('id')->where(array('id'=>['neq',$data['id']],'login_name'=>$data['login_name']))->find();
			if($check){
				return ['msg'=>'该登录账号已存在','status'=>400];
			}
			$check = $model->field('id')->where(array('id'=>['neq',$data['id']],'mobile'=>$data['mobile']))->find();
			if($check){
				return ['msg'=>'该手机号已存在','status'=>400];
			}
			//保存数据
			$save_data['id'] = $data['id'];
			$save_data['name'] = $data['name'];
			$save_data['mobile'] = $data['mobile'];
			$save_data['login_name'] = $data['login_name'];
			$save_data['role_id'] = $data['role_id'];
			$save_data['update_time'] = NOW_TIME;
			$res = $model->save($save_data);
			if($res === false){
				return ['msg'=>'保存失败','status'=>400];
			}
		}else{
			//新增
			//判断角色名称是否相同
			$check = $model->field('id')->where(array('login_name'=>$data['login_name']))->find();
			if($check){
				return ['msg'=>'该登录账号已存在','status'=>400];
			}
			$check = $model->field('id')->where(array('mobile'=>$data['mobile']))->find();
			if($check){
				return ['msg'=>'该手机号已存在','status'=>400];
			}
			//新增数据
			$add_data['name'] = $data['name'];
			$add_data['mobile'] = $data['mobile'];
			$add_data['login_name'] = $data['login_name'];
			$add_data['role_id'] = $data['role_id'];
			$add_data['password'] = think_ucenter_md5('123456',UC_AUTH_KEY);
			$add_data['create_time'] = NOW_TIME;
			$add_data['update_time'] = NOW_TIME;
			$add_data['login_ip'] = '';
			$res= $model->add($add_data);
			if($res === false){
				return ['msg'=>'保存失败','status'=>400];
			}
		}
		return ['msg'=>'保存成功','status'=>200];
	}

	/**
	 * 修改密码
	 */
	public function edit_password($data){
		$data = set_trim($data);
		//判断
		if(empty($data['id'])){
			return ['msg'=>'数据不存在','status'=>400];
		}elseif(empty($data['password'])){
			return ['msg'=>'请完善信息','status'=>400];
		}elseif(empty($data['repassword'])){
			return ['msg'=>'请完善信息','status'=>400];
		}elseif($data['password'] != $data['repassword']){
			return ['msg'=>'密码不一致，请重新输入','status'=>400];
		}elseif(preg_match('/[\x{4e00}-\x{9fa5}]/u', $data['password']) === 1){
			return ['msg'=>'密码账号不能有中文','status'=>400];
		}elseif(strlen($data['password']) > 20 || strlen($data['password']) < 6){
			return ['msg'=>'密码必须在5-20个字符之间','status'=>400];
		}
		//修改
		$model = M('users');
		$check = $model->field('id')->where(array('id'=>$data['id']))->find();
		if(empty($check)){
			return ['msg'=>'数据不存在','status'=>400];
		}
		$save_data = array();
		$save_data['id'] = $data['id'];
		$save_data['password'] = think_ucenter_md5($data['password'], UC_AUTH_KEY);
		$res = $model->save($save_data);
		if($res === false){
			return ['msg'=>'密码重置失败','status'=>400];
		}
		return ['msg'=>'密码重置成功','status'=>200];
	}

	/**
	 * 冻结、解冻员工
	 */
	public function edit_status($data){
		$data = set_trim($data);
		if(empty($data['id'])){
			return ['msg'=>'数据不存在','status'=>400];
		}
		$model = M('users');
		//判断
		$users_data = $model->field('id,status,type')->where(array('id'=>$data['id']))->find();
		if(empty($users_data)){
			return ['msg'=>'数据不存在','status'=>400];
		}
		if($users_data['type'] == 1){
			return ['msg'=>'操作有误','status'=>400];
		}
		if($users_data['status'] == 1){
			$status = 2;
			$msg = '冻结';
		}else{
			$status = 1;
			$msg = '解冻';
		}
		//删除数据
		$res = $model->where(array('id'=>$data['id']))->save(array('status'=>$status));
		if($res === false){
			return ['msg'=>$msg.'失败','status'=>400];
		}
		return ['msg'=>$msg.'成功','status'=>200];
	}

	/**
	 * 删除员工
	 */
	public function del_users($data){
		$data = set_trim($data);
		if(empty($data['id'])){
			return ['msg'=>'数据不存在','status'=>400];
		}
		$model = M('users');
		//判断
		$users_data = $model->field('id,type')->where(array('id'=>$data['id']))->find();
		if(empty($users_data)){
			return ['msg'=>'数据不存在','status'=>400];
		}
		if($users_data['type'] == 1){
			return ['msg'=>'操作有误','status'=>400];
		}

		//删除数据
		$res = $model->where(array('id'=>$data['id']))->delete();
		if($res === false){
			return ['msg'=>'删除失败','status'=>400];
		}
		return ['msg'=>'删除成功','status'=>200];
	}


	/**
	 * 获取角色管理列表（分页）
	 */
	public function get_role_lists($data,$page = 0,$row = 0){
		$model = M('role');
		$fields = 'id,name,intro,type';
		$lists = $model->field($fields)->page($page,$row)->select();
		$count = $model->count();

		$return['lists'] = $lists;
		$return['count'] = $count;
		return $return;
	}

	/**
	 * 获取角色配置
	 */
	public function get_role_config(){
		$model = M('role');
		$lists = $model->field('id,name')->where(array('type'=>2))->select();
		return $lists;
	}

	/**
	 * 获取角色详情
	 */
	public function get_role_info($data){
		$info = array();
		if(empty($data['id'])){
			return $info;
		}
		$model = M('role');
		$map['id'] = $data['id'];
		$info = $model->field('id,name,intro')->where($map)->find();
		return $info;
	}

	/**
	 * 新增、编辑角色
	 */
	public function edit_role($data){
		$data = set_trim($data);
		//判断
		if(empty($data['name'])){
			return ['msg'=>'请完善信息','status'=>400];
		}elseif(mb_strlen($data['name'],'utf-8') > 20){
			return ['msg'=>'角色名称不能超过20个字符','status'=>400];
		}elseif(empty($data['intro'])){
			return ['msg'=>'请完善信息','status'=>400];
		}elseif(mb_strlen($data['intro'],'utf-8') > 200){
			return ['msg'=>'角色描述不能超过200个字符','status'=>400];
		}
		$model = M('role');
		$model->startTrans();
		if($data['id']){
			//保存
			$role_data = $model->field('id,type')->where(array('id'=>$data['id']))->find();
			if(empty($role_data)){
				return ['msg'=>'数据不存在','status'=>400];
			}
			if($role_data['type'] == 1){
				return ['msg'=>'操作有误','status'=>400];
			}
			//判断角色名称是否相同
			$check = $model->field('id')->where(array('id'=>['neq',$data['id']],'name'=>$data['name']))->find();
			if($check){
				return ['msg'=>'该角色名称已存在','status'=>400];
			}
			//保存数据
			$save_data['id'] = $data['id'];
			$save_data['name'] = $data['name'];
			$save_data['intro'] = $data['intro'];
			$res = $model->save($save_data);
			if($res === false){
				$model->rollback();
				return ['msg'=>'保存失败','status'=>400];
			}
		}else{
			//新增
			//判断角色名称是否相同
			$check = $model->field('id')->where(array('name'=>$data['name']))->find();
			if($check){
				return ['msg'=>'该角色名称已存在','status'=>400];
			}
			//新增数据
			$add_data['name'] = $data['name'];
			$add_data['intro'] = $data['intro'];
			$add_data['create_time'] = NOW_TIME;
			$role_id = $model->add($add_data);
			if($role_id === false){
				$model->rollback();
				return ['msg'=>'保存失败','status'=>400];
			}

			//新增权限
			$rules_id = M('rules')->where(array('mark'=>'customer/my_lists'))->getField('id');
			$res = M('role_rules')->add(array('role_id'=>$role_id,'rules_id'=>$rules_id));
			if($res === false){
				$model->rollback();
				return ['msg'=>'保存失败','status'=>400];
			}
		}
		$model->commit();
		return ['msg'=>'保存成功','status'=>200];
	}

	/**
	 * 删除角色
	 */
	public function del_role($data){
		$data = set_trim($data);
		if(empty($data['id'])){
			return ['msg'=>'数据不存在','status'=>400];
		}
		$model = M('role');
		//判断
		$users_data = $model->field('id,type')->where(array('id'=>$data['id']))->find();
		if(empty($users_data)){
			return ['msg'=>'数据不存在','status'=>400];
		}
		if($users_data['type'] == 1){
			return ['msg'=>'操作有误','status'=>400];
		}
		//判断是否有员工有该角色
		$check = M('users')->field('id')->where(array('role_id'=>$data['id']))->find();
		if($check){
			return ['msg'=>'该角色有成员存在','status'=>400];
		}

		//删除数据
		$model->startTrans();
		$res = $model->where(array('id'=>$data['id']))->delete();
		if($res === false){
			$model->rollback();
			return ['msg'=>'删除失败','status'=>400];
		}
		$res = M('role_rules')->where(array('role_id'=>$data['id']))->delete();
		if($res === false){
			$model->rollback();
			return ['msg'=>'删除失败','status'=>400];
		}
		$model->commit();
		return ['msg'=>'删除成功','status'=>200];
	}

	/**
	 * 获取权限
	 */
	public function get_rules($data){
		$model = M('rules');
		$lists = $model->field('id,pid,title,mark')->order('pid asc,sort asc,id desc')->select();
		$rules_id = M('role_rules')->where(array('role_id'=>$data['role_id']))->getField('rules_id',true);
		$return = array();
		foreach ($lists as $key => $value) {
			if($value['pid'] == 0){
				$return[$value['id']]['title'] = $value['title'];
			}else{
				if(in_array($value['id'],$rules_id)){
					$value['is_on'] = 1;
				}
				$return[$value['pid']]['content'][] = $value;
			}
		}
		return $return;
	}

	/**
	 * 编辑权限
	 */
	public function edit_rules($data){
		$data = set_trim($data);
		//判断
		if(empty($data['role_id'])){
			return ['msg'=>'数据不存在','status'=>400];
		}elseif(empty($data['rules']) || !is_array($data['rules'])){
			return ['msg'=>'请设置客户可见权限','status'=>400];
		}
		$must_rules = M('rules')->where(array('mark'=>['in',['customer/lists','customer/my_lists']]))->getField('id',true);
		$result = array_intersect($data['rules'],$must_rules);
		if(empty($result)){
			return ['msg'=>'请设置客户可见权限','status'=>400];
		}
		$add_data = array();
		foreach ($data['rules'] as $key => $value) {
			if(empty($value)){
				continue;
			}
			$add_data[] = array('role_id'=>$data['role_id'],'rules_id'=>$value);
		}
		if(empty($add_data)){
			return ['msg'=>'请设置客户可见权限','status'=>400];
		}
		$model = M('role_rules');
		$model->startTrans();
		$res = $model->where(array('role_id'=>$data['role_id']))->delete();
		if($res === false){
			$model->rollback();
			return ['msg'=>'保存失败','status'=>400];
		}
		$res = $model->addAll($add_data);
		if($res === false){
			$model->rollback();
			return ['msg'=>'保存失败','status'=>400];
		}
		$model->commit();
		return ['msg'=>'保存成功','status'=>200];
	}
}
