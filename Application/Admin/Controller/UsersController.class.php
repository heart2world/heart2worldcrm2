<?php

namespace Admin\Controller;

class UsersController extends BaseController {

	/**
	 * 员工管理列表
	 */
	public function users_lists(){
		$info = D('Users')->get_users_lists(array(),$this->_p,$this->_row);
		$this->assign('lists',$info['lists']);
		//分页
		$page = new \Think\Page($info['count'], $this->_row);
		$p = $page->show();
		$this->assign('_page', $p ? $p : '');
		$this->display();
	}
	/**
	 * 新增、编辑员工
	 */
	public function edit_users(){
		if(IS_POST){
			$data = I('post.');
			$res = D('Users')->edit_users($data);
			if($res['status'] != 200){
				$this->error($res['msg']);
			}
			$this->success($res['msg']);
		}
		$data = I('get.');
		$info = D('Users')->get_users_info($data);
		$this->assign('info',$info);
		//角色配置
		$role_data = D('Users')->get_role_config();
		$this->assign('role_data',$role_data);
		$this->display();
	}

	/**
	 * 个人-修改密码
	 */
	public function update_password(){
		if(IS_POST){
			$data = I('post.');
			$data['id'] = $this->_user['id'];
			$res = D('Users')->edit_password($data);
			if($res['status'] != 200){
				$this->error($res['msg']);
			}
			$this->success($res['msg']);
		}
		$this->display('edit_password');
	}

	/**
	 * 员工管理-重置密码
	 */
	public function edit_password(){
		if(IS_POST){
			$data = I('post.');
			$res = D('Users')->edit_password($data);
			if($res['status'] != 200){
				$this->error($res['msg']);
			}
			$this->success($res['msg']);
		}
		$this->display();
	}

	/**
	 * 冻结、解冻员工
	 */
	public function edit_status(){
		$data = I('get.');
		$res = D('Users')->edit_status($data);
		if($res['status'] != 200){
			$this->error($res['msg']);
		}
		$this->success($res['msg']);
	}

	/**
	 * 删除员工
	 */
	public function del_users(){
		$data = I('get.');
		$res = D('Users')->del_users($data);
		if($res['status'] != 200){
			$this->error($res['msg']);
		}
		$this->success($res['msg']);
	}

	/**
	 * 角色管理列表
	 */
	public function role_lists(){
		$info = D('Users')->get_role_lists(array(),$this->_p,$this->_row);
		$this->assign('lists',$info['lists']);
		//分页
		$page = new \Think\Page($info['count'], $this->_row);
		$p = $page->show();
		$this->assign('_page', $p ? $p : '');
		$this->display();
	}

	/**
	 * 新增、编辑角色
	 */
	public function edit_role(){
		if(IS_POST){
			$data = I('post.');
			$res = D('Users')->edit_role($data);
			if($res['status'] != 200){
				$this->error($res['msg']);
			}
			$this->success($res['msg']);
		}
		$data = I('get.');
		$info = D('Users')->get_role_info($data);
		$this->assign('info',$info);
		$this->display();
	}

	/**
	 * 删除角色
	 */
	public function del_role(){
		$data = I('get.');
		$res = D('Users')->del_role($data);
		if($res['status'] != 200){
			$this->error($res['msg']);
		}
		$this->success($res['msg']);
	}

	/**
	 * 编辑权限
	 */
	public function edit_rule(){
		if(IS_POST){
			$data = I('post.');
			$res = D('Users')->edit_rules($data);
			if($res['status'] != 200){
				$this->error($res['msg']);
			}
			$this->success($res['msg']);
		}
		$data['role_id'] = I('get.role_id');
		$rules = D('Users')->get_rules($data);
		$this->assign('rules',$rules);
		$this->display();
	}
}
