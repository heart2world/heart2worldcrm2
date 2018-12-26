<?php

/**
 * 全局自定义函数类.
 */

/**
 * 获取组织架构数据---缓存.
 *
 * @return [type] [description]
 *
 * @author duhaibo
 */
function get_org_data() {
	$data = S('cache_org_data');
	if (!isset($data) || empty($data)) {
		$data = M('org')->where(['status' => ['egt', 0]])->order('pid,level,sort')->select();
		S('cache_org_data', $data);
	}

	return $data;
}

/**
 * 根据某一节点获取所有相关数据，包括上级及下级节点.
 *
 * @param [type] $nodeId [节点ID]
 *
 * @return [type] [description]
 */
function city_tree_by_nodeId($nodeId) {
	$parentData = city_parent_with_nodeId([$nodeId]);
	$childData = city_child_with_nodeId([$nodeId]);
	$data = array_merge($parentData, $childData);
	//去重
	return array_column($data, null, 'id');
}

/**
 * 获取当前节点信息-城市
 * @param  array  $nodeArr [description]
 * @return [type]          [description]
 */
function city_data_by_nodeId($nodeArr = []) {
	$citys = array_column(getCityTree(true), null, 'id');
	$data = [];
	foreach ($nodeArr as $key) {
		array_push($data, $citys[$key]);
	}
	return $data;
}

/**
 * 根据已有节点，返回下级所有节点.
 *
 * @param array $dist [区域ID集合]
 *
 * @return [type] [返回集合]
 */
function city_child_with_nodeId($nodeArr = []) {
	if (empty($nodeArr)) {
		return [];
	}
	//这里只处理了已激活城市
	$citys = array_column(getCityTree(true), null, 'id');
	$tmp = [];
	foreach ($nodeArr as $dist) {
		_city_child_tree_with_nodeId($tmp, $citys, $dist);
	}

	return array_column($tmp, null, 'id');
}

/**
 * 根据已有节点，返回上级所有节点.
 *
 * @param array $dist [区域ID集合]
 *
 * @return [type] [返回集合]
 */
function city_parent_with_nodeId($nodeArr = []) {
	if (empty($nodeArr)) {
		return [];
	}
	//这里只处理了已激活城市
	$citys = array_column(getCityTree(true), null, 'id');
	$tmp = [];
	foreach ($nodeArr as $dist) {
		_city_parent_tree_with_nodeId($tmp, $citys, $dist);
	}

	return array_column($tmp, null, 'id');
}

/**
 * 递归获取数据上级节点，包括当前节点.
 *
 * @param [type] &$tmp    [保存递归数据数组变量]
 * @param [type] $data    [需要递归数据源]
 * @param int    $nodeId  [开始递归节点]
 * @param string $nodeKey [递归节点名]
 *
 * @return [type] [description]
 */
function _city_parent_tree_with_nodeId(&$tmp, $data, $nodeId = 0, $nodeKey = 'pid') {
	// static $tmp = []; //加上静态变量，递归结果将不会清空。其它方法调用则会获取到之前数据
	$tmpData = $data[$nodeId];
	if (!empty($tmpData)) {
		array_unshift($tmp, $tmpData);
		_city_parent_tree_with_nodeId($tmp, $data, $tmpData[$nodeKey]);
	}
	// return $tmp;
}

/**
 * 获取节点下级子节点，不包括当前节点.
 *
 * @param [type] &$tmp    [保存递归数据数组变量]
 * @param [type] $data    [需要递归数据源]
 * @param int    $nodeId  [开始递归节点]
 * @param string $nodeKey [递归节点名]
 *
 * @return [type] [description]
 */
function _city_child_tree_with_nodeId(&$tmp, $data, $nodeId = 0, $nodeKey = 'pid', $childNodeKey = 'id') {
	foreach ($data as $node) {
		if ($node[$nodeKey] == $nodeId) {
			array_push($tmp, $node);
			_city_child_tree_with_nodeId($tmp, $data, $node[$childNodeKey]);
		}
	}
}

/**
 * 跟进详情对应信息.
 * @return [type] [description]
 */
