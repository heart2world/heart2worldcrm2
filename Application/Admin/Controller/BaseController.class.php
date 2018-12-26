<?php

/**
 * 后台基类
 */

namespace Admin\Controller;

use Think\Controller;

class BaseController extends Controller {

	protected $_map = ""; //get参数
	protected $_p = 1; //分页页数
	protected $_order = ""; //排序
	protected $_row = 30; //每页条数
	protected $_user = ""; //登录对象
	protected $_post = ""; //post参数
	protected $_model = ""; //模型对象
	protected $_type = false; //是否是_edit
	protected $_config = ""; //系统配置
	protected $_roleId = ""; //当前角色ID

	public function _initialize() {
		//不需要验证登录的
		$thisaction = strtolower(CONTROLLER_NAME) . '/' . strtolower(ACTION_NAME);
		if (in_array($thisaction, C('NOT_CHECK_LOGIN_ACTION'))) {
			return true;
		}
		$this->_p = I('get.p',1);

		$this->_user = session('user_auth');
		if(empty($this->_user)){
			header('Location: '.U('Public/login','',true,true));
		}
		defined("UID") || define("UID", $this->_user['id']);

		$this->assign('user',$this->_user);

		//判断
		$user_data = M('users')->field('id,status')->where(array('id'=>UID))->find();
		if(empty($user_data) || $user_data['status'] == 2){
			session(null);
			header('Location: '.U('Public/login','',true,true));
		}

		//菜单
		$menu_data = array(
			array(
				'title'=>'客户管理',
				'type'=>1,
				'level_data'=>array(
					array(
						'title'=>'全部客户',
						'url'=>U('Index/lists'),
						'mark'=>'customer/lists',
						'extra'=>array('index/lists'),
					),
					array(
						'title'=>'我的客户',
						'url'=>U('Index/my_lists'),
						'mark'=>'customer/my_lists',
						'extra'=>array('index/my_lists'),
					),
				)
			),
			array(
				'title'=>'客户公海',
				'type'=>4,
				'level_data'=>array(
					array(
						'title'=>'客户公海',
						'url'=>U('waters/lists'),
						'mark'=>'waters/lists',
						'extra'=>array('waters/lists','waters/ajaxLists','waters/edit_fields_show','waters/edit_fields_screen','waters/grab','excel/waters_excel'),
					),
				)
			),
			array(
				'title'=>'系统设置',
				'type'=>2,
				'level_data'=>array(
					array(
						'title'=>'业务字段',
						'url'=>U('Setting/fields_lists'),
						'mark'=>'setting/index',
						'extra'=>array('setting/fields_lists','setting/edit_fields','setting/del_fields'),
					),
					array(
						'title'=>'公海设置',
						'url'=>U('Setting/edit_waters'),
						'mark'=>'setting/index',
						'extra'=>array('setting/edit_waters'),
					),
				)
			),
			array(
				'title'=>'员工管理',
				'type'=>3,
				'level_data'=>array(
					array(
						'title'=>'员工管理',
						'url'=>U('Users/users_lists'),
						'mark'=>'users/index',
						'extra'=>array('users/users_lists','users/edit_users','users/edit_status','users/edit_password','users/del_users'),
					),
					array(
						'title'=>'角色管理',
						'url'=>U('Users/role_lists'),
						'mark'=>'users/index',
						'extra'=>array('users/role_lists','users/edit_role','users/del_role','users/edit_rule'),
					),
				)
			)
		);
		//菜单权限
		if($this->_user['type'] == 1){
			//管理员权限
			$rules_data = M('rules')->getField('mark',true);
			$rules_data = array_filter($rules_data);
		}else{
			$role_id = $this->_user['role_id'];
			$rules_id = M('role_rules')->where(array('role_id'=>$role_id))->getField('rules_id',true);
			if($rules_id){
				$rules_data = M('rules')->where(array('id'=>['in',$rules_id]))->getField('mark',true);
				$rules_data = array_filter($rules_data);
			}
		}
		foreach ($menu_data as $key => $value) {
			$is_show = 0;
			foreach ($value['level_data'] as $k => $v) {
				if(!in_array($v['mark'],$rules_data)){
					unset($menu_data[$key]['level_data'][$k]);
				}
				if(in_array($thisaction,$v['extra'])){
					$is_show = 1;
					$menu_data[$key]['level_data'][$k]['is_show'] = 1;
				}
			}
			if(empty($menu_data[$key]['level_data'])){
				unset($menu_data[$key]);
				continue;
			}
			$menu_data[$key]['is_show'] = $is_show;
		}
		$this->assign('menu_data',$menu_data);
	}

}
