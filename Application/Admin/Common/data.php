<?php

// +----------------------------------------------------------------------
// | 破晓科技 [ 科技以人为本 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.ipoxiao.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: Everyday <fenghao@vip.qq.com> <http://www.ipoxiao.com>
// +----------------------------------------------------------------------

/**
 * 根据管理员ID获取管理员用户名称
 * @param  [type] $id [管理员ID]
 * @return [type]     [description]
 * @author duhaibo
 */
function get_manager_name($managerId) {
	return M('manager')->getFieldById($managerId, 'username');
}

/**
 * 获取楼盘责任经纪人名称
 * @param  [type] $id [管理员ID]
 * @return [type]     名称字符串,如:张三,李四
 * @author duhaibo
 */
function get_houses_broker_name($houses_id) {
	$tmp = M('v_houses_broker')->where(['target_id' => $houses_id])->getField('nickname', true);
	return $tmp ? implode(',', $tmp) : '';
}

/**
 * 获取楼盘责任经纪人名称
 * @param  [type] $id [管理员ID]
 * @return [type]     名称字符串,如:张三,李四
 * @author duhaibo
 */
function get_houses_broker($houses_id) {
	$tmp = M('houses_broker')->alias('b')
		->join('qxb_member_info as m ON b.uid=m.uid')
		->where(['b.houses_id' => $houses_id])
		->group('b.uid,houses_id,m.nickname')
		->select();
	$nickname = $tmp ? array_column($tmp, 'nickname') : [];
	return $nickname ? implode(',', $nickname) : '';
}

/**
 * 获取组织架构层级名称
 * @param  integer $id [当前层级ID]
 * @param  $level  [显示层级,0 表示全显示,大于0 表示从头开始隐藏级数]
 * @return [type]      [description]
 */
function get_org_level_name($orgNodeId = 0, $level = 0) {
	if (empty($orgNodeId)) {
		return '';
	}
	$name = [];
	_get_org_level_nodes($name, $orgNodeId, 'title');
	for ($i = 0; $i < $level; $i++) {
		array_shift($name);
	}
	return implode('-', $name);
}

//获取组织架构完整节点
function get_org_level_nodes($orgNodeId = 0) {
	$nodeIds = get_org_parent_nodes($orgNodeId);
	$childNodeIds = get_org_child_nodes($orgNodeId);
	if (!empty($childNodeIds)) {
		$nodeIds = array_merge($nodeIds, $childNodeIds);
	}
	return $nodeIds;
}

/**
 * 获取数组架构同上层级
 * @param  integer $orgNodeId [description]
 * @return [type]             [description]
 */
function get_org_parent_nodes($orgNodeId = 0) {
	if (empty($orgNodeId)) {
		return [];
	}
	$data = [];
	_get_org_level_nodes($data, $orgNodeId, 'id');
	return $data;
}

function get_org_child_nodes($orgNodeId = 0) {
	if (empty($orgNodeId)) {
		return [];
	}
	$data = [];
	_get_org_level_nodes($data, $orgNodeId, 'id', false);
	return $data;
}

/**
 * 获取数组架构向上层级私有方法
 * @param  array   &$nameArr [结果集数组]
 * @param  integer $nodeId   [当前查询节点]
 * @param  boolean $field    [返回字段，为false返回对象]
 * @return [type]            [description]
 */
function _get_org_level_nodes(&$nameArr = [], $nodeId = 0, $field = false, $toword = true) {
	$list = get_org_data();
	foreach ($list as $data) {
		if ($data[$toword ? 'id' : 'pid'] == $nodeId) {
			array_unshift($nameArr, $field === false ? $data : $data[$field]);
			_get_org_level_nodes($nameArr, $data[$toword ? 'pid' : 'id'], $field, $toword);
		}
	}
}

function search_org_list_org_name($orgNodeIds = []) {
	if (empty($orgNodeIds)) {
		return '';
	}
	$name = [];
	foreach ($orgNodeIds as $nodeId) {
		$name[] = get_org_level_name($nodeId, 1);
	}
	return implode('<br/>', $name);
}

/**
 * 获取组织架构下级节点
 * @param  [type]  $data [description]
 * @param  integer $pid  [description]
 * @return [type]        [description]
 */
function get_org_childs($data, $pid = 0) {
	static $res = [];
	foreach ($data as $value) {
		if ($value['pid'] == $pid) {
			array_push($res, $value['id']);
			get_org_childs($data, $value['id']);
		}
	}
	return $res;
}

/**
 * 获取菜单树（html）
 * @param type $pid
 * @param type $id
 * @param type $table
 * @param type $ispid 判断是否只返回一级菜单
 * @return type
 */
function getMenuTreeHtml($pid = 0, $nodeId = 0, $table = "auth_rule", $ispid = false) {
	$map = ['id' => ['neq', $nodeId]];
	$ispid && $map['pid'] = 0;
	$map['type'] = 4;
	$menu = M($table)->where($map)->field('id,title,pid')->select();
	$tree = " <option value=\"0\">顶级菜单</option>";
	foreach ($menu as $v) {
		if ($v['pid'] == 0) {
			if ($ispid) {
				$tree .= "<option value=\"" . $v['id'] . "\" ";
				if ($v['id'] == $pid) {
					$tree .= "selected=\"selected\"";
				}
				$tree .= " >" . $v['title'] . "</option>";
				continue;
			}
			$cate_prent = new \Common\Library\Cate\Tree($menu);
			$tree .= "<option value=\"" . $v['id'] . "\" ";
			if ($v['id'] == $pid) {
				$tree .= "selected=\"selected\"";
			}
			$tree .= " >" . $v['title'] . "</option>";
			$tree .= $cate_prent->get_tree($v['id'], "<option value=\$id \$selected>\$spacer\$title</option>", $pid);
		}
	}
	return $tree;
}