function columnArr($key = 1) {
	$base = [
		'houselayout' => '户型', //qxb_house_dwelling
		//'video' => '视频',
		'pic' => '图片视频',
		'content' => '房源介绍',
		'building_area' => '建筑面积',
		'area' => '套内面积',
		'towards' => '朝向',
		'decorate_status' => '装修',
		'building_structure' => '楼层类型', //2017.8.4 2.7.5由房屋结构改为楼层类型
		'get_way' => '取得方式',
		'house_mating' => '配套设施',
		'home_state' => '房屋特色',
		'house_type' => '物业类型', //qxb_house
		'is_mortgage' => '有无贷款', //qxb_house
		'buildyear' => '建成年代',
		'house_total_price' => '底价',
		'pay_type' => '付款方式', //house_agent_info
		'tax_type' => '税费承担方式',
		// 'entrust_type'       => '委托方式',
		// 'entrust_time'       => '独家委托时间',
		'now_status' => '现状',
		'property_right' => '产权',
		'papers' => '证件',
		'owner' => '业主信息',
		'contacts' => '联系人',
		'houses_title' => '楼盘名称',
		'dist' => '区域',
		'floors_title' => '楼栋',
		'units_title' => '单元',
		'house_floor' => '楼层',
		'total_layer' => '总楼层',
		'house_no' => '房号',
		'units_status' => '楼栋',
		'property_right' => '产权类型',
		'floors_house_type' => '房屋类型',
		'house_cate_type' => '用途',
		'offer_price' => '价格',
		//'pic' => '图片视频',
		// 'room' => '室',
		// 'hall' => '厅',
		// 'toilet' => '卫',
		// 'kitchen' => '厨',
		// 'veranda' => '阳台',
		'trade' => '适用行业',
		'height' => '层高',
		'frontage' => '临街状况',
		'source' => '来源',
		'agent' => '房源人',
		'input_agent' => '录入人',
		'pic_agent' => '图片人',
		'key_agent' => '钥匙人',
		'excellent_agent' => '优质人'];
	switch ($key) {
	case 2:
		$base['rent_type'] = '租赁方式';
		$base['foregift_price'] = '押金';
		$base['house_total_price'] = '租金';
		$base['pay_type'] = '付款方式';
		break;
	}
	return $base;
}
function houselayout() {
	return [
		'room' => '室',
		'hall' => '厅',
		'toilet' => '卫',
		'kitchen' => '厨',
		'veranda' => '阳台',
	];
}
function get_columnstr($key) {
	$data = columnArr(2);
	return $data[$key];
}

/**
 *跟进详情户型字符拼接.
 */
function arrToNewArr($arr) {
	if (empty($arr)) {
		return [];
	}
	$keys = array_keys(current($arr));
	$strarray = ['room' => '室', 'hall' => '厅', 'toilet' => '卫', 'kitchen' => '厨', 'veranda' => '阳台'];
	$newArr = [];
	$show = [];
	foreach ($keys as $key) {
		switch ($key) {
		case 'column':
			$column_arr = array_column($arr, $key);
			foreach ($column_arr as $k => $v) {
				$show[] = $strarray[$v];
			}
			$str = 'houselayout';
			break;
		case 'old':
		case 'new':
			$str = implode('%s', array_column($arr, $key));
			$str = vsprintf($str . '%s', $show);
			break;
		case 'status':
			$str = array_sum(array_column($arr, $key));
			break;
		default:
			$str = implode(',', array_column($arr, $key));
			break;
		}
		$newArr[$key] = $str;
	}
	return $newArr;
}

/**
 * 获取菜单树（html）
 * @param  [type]  $data     [数据源，二维数组：array(['id','pid','title'])]
 * @param  integer $selectId [当前选择项ID]
 * @param  string  $pid      [description]
 * @return [type]            [description]
 */
function getTreeHtml($data, $selectId = 0, $pid = 'pid', $root = 0, $showField = 'title') {
	$tree = " <option value=\"0\">＝请选择＝</option>";
	foreach ($data as $v) {
		if ($v[$pid] == $root) {
			$cate_prent = new \Common\Library\Cate\Tree($data);
			$tree .= "<option value=\"" . $v['id'] . "\" ";
			if ($v['id'] == $selectId) {
				$tree .= "selected=\"selected\"";
			}
			$tree .= " >" . $v[$showField] . "</option>";
			$tree .= $cate_prent->get_tree($v['id'], "<option value=\$id \$selected>\$spacer\$$showField</option>", $selectId);
		}
	}
	return $tree;
}

