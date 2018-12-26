<?php

namespace Admin\Controller;
/**
 * 公海管理
 */
class WatersController extends BaseController {

	/**
	 * 公海列表
	 */
	public function lists(){
		$screen_map['users_id'] = $this->_user['id'];
		$screen_map['waters_type'] = 1;
		$info = D('Customer')->get_screen($screen_map);
		$this->assign('info',$info);
		$this->assign('url',U('Waters/ajaxLists'));
		$this->display();
	}

	public function ajaxLists(){
		$this->_p = I('post.p',1);
		$data = I('post.');
		$data['uid'] = $this->_user['id'];
		$data['status'] = 2;
		$data['waters_type'] = 1;
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
			$data['waters_type'] = 1;
			$res = D('Customer')->edit_fields_show($data);
			if($res['status'] != 200){
				$this->error($res['msg']);
			}
			$this->success($res['msg']);
		}
		$data['users_id'] = $this->_user['id'];
		$data['waters_type'] = 1;
		$lists = D('Customer')->get_fields_show($data);
		$this->assign('lists',$lists);
		$this->display('index/edit_fields_show');
	}

	/**
	 * 列表字段筛选
	 */
	public function edit_fields_screen(){
		if(IS_POST){
			$data = I('post.');
			$data['users_id'] = $this->_user['id'];
			$data['waters_type'] = 1;
			$res = D('Customer')->edit_fields_screen($data);
			if($res['status'] != 200){
				$this->error($res['msg']);
			}
			$this->success($res['msg']);
		}
		$data['users_id'] = $this->_user['id'];
		$data['waters_type'] = 1;
		$lists = D('Customer')->get_fields_screen($data);
		$this->assign('lists',$lists);
		$this->display('index/edit_fields_screen');
	}

	/**
	 * 抢单
	 */
	public function grab(){
		$data = I('get.');
		$data['users_id'] = $this->_user['id'];
		$res = D('Customer')->grab($data);
		if($res['status'] != 200){
			$this->error($res['msg']);
		}
		$this->success($res['msg']);
	}

	/**
	 * 转移至公海
	 */
	public function into(){
		$data = I('get.');
		$data['users_id'] = $this->_user['id'];
		$res = D('Customer')->into_waters($data);
		if($res['status'] != 200){
			$this->error($res['msg']);
		}
		$this->success($res['msg']);
	}
}
