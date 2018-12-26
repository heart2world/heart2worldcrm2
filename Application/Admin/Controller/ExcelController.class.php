<?php

namespace Admin\Controller;

class ExcelController extends BaseController {

	/**
	 * 客户信息导出
	 */
	public function customer_excel(){
		//表头
		$screen_map['users_id'] = $this->_user['id'];
		$show_data = D('Customer')->get_screen($screen_map);
		$show_data = $show_data['show_data'];
		$cellName = array();
		foreach ($show_data as $key => $value) {
			$cellName[] = array($value['fields_text'],$value['title']);
		}
		//数据
		$data = I('get.');
		$data['uid'] = $this->_user['id'];
		$data['status'] = 1;
		$lists = D('Customer')->get_lists($data);
		$lists = $lists['lists'];
		$dataLists = array();
		foreach ($lists as $key => $value) {
			$one_data = array();
			foreach ($show_data as $k => $v) {
				$one_data[$v['fields_text']] = $value['data'][$k];
			}
			$dataLists[] = $one_data;
		}
		$this->exportExcel("客户信息",$cellName,$dataLists);
	}
	/**
	 * 客户公海导出
	 */
	public function waters_excel(){
		//表头
		$screen_map['users_id'] = $this->_user['id'];
		$screen_map['waters_type'] = 1;
		$show_data = D('Customer')->get_screen($screen_map);
		$show_data = $show_data['show_data'];
		$cellName = array();
		foreach ($show_data as $key => $value) {
			$cellName[] = array($value['fields_text'],$value['title']);
		}
		//数据
		$data = I('get.');
		$data['uid'] = $this->_user['id'];
		$data['status'] = 2;
		$data['waters_type'] = 1;
		$lists = D('Customer')->get_lists($data);
		$lists = $lists['lists'];
		$dataLists = array();
		foreach ($lists as $key => $value) {
			$one_data = array();
			foreach ($show_data as $k => $v) {
				$one_data[$v['fields_text']] = $value['data'][$k];
			}
			$dataLists[] = $one_data;
		}
		$this->exportExcel("客户公海信息",$cellName,$dataLists);
	}
	/**
	 * excel导出
	 * @param $title
	 * @param $cellName
	 * @param $data
	 * @throws \PHPExcel_Exception
	 * @throws \PHPExcel_Reader_Exception
	 */
	public function exportExcel($title,$cellName,$data){
		vendor("PHPExcel.PHPExcel");
		$objPHPExcel = new \PHPExcel();

		//定义配置
		$topNumber = 2;//表头有几行占用
		$xlsTitle = iconv('utf-8', 'gb2312', $title);//文件名称
		$fileName = $title.date('_YmdHis');//文件名称
		$cellKey = array(
				'A','B','C','D','E','F','G','H','I','J','K','L','M',
				'N','O','P','Q','R','S','T','U','V','W','X','Y','Z',
				'AA','AB','AC','AD','AE','AF','AG','AH','AI','AJ','AK','AL','AM',
				'AN','AO','AP','AQ','AR','AS','AT','AU','AV','AW','AX','AY','AZ'
		);

		//处理表头
		foreach ($cellName as $k=>$v)
		{
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue($cellKey[$k].$topNumber, $v[1]);//设置表头数据
			$objPHPExcel->getActiveSheet()->freezePane($cellKey[$k].($topNumber+1));//冻结窗口
			$objPHPExcel->getActiveSheet()->getStyle($cellKey[$k].$topNumber)->getFont()->setBold(true);//设置是否加粗
			$objPHPExcel->getActiveSheet()->getStyle($cellKey[$k].$topNumber)->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);//垂直居中
			if($v[3] > 0)//大于0表示需要设置宽度
			{
				$objPHPExcel->getActiveSheet()->getColumnDimension($cellKey[$k])->setWidth($v[3]);//设置列宽度
			}
		}
		//处理数据
		foreach ($data as $k=>$v)
		{
			foreach ($cellName as $k1=>$v1)
			{
				$objPHPExcel->getActiveSheet()->setCellValue($cellKey[$k1].($k+1+$topNumber), $v[$v1[0]]);
				if($v['end'] > 0)
				{
					if($v1[2] == 1)//这里表示合并单元格
					{
						$objPHPExcel->getActiveSheet()->mergeCells($cellKey[$k1].$v['start'].':'.$cellKey[$k1].$v['end']);
						$objPHPExcel->getActiveSheet()->getStyle($cellKey[$k1].$v['start'])->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
					}
				}
				if($v1[4] != "" && in_array($v1[4], array("LEFT","CENTER","RIGHT")))
				{
					$v1[4] = eval('return PHPExcel_Style_Alignment::HORIZONTAL_'.$v1[4].';');
					//这里也可以直接传常量定义的值，即left,center,right；小写的strtolower
					$objPHPExcel->getActiveSheet()->getStyle($cellKey[$k1].($k+1+$topNumber))->getAlignment()->setHorizontal($v1[4]);
				}
			}
		}
		//导出execl
		header('pragma:public');
		header('Content-type:application/vnd.ms-excel;charset=utf-8;name="'.$xlsTitle.'.xls"');
		header("Content-Disposition:attachment;filename=$fileName.xls");//attachment新窗口打印inline本窗口打印
		$objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		$objWriter->save('php://output');
		exit;
	}



	public function customer_import(){
		$data = $this->importExecl('C:\Users\Administrator\Desktop\11月9日客户\客户.xlsx');
		//用户信息
		$users_data = M('users')->field('id,name')->select();
		$users_data = array_column($users_data,'id','name');
		//客户类型
		$type_data = D('Setting')->get_customer_type_config();
		$type_data = array_column($type_data,'id','title');
		//跟进状态
		$status_data = D('Setting')->get_follow_status_config();
		$status_data = array_column($status_data,'id','title');
		$model = M('customer');
		$model->startTrans();
		foreach ($data as $key => $value) {
			$one_data = array();
			$one_data['person_id'] = $users_data[$value['A']];//负责人
			$one_data['name'] = $value['B'];
			$one_data['mobile'] = $value['N'];
			$one_data['create_time'] = strtotime($value['D']);
			$one_data['update_time'] = strtotime($value['D']);
			$one_data['type'] = $type_data[$value['E']];
			$one_data['follow_status'] = $status_data[$value['H']];
			$one_data['next_follow_time'] = strtotime($value['J']);
			$one_data['remark'] = $value['Z'];
			$one_data['users_id'] = $users_data[$value['M']] ? $users_data[$value['M']] : 1;//创建人
			$customer_id = M('customer')->add($one_data);
			if($customer_id === false){
				$model->rollback();
				echo '操作失败';die;
			}
			//自定义字段
			$value['L'] = explode('，',$value['L']);
			$value['L'] = '_'.implode('_',$value['L']).'_';
			$two_data = array(
				array('customer_id'=>$customer_id,'fields_id'=>1,'content'=>$value['K']),
				array('customer_id'=>$customer_id,'fields_id'=>12,'content'=>$value['L'])
			);
			//微信号
			if($value['O']){
				$two_data[] = array('customer_id'=>$customer_id,'fields_id'=>24,'content'=>$value['O']);
			}
			//公司名称
			if($value['P']){
				$two_data[] = array('customer_id'=>$customer_id,'fields_id'=>25,'content'=>$value['P']);
			}
			//地址
			if($value['Q']){
				$two_data[] = array('customer_id'=>$customer_id,'fields_id'=>26,'content'=>$value['Q']);
			}
			//更新于
			if($value['T']){
				$two_data[] = array('customer_id'=>$customer_id,'fields_id'=>6,'content'=>$value['T']);
			}
			//搜索关键词
			if($value['U']){
				$two_data[] = array('customer_id'=>$customer_id,'fields_id'=>14,'content'=>$value['U']);
			}
			//其他联系方式
			if($value['V']){
				$two_data[] = array('customer_id'=>$customer_id,'fields_id'=>27,'content'=>$value['V']);
			}
			//特殊属性
			if($value['V']){
				$two_data[] = array('customer_id'=>$customer_id,'fields_id'=>11,'content'=>$value['W']);
			}
			//区县
			if($value['X']){
				$two_data[] = array('customer_id'=>$customer_id,'fields_id'=>13,'content'=>$value['X']);
			}
			$res = M('customer_fields')->addAll($two_data);
			if($res === false){
				$model->rollback();
				echo '操作失败';die;
			}
		}
		$model->commit();
		echo '操作成功';
	}

	public function follow_import(){
		$data = $this->importExecl('C:\Users\Administrator\Desktop\11月9日客户\日志11-02.xlsx');
		//客户信息
		$customer_data = M('customer')->field('id,name')->select();
		$customer_data = array_column($customer_data,'id','name');
		//用户信息
		$users_data = M('users')->field('id,name')->select();
		$users_data = array_column($users_data,'id','name');
		//跟进状态
		$type_data = D('Setting')->get_follow_type_config();
		$type_data = array_column($type_data,'id','title');
		$model = M('follow');
		$model->startTrans();
		$add_follow_data = array();
		$add_logs_data = array();
		foreach ($data as $key => $value) {
			$one_data = array();
			$customer_id = $customer_data[$value['B']];
			if(empty($customer_id)){
				continue;
			}
			$one_data['customer_id'] = $customer_id;
			$one_data['users_id'] = $users_data[$value['G']];
			$one_data['type'] = $type_data[$value['D']];
			$one_data['content'] = $value['E'];
			$one_data['follow_time'] = strtotime($value['F']);
			$one_data['next_follow_time'] = 0;
			$one_data['create_time'] = strtotime($value['H']);
			$add_follow_data[] = $one_data;
			$content = array('content'=>$one_data['content'],'follow_time'=>$value['F'],'follow_user'=>$value['G'],'follow_type'=>$value['D']);
			$content = json_encode($content);
			$add_logs_data[] = array('customer_id'=>$customer_id,'content'=>$content,'create_time'=>$one_data['create_time']);
		}
		$res = $model->addAll($add_follow_data);
		if($res === false){
			$model->rollback();
			echo '操作失败';die;
		}
		$res = M('customer_logs')->addAll($add_logs_data);
		if($res === false){
			$model->rollback();
			echo '操作失败';die;
		}
		$model->commit();
		echo '操作成功5';
	}

	/**
	 *  数据导入
	 * @param string $file excel文件
	 * @param string $sheet
	 * @return string   返回解析数据
	 * @throws PHPExcel_Exception
	 * @throws PHPExcel_Reader_Exception
	 */
	function importExecl($file='', $sheet=0){
		$file = iconv("utf-8", "gb2312", $file);   //转码
		if(empty($file) OR !file_exists($file)) {
			die('file not exists!');
		}
		vendor("PHPExcel.PHPExcel");
		Vendor("PHPExcel.PHPExcel.Reader.Excel2007");
		$objRead = new \PHPExcel_Reader_Excel2007();   //建立reader对象
		if(!$objRead->canRead($file)){
			Vendor("PHPExcel.PHPExcel.Reader.Excel5");
			$objRead = new \PHPExcel_Reader_Excel5();
			if(!$objRead->canRead($file)){
				die('No Excel!');
			}
		}

		$cellName = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', 'AA', 'AB', 'AC', 'AD', 'AE', 'AF', 'AG', 'AH', 'AI', 'AJ', 'AK', 'AL', 'AM', 'AN', 'AO', 'AP', 'AQ', 'AR', 'AS', 'AT', 'AU', 'AV', 'AW', 'AX', 'AY', 'AZ');

		$obj = $objRead->load($file);  //建立excel对象
		$currSheet = $obj->getSheet($sheet);   //获取指定的sheet表
		$columnH = $currSheet->getHighestColumn();   //取得最大的列号
		$columnCnt = array_search($columnH, $cellName);
		$rowCnt = $currSheet->getHighestRow();   //获取总行数

		$data = array();
		for($_row=1; $_row<=$rowCnt; $_row++){  //读取内容
			for($_column=0; $_column<=$columnCnt; $_column++){
				$cellId = $cellName[$_column].$_row;
				$cellValue = $currSheet->getCell($cellId)->getValue();
				//$cellValue = $currSheet->getCell($cellId)->getCalculatedValue();  #获取公式计算的值
				if($cellValue instanceof PHPExcel_RichText){   //富文本转换字符串
					$cellValue = $cellValue->__toString();
				}

				$data[$_row][$cellName[$_column]] = $cellValue;
			}
		}
		unset($data[1]);
		return $data;
	}

}