/**
 * 数组按某字段分组
 * @param  [type] $data     [description]
 * @param  [type] $groupKey [description]
 * @return [type]           [description]
 */
function array_with_group($data, $groupKey) {
	$res = [];
	foreach ($data as $tmp) {
		$res[$tmp[$groupKey]][] = $tmp;
	}
	return $res;
}

function defaultVaule($value, $default) {
	return empty($value) ? $default : $value;
}

function setNo($type, $uid) {
	$BasicApi = CAPI("/CApi/BasicApi");
	return $BasicApi->set_no($type, $uid);
}

/**
 * 保持登录 暂定1天
 */
function setLogin() {
	session_start();
	$lifeTime = 24 * 3600; // 保存一天
	setcookie(session_name(), session_id(), time() + $lifeTime, "/");
}

function setLoginOut() {
	session_start();
	cookie(session_name(), null);
}

function isLogin() {
	$user = session('company_auth');
	$area = array(
		'3' => array(), // 城市区域
		'4' => array(), //商圈
		'5' => array(), //街道
	);
	if (empty($user)) {
		return [];
	} else {
		// return session('user_auth_sign') == data_auth_sign($user) ? $user : [];
		if (!$user['is_admin']) {
			if (
				(!$login_org = session('login_org'))
				&&
				(!$login_city = session('login_city'))
				&&
				(!$login_job = session('login_job'))
				&&
				(!$login_rule = session('login_rule'))
			) {
				$CAPI = CAPI("/CApi/Company");
				$organization = $CAPI->get_organization($user);
				$job = $CAPI->get_job($user);
				session('login_org', $organization['org']);
				session('login_city', $organization['city']);
				session('login_area', $organization['city_group']);
				session('login_job', $job['job']);
				session('login_job_text', $job['job_text']);
				session('login_rule', $job['rule']);
			}
		} else {
			$area['3'] = $user['city'];
			session('login_area', $area);
			session('login_job_text', array(get_manager_title()));
		}
		session('city', $user['city']);
		session('province', $user['province']);

		return $user;
	}

}

function get_jobName() {
	$login_job_text = session('login_job_text');
	if (count($login_job_text) > 0) {
		return implode(',', $login_job_text);
	} else {
		return end($login_job_text);
	}
}

function get_CompanyId() {
	$_user = session('user_auth');
	isset($_user['company_id']) ? $company_id = $_user['company_id'] : $company_id = $_user['id'];
	return $company_id;
}

function get_CompanyName() {
	$_user = isLogin();

	return $_user['company_name'];
}

function set_list($list, $count, $exp = array()) {
	$p = $_GET['page'];
	$rows = $_GET['rows'];
	if ($list) {
		$return = ['rows' => $list, 'records' => $count, 'total' => ceil($count / $rows), 'page' => $p];
	} else {
		$return = ['rows' => $list, 'records' => 0, 'total' => 0, 'page' => 0];
	}
	$return = array_merge($return, $exp);
	return json_encode($return);
}

function set_list_local($list, $count) {
	$p = 1;
	$row = $count;
	if ($list) {
		$return = ['rows' => $list, 'records' => $count, 'total' => ceil($count / $row), 'page' => $p];
	} else {
		$return = ['rows' => $list, 'records' => 0, 'total' => 0, 'page' => 0];
	}
	return json_encode($return);
}

function array_key_value($array, $key, $value) {
	$arr = array();
	foreach ($array as $k => $val) {
		$arr[$val[$key]] = $val[$value];
	}
	return $arr;
}

function get_city_group($key = 0) {
	if ($key) {
		$login_area = session('login_area');
		echo $login_area[$key];
	}
}

function get_top_area() {
	$user = session('company_auth');
	echo $user['city'];

}

function get_cityid() {
	if (!$city = session('city')) {
		$city = M('member')->where('id=' . UID)->getField('city');
	}
	return $city;
}

function get_in_array_key($list, $keys, $values) {
	$array = array();
	foreach ($list as $key => $value) {
		$array[$value[$keys]] = $value[$values];
	}
	return $array;
}

