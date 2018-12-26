<?php

namespace Admin\Controller;

class PublicController extends BaseController {

	/**
	 * 用户登录
	 */
	public function login(){
		if(IS_POST){
			$data = I('post.');
			$res = D('Users')->login($data);
			if($res['status'] != 200){
				$this->error($res['msg']);
			}
			$this->success($res['msg'],U('Index/index'));
		}
		$this->display();
	}

	/**
	 * 退出
	 */
	public function logout(){
		session('user_auth',null);
		redirect(U('Public/login'));
	}

	/**
	 * 图片验证码
	 */
	public function verify_code(){
		ob_clean();
		$verify = new \Think\Verify(array('imageW'=>'320px','length'=>4));
		$verify->entry();
	}

	/**
	 * 修改最新跟进时间-执行脚本
	 */
	public function new_follow_time(){
		$model = M('customer');
		$model->startTrans();
		$lists = $model->alias('c')->field('c.id,c.create_time,max(f.create_time) as new_follow_time')->join('left join sys_follow as f ON f.customer_id = c.id')->group('c.id')->select();
		foreach ($lists as $key => $value) {
			$new_follow_time = $value['create_time'];
			if($value['new_follow_time']){
				$new_follow_time = $value['new_follow_time'];
			}
			$no_follow_day = strtotime(date('Y-m-d')) - strtotime(date('Y-m-d',$new_follow_time));
			$no_follow_day = $no_follow_day/(24*60*60);
			$res = $model->where(array('id'=>$value['id']))->save(array('new_follow_time'=>$new_follow_time,'no_follow_day'=>$no_follow_day));
			if($res === false){
				echo 'bs';
				$model->rollback();
				die;
			}
		}
		$model->commit();
		echo 'cg';
	}

	/**
	 * 检测工作日
	 * return 1上班 2休息
	 */
	public function check_work_day(){
		$month = date('Y-n');
		$day = date('Y-n-j');
		$week = date('w');
		header("Content-type: text/html; charset=utf-8");
		$result = file_get_contents('http://v.juhe.cn/calendar/month?year-month='.$month.'&key=f7b0974f0d5608e67370d0b7adf068c4');
		$result = json_decode($result,true);
		$holiday_array = $result['result']['data']['holiday_array'];
		if($holiday_array){
			$rest_day = array();//休息日
			$work_day = array();//工作日
			foreach ($holiday_array as $key => $value) {
				foreach ($value['list'] as $k => $v) {
					if($v['status'] == 1){
						$rest_day[] = $v['date'];
					}elseif($v['status'] == 2){
						$work_day[] = $v['date'];
					}
				}
			}
		}
		if(in_array($week,[0,6])){
			//休息日
			if(in_array($day,$work_day)){
				//上班
				return 1;
			}else{
				return 2;
			}
		}else{
			//工作日
			if(in_array($day,$rest_day)){
				//休息
				return 2;
			}else{
				return 1;
			}
		}
	}

	/**
	 * 客户掉入公海-自动任务
	 */
	public function auto_into_waters(){
		$now_time = date('His',NOW_TIME);
		if($now_time == 235959){
			$model = M('customer');
			$type = $this->check_work_day();
			if($type == 1){
				$model->startTrans();
				//工作日
				//查询需要掉入公海的用户
				$setting = D('Setting')->get_waters();
				$time = $setting['time'];
				$map = array();
				$map['status'] = 1;
				if($setting['type'] == 1){
					//统一规则
					$map['no_follow_day'] = ['egt',$time[0]];
				}else{
					$string = '';
					for($i = 1;$i<=5;$i++){
						if(empty($string)){
							$string .= '(type = '.$i.' and no_follow_day >= '.$time[$i].')';
						}else{
							$string .= ' or (type = '.$i.' and no_follow_day >= '.$time[$i].')';
						}
					}
					$map['_string'] = $string;
				}
				$lists = $model->field('id,person_id')->where($map)->select();
				foreach ($lists as $key => $value) {
					//增加日志
					$log_data = array();
					$log_data['users_id'] = $value['person_id'];
					$log_data['customer_id'] = $value['id'];
					$log_data['type'] = 6;
					$log_data['content'] = '客户掉入公海';
					$res = D('Customer')->add_customer_logs($log_data);
					if($res['status'] != 200){
						$model->rollback();
						return;
					}
				}
				$res = $model->where($map)->save(array('status'=>2));
				if($res === false){
					$model->rollback();
					return;
				}
				//未跟进增加1
				$res = $model->where(array('status'=>1))->setInc('no_follow_day',1);
				if($res === false){
					$model->rollback();
					return;
				}

				$model->commit();
			}
		}
	}
}
