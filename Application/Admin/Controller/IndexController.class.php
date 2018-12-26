<?php

namespace Admin\Controller;

class IndexController extends BaseController {

	public function index() {
		$this->display();
	}

	/**
	 * 客户管理列表
	 */
	public function lists(){
		$screen_map['users_id'] = $this->_user['id'];
		$info = D('Customer')->get_screen($screen_map);
		$this->assign('info',$info);
		$this->assign('url',U('Index/ajaxLists'));
		$this->assign('is_my',0);
		$this->display();
	}

	public function ajaxLists(){
		$this->_p = I('post.p',1);
		$data = I('post.');
		$data['uid'] = $this->_user['id'];
		$data['is_my'] = 0;
		$data['status'] = 1;
		$info = D('Customer')->get_lists($data,$this->_p,$this->_row);
		$this->assign('info',$info);
		//分页
		$page = new \Think\Page($info['count'], $this->_row,array('p'=>$this->_p));
		$p = $page->special_show();
		$html['lists'] = $this->fetch('ajax_lists');
		$html['page'] = $p;
		$this->ajaxReturn($html);
	}

	/**
	 * 客户管理列表
	 */
	public function my_lists(){
		$screen_map['users_id'] = $this->_user['id'];
		$info = D('Customer')->get_screen($screen_map);
		$this->assign('info',$info);
		$this->assign('url',U('Index/ajaxMyLists'));
		$this->assign('is_my',1);
		$this->display('lists');
	}

	public function ajaxMyLists(){
		$this->_p = I('post.p',1);
		$data = I('post.');
		$data['uid'] = $this->_user['id'];
		$data['is_my'] = 1;
		$data['status'] = 1;
		$info = D('Customer')->get_lists($data,$this->_p,$this->_row);
		$this->assign('info',$info);
		//分页
		$page = new \Think\Page($info['count'], $this->_row,array('p'=>$this->_p));
		$p = $page->special_show();
		$html['lists'] = $this->fetch('ajax_lists');
		$html['page'] = $p;
		$this->ajaxReturn($html);
	}

	/**
	 * 列表字段的显示
	 */
	public function edit_fields_show(){
		if(IS_POST){
			$data = I('post.');
			$data['users_id'] = $this->_user['id'];
			$res = D('Customer')->edit_fields_show($data);
			if($res['status'] != 200){
				$this->error($res['msg']);
			}
			$this->success($res['msg']);
		}
		$data['users_id'] = $this->_user['id'];
		$lists = D('Customer')->get_fields_show($data);
		$this->assign('lists',$lists);
		$this->display();
	}

	/**
	 * 列表字段筛选
	 */
	public function edit_fields_screen(){
		if(IS_POST){
			$data = I('post.');
			$data['users_id'] = $this->_user['id'];
			$res = D('Customer')->edit_fields_screen($data);
			if($res['status'] != 200){
				$this->error($res['msg']);
			}
			$this->success($res['msg']);
		}
		$data['users_id'] = $this->_user['id'];
		$lists = D('Customer')->get_fields_screen($data);
		$this->assign('lists',$lists);
		$this->display();
	}

	/**
	 * 新增、编辑客户
	 */
	public function edit(){
		if(IS_POST){
			$data = I('post.');
			$data['users_id'] = $this->_user['id'];
			$res = D('Customer')->edit($data);
			if($res['status'] != 200){
				$this->error($res['msg']);
			}
			$this->success($res['msg']);
		}
		$data = I('get.');
		$info = D('Customer')->get_info($data);
		if(empty($info)){
			$info['person_id'] = $this->_user['id'];
		}
		$this->assign('info',$info);
		//配置
		$configs = array();
		$configs['customer_type'] = D('Setting')->get_customer_type_config();
		$configs['follow_status'] = D('Setting')->get_follow_status_config();
		$configs['person'] = D('Setting')->get_person();
		//字段
		$fields_data = M('fields')->field('id,type,title,is_must,tips,extra')->where(array('status'=>1,'is_open'=>1))->order('sort asc,id desc')->select();
		$time_ids = array();
		foreach ($fields_data as $key => $value) {
			if(in_array($value['type'],[3,4])){
				$value['extra'] = json_decode($value['extra'],true);
			}else{
				$value['extra'] = '';
			}
			if($value['type'] == 7){
				$time_ids[] = $value['id'];
			}
			$fields_data[$key] = $value;
		}
		$configs['fields_data'] = $fields_data;
		$configs['time_ids'] = json_encode($time_ids);
		$this->assign('configs',$configs);
		$this->display();
	}

	/**
	 * 客户详情
	 */
	public function customer_info(){
		$data['id'] = I('get.id');
		$info = D('Customer')->get_info_text($data);
		$this->assign('info',$info);
		$this->display();
	}

	/**
	 * 客户完整信息
	 */
	public function info(){
		//客户信息
		$data['id'] = I('get.id');
		$info = D('Customer')->get_info_text($data);
		$this->assign('info',$info);
		//跟进信息
		$data['customer_id'] = I('get.id');
		$logs = D('Customer')->get_customer_logs($data);
		$this->assign('logs',$logs);
		$this->display();
	}

	/**
	 * 写跟进
	 */
	public function add_follow(){
		if(IS_POST){
			$data = I('post.');
			$data['users_id'] = $this->_user['id'];
			$res = D('Customer')->add_follow($data);
			if($res['status'] != 200){
				$this->error($res['msg']);
			}
			$this->success($res['msg']);
		}
		//配置
		$configs = array();
		$configs['follow_type'] = D('Setting')->get_follow_type_config();
		$configs['follow_status'] = D('Setting')->get_follow_status_config();
		$customer_id = I('get.customer_id');
		$configs['status'] = M('customer')->where(array('id'=>$customer_id))->getField('follow_status');
		$configs['status'] = $configs['status'] ? $configs['status'] : '';
		$this->assign('configs',$configs);
		$this->display();
	}

	/**
	 * 转移客户
	 */
	public function customer_transfer(){
		if(IS_POST){
			$data = I('post.');
			$data['users_id'] = $this->_user['id'];
			$res = D('Customer')->edit_customer_transfer($data);
			if($res['status'] != 200){
				$this->error($res['msg']);
			}
			$this->success($res['msg']);
		}
		$data = I('get.');
		$info = D('Customer')->get_info($data);
		$this->assign('info',$info);
		//配置
		$configs = array();
		$configs['person'] = D('Setting')->get_person();
		$this->assign('configs',$configs);
		$this->display();
	}
}