function get_in_array($list, $keys) {
	$array = array();
	foreach ($list as $key => $value) {
		$array[] = $value[$keys];
	}
	return $array;
}

function array_keytovalue($list) {
	$array = array();
	foreach ($list as $key => $value) {
		$array[] = $key;
	}
	return $array;
}

function get_org() {
	$login_org = implode(',', session('login_org'));
	$uid = UID;
	if (empty($login_org) && !empty($uid)) {
		$login_org = M('organization_role')->where(array('uid' => $uid))->getField('organization_id');
	}
	return $login_org;

}
//获取部门ID(后勤部存在服务部门获取服务部门ID)
function get_service_org() {
	$company_id = get_CompanyId();
	$uid = UID;
	$model = M('organization');
	if ($uid) {
		$role_data = M('organization_role')->where(array('uid' => $uid))->find();
		$org_id = $role_data['organization_id'];
		if ($role_data['service_id']) {
			$org_id = $role_data['service_id'];
		}
	} else {
		$org_id = $model->where(array('company_id' => $company_id, 'status' => 1, 'type' => 1))->getField('id');
	}
	return $org_id;
}

function is_admin() {
	$_user = session('user_auth');
	return $_user['is_admin'];
}

// 用户名
function get_name() {
	$_user = isLogin();
	if ($_user['nickname']) {
		return $_user['nickname'];
	} else {
		return get_CompanyName();
	}

}

// 用户所在行政区
function get_dist() {
	$login_area = session('login_area');
	return isset($login_area[3]) ? $login_area[3] : 0;
}

// 用户所在商圈
function get_business() {
	$login_area = session('login_area');

	return isset($login_area[4]) ? $login_area[3] : 0;
}

// 用户所在街道
function get_street() {
	$login_area = session('login_area');

	return isset($login_area[5]) ? $login_area[3] : 0;

}

// 用户所在省市
function get_province() {
	return session('province');
}

// 用户所在职位
function get_job() {
	return end(session('login_job'));
}

// 用户所在职位名称
function get_job_name() {
	return end(session('login_job_text'));
}

//
function get_rule() {
	return session('login_rule');
}

function get_broker_rule() {
	return session('broker_rule');
}

// 权限判断

function is_rule($aciton = '') {
	return true;
	// 先判断是否加入权限表 没有加入就直接通过
	$Company = CAPI('/CApi/Company');
	$rule = $Company->is_add_rule($aciton);
	if ($rule) {
		if (is_admin()) {
			$rules = array('Houses/edit', 'Floors/edit', 'Units/unitsEdit', 'Units/del');
			foreach ($rules as $k => $v) {
				if ($aciton == $v) {
					return false;
				}
			}
			return true;
		} else {
			$rules = get_rule();
			return in_array($rule, $rules);
		}
	} else {
		return true;
	}

	// 加入权限表了，就把用户权限和传入的aciton 对比

}

function array_remove($arr, $value) {

	$key = array_search($value, $arr);
	if ($key !== false) {
		array_splice($arr, $key, 1);
	}

	return $arr;

}

/**
 * URL组装 支持不同URL模式
 * @param string $url URL表达式，格式：'[模块/控制器/操作#锚点@域名]?参数1=值1&参数2=值2...'
 * @param string|array $vars 传入的参数，支持数组和字符串
 * @param string|boolean $suffix 伪静态后缀，默认为true表示获取配置值
 * @param boolean $domain 是否显示域名
 * @return string
 */
