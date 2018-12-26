<?php

namespace Admin\Controller;

class SettingController extends BaseController {

	/**
	 * 业务字段列表
	 */
	public function fields_lists(){
		$info = D('Setting')->get_fields_lists(array(),$this->_p,$this->_row);
		$this->assign('lists',$info['lists']);
		//分页
		$page = new \Think\Page($info['count'], $this->_row);
		$p = $page->show();
		$this->assign('_page', $p ? $p : '');
		$this->display();
	}
	/**
	 * 新增、编辑字段
	 */
	public function edit_fields(){
		if(IS_POST){
			$data = I('post.');
			$data['users_id'] = $this->_user['id'];
			$res = D('Setting')->edit_fields($data);
			if($res['status'] != 200){
				$this->error($res['msg']);
			}
			$this->success($res['msg']);
		}
		$data = I('get.');
		$info = D('Setting')->get_fields_info($data);
		$this->assign('info',$info);
		//配置
		$type_data = D('Setting')->get_fields_type_config();
		$this->assign('type_data',$type_data);
		$category_data = D('Setting')->get_fields_category();
		$this->assign('category_data',$category_data);
		$this->display();
	}

	/**
	 * 删除字段
	 */
	public function del_fields(){
		$data = I('get.');
		$res = D('Setting')->del_fields($data);
		if($res['status'] != 200){
			$this->error($res['msg']);
		}
		$this->success($res['msg']);
	}

	/**
	 * 编辑公海
	 */
	public function edit_waters(){
		if(IS_POST){
			$data = I('post.');
			$data['users_id'] = $this->_user['id'];
			$res = D('Setting')->edit_waters($data);
			if($res['status'] != 200){
				$this->error($res['msg']);
			}
			$this->success($res['msg']);
		}
		$info = D('Setting')->get_waters();
		$this->assign('info',$info);
		$this->display();
	}
}