function QXBU($url = '', $vars = '', $URL_MODEL = 0, $suffix = true, $domain = false) {
	// 解析URL
	$info = parse_url($url);
	$url = !empty($info['path']) ? $info['path'] : ACTION_NAME;
	if (isset($info['fragment'])) {
		// 解析锚点
		$anchor = $info['fragment'];
		if (false !== strpos($anchor, '?')) {
			// 解析参数
			list($anchor, $info['query']) = explode('?', $anchor, 2);
		}
		if (false !== strpos($anchor, '@')) {
			// 解析域名
			list($anchor, $host) = explode('@', $anchor, 2);
		}
	} elseif (false !== strpos($url, '@')) {
		// 解析域名
		list($url, $host) = explode('@', $info['path'], 2);
	}
	// 解析子域名
	if (isset($host)) {
		$domain = $host . (strpos($host, '.') ? '' : strstr($_SERVER['HTTP_HOST'], '.'));
	} elseif ($domain === true) {
		$domain = $_SERVER['HTTP_HOST'];
		if (C('APP_SUB_DOMAIN_DEPLOY')) {
			// 开启子域名部署
			$domain = $domain == 'localhost' ? 'localhost' : 'www' . strstr($_SERVER['HTTP_HOST'], '.');
			// '子域名'=>array('模块[/控制器]');
			foreach (C('APP_SUB_DOMAIN_RULES') as $key => $rule) {
				$rule = is_array($rule) ? $rule[0] : $rule;
				if (false === strpos($key, '*') && 0 === strpos($url, $rule)) {
					$domain = $key . strstr($domain, '.'); // 生成对应子域名
					$url = substr_replace($url, '', 0, strlen($rule));
					break;
				}
			}
		}
	}

	// 解析参数
	if (is_string($vars)) {
		// aaa=1&bbb=2 转换成数组
		parse_str($vars, $vars);
	} elseif (!is_array($vars)) {
		$vars = array();
	}
	if (isset($info['query'])) {
		// 解析地址里面参数 合并到vars
		parse_str($info['query'], $params);
		$vars = array_merge($params, $vars);
	}

	// URL组装
	$depr = C('URL_PATHINFO_DEPR');
	$urlCase = C('URL_CASE_INSENSITIVE');
	if ($url) {
		if (0 === strpos($url, '/')) {
// 定义路由
			$route = true;
			$url = substr($url, 1);
			if ('/' != $depr) {
				$url = str_replace('/', $depr, $url);
			}
		} else {
			if ('/' != $depr) {
				// 安全替换
				$url = str_replace('/', $depr, $url);
			}
			// 解析模块、控制器和操作
			$url = trim($url, $depr);
			$path = explode($depr, $url);
			$var = array();
			$varModule = C('VAR_MODULE');
			$varController = C('VAR_CONTROLLER');
			$varAction = C('VAR_ACTION');
			$var[$varAction] = !empty($path) ? array_pop($path) : ACTION_NAME;
			$var[$varController] = !empty($path) ? array_pop($path) : CONTROLLER_NAME;
			if ($maps = C('URL_ACTION_MAP')) {
				if (isset($maps[strtolower($var[$varController])])) {
					$maps = $maps[strtolower($var[$varController])];
					if ($action = array_search(strtolower($var[$varAction]), $maps)) {
						$var[$varAction] = $action;
					}
				}
			}
			if ($maps = C('URL_CONTROLLER_MAP')) {
				if ($controller = array_search(strtolower($var[$varController]), $maps)) {
					$var[$varController] = $controller;
				}
			}
			if ($urlCase) {
				$var[$varController] = parse_name($var[$varController]);
			}
			$module = '';

			if (!empty($path)) {
				$var[$varModule] = implode($depr, $path);
			} else {
				if (C('MULTI_MODULE')) {
					if (MODULE_NAME != C('DEFAULT_MODULE') || !C('MODULE_ALLOW_LIST')) {
						$var[$varModule] = MODULE_NAME;
					}
				}
			}
			if ($maps = C('URL_MODULE_MAP')) {
				if ($_module = array_search(strtolower($var[$varModule]), $maps)) {
					$var[$varModule] = $_module;
				}
			}
			if (isset($var[$varModule])) {
				$module = $var[$varModule];
				unset($var[$varModule]);
			}
		}
	}

	if ($URL_MODEL == 0 && C('URL_MODEL') == 0) {
		// 普通模式URL转换
		$url = __APP__ . '?' . C('VAR_MODULE') . "={$module}&" . http_build_query(array_reverse($var));
		if ($urlCase) {
			$url = strtolower($url);
		}
		if (!empty($vars)) {
			$vars = http_build_query($vars);
			$url .= '&' . $vars;
		}
	} else {
		// PATHINFO模式或者兼容URL模式

		if (isset($route)) {
			$url = __ROOT__ . '/' . rtrim($url, $depr);
		} else {
			$module = (defined('BIND_MODULE') && BIND_MODULE == $module) ? '' : $module;
			$url = __ROOT__ . '/' . ($module ? $module . MODULE_PATHINFO_DEPR : '') . implode($depr, array_reverse($var));
		}
		if ($urlCase) {
			$url = strtolower($url);
		}

		if (!empty($vars)) {
			// 添加参数
			foreach ($vars as $var => $val) {
				if ('' !== trim($val)) {
					$url .= $depr . $var . $depr . urlencode($val);
				}

			}
		}
		if ($suffix) {
			$suffix = $suffix === true ? C('URL_HTML_SUFFIX') : $suffix;
			if ($pos = strpos($suffix, '|')) {
				$suffix = substr($suffix, 0, $pos);
			}
			if ($suffix && '/' != substr($url, -1)) {
				$url .= '.' . ltrim($suffix, '.');
			}
		}
	}
	if (isset($anchor)) {
		$url .= '#' . $anchor;
	}
	if ($domain) {
		$url = (is_ssl() ? 'https://' : 'http://') . $domain . $url;
	}
	return $url;
}

function U1($url = '', $vars = '', $suffix = true, $domain = false) {
	$URL_MODEL = 1;
	return QXBU($url = '', $vars = '', $URL_MODEL, $suffix = true, $domain = false);
}

function fine_thumb($value, $url) {

	return [
		'thumb' => $value,
		'thumb_link' => $url,
	];
}

function fine_thumbs($datas, $thumb, $url) {
	$array = [];
	foreach ($datas as $key => $value) {
		$array[$key]['thumb'] = $value[$thumb];
		$array[$key]['thumb_link'] = $value[$url];
	}
	return $array;
}

function fine_thumbs_second($values, $urls) {
	$array = [];
	foreach ($values as $key => $value) {
		$array[$key]['thumb'] = $value;
	}
	foreach ($urls as $key => $value) {
		$array[$key]['thumb_link'] = $value;
	}

	return $array;
}

function get_org_title($uid) {
	$organization_id = M('organization_role')->where(['uid' => $uid])->getField('organization_id');
	if (empty($organization_id)) {
		return '';
	}
	$org_title = M('organization')->where(array('id' => $organization_id))->getField('title');
	return $org_title;
}

function get_job_title($uid) {
	$job_id = M('job_role')->where(['uid' => $uid])->getField('job_id');
	if (empty($job_id)) {
		return '';
	}
	$job_title = M('job')->where(array('id' => $job_id))->getField('title');
	return $job_title;
}

//拼装 组织架构
function combin_org_title($org_id) {
	$title = [];
	while (true) {
		$organization = M('organization')->where(array('id' => $org_id))->field('title,pid,level')->find();
		$title[] = $organization['title'];
		if ($organization['level'] == 2 || empty($organization['pid']) || $org_id == $organization['pid']) {
			break;
		}
		$org_id = $organization['pid'];
	}
	// print_r(implode(' — ', $title));
	krsort($title);
	return implode(' - ', $title);
}
//获取合同成交人部门ID
function get_deal_org_id($deed_id) {
	$model = M('deed_achievement_log');
	$org_id = $model->where(array('deed_id' => $deed_id, 'type' => 1))->getField('org_id');
	return $org_id;
}
//显示管理员文字
function get_manager_title() {
	$title = '管理员';
	return $title;
}
//是否有权限
function get_auth($name, $uid) {
	switch ($name) {
	case 'Deed/edit':
		$rules = get_rules('editDeed', $uid);
		if (in_array(1, $rules['type'])) {
			return 1;
		} else {
			return 0;
		}
		break;
	case 'Deed/lease_edit':
		$rules = get_rules('editDeed', $uid);
		if (in_array(2, $rules['type'])) {
			return 1;
		} else {
			return 0;
		}
		break;

	default:
		# code...
		break;
	}
	if (empty($uid)) {
		return 1;
	}
	$job_id = M('job_role')->where(array('uid' => $uid))->getField('job_id');
	if (empty($job_id)) {
		return 0;
	}
	$rule_id = M('auth_rule')->where(array('name' => $name, 'is_company' => 1))->getField('id');
	if (empty($rule_id)) {
		return 0;
	}
	$check = M('job_rule')->field('id')->where(array('job_id' => $job_id, 'rule_id' => $rule_id))->find();
	if ($check) {
		return 1;
	}
	return 0;
}