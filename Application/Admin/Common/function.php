<?php

// +----------------------------------------------------------------------
// | 破晓科技 [ 科技以人为本 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.ipoxiao.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: Everyday <fenghao@vip.qq.com> <http://www.ipoxiao.com>
// +----------------------------------------------------------------------

/**
 * 检测当前用户是否为管理员
 * @return boolean true-管理员，false-非管理员
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
function is_administrator($uid = null) {
    $uid = is_null($uid) ? is_login()['id'] : $uid;
    return $uid && (intval($uid) === C('USER_ADMINISTRATOR'));
}

/**
 * 获取菜单树（html）
 * @param type $pid
 * @param type $id
 * @param type $table
 * @param type $ispid 判断是否只返回一级菜单
 * @return type
 */
function getMenu($pid = 0, $id = 0, $table = "auth_rule", $ispid = false) {
    $map = ['id' => ['neq', $id]];
    $ispid && $map['pid'] = 0;
    $map['type'] = ['lt', 3];
    $menu = M($table)->where($map)->field('id,title,pid')->select();
    $tree = " <option value=\"0\">顶级菜单</option>";
    foreach ($menu as $k => $v) {
        if ($v['pid'] == 0) {
            if ($ispid) {
                $tree.="<option value=\"" . $v['id'] . "\" ";
                if ($v['id'] == $pid) {
                    $tree.="selected=\"selected\"";
                }
                $tree.=" >" . $v['title'] . "</option>";
                continue;
            }
            $cate_prent = new \Common\Library\Cate\Tree($menu );
            $tree.="<option value=\"" . $v['id'] . "\" ";
            if ($v['id'] == $pid) {
                $tree.="selected=\"selected\"";
            }
            $tree.=" >" . $v['title'] . "</option>";
            $tree.=$cate_prent->get_tree($v['id'], "<option value=\$id \$selected>\$spacer\$title</option>", $pid);
        }
    }
    return $tree;
}

/**
 * 获取分类树（html）
 */
function getCategoryTree($map, $pid = 0) {
    $category = M('category')->where($map)->field('id,title,pid')->select();
    $tree = " <option value=\"0\">顶级分类</option>";
    foreach ($category as $k => $v) {
        if ($v['pid'] == 0) {
            $cate_prent = new \Common\Library\Cate\Tree($category);
            $tree.="<option value=\"" . $v['id'] . "\" ";
            if ($v['id'] == $pid) {
                $tree.="selected=\"selected\"";
            }
            $tree.=" >" . $v['title'] . "</option>";
            $tree.=$cate_prent->get_tree($v['id'], "<option value=\$id \$selected>\$spacer\$title</option>", $pid);
        }
    }
    return $tree;
}

/**
 * 数据导出格式化
 * @param type $data
 * @param type $columns
 * @return type
 */
function exportPar($data, $columns) {
    if (!isset($columns[2])) {
        return $data;
    }
    switch ($columns[2]) {
        case 'fun':
            $args = $data[$columns[0]];
            if (!is_array($args)) {
                $args = [$args];
            }
            $data[$columns[0]] = call_user_func_array($columns[3], $args);
            break;
        case 'date':
            $format = 'Y-m-d H:i:s';
            if (isset($columns[3])) {
                $format = $columns[3];
            }
            $data[$columns[0]] = date($format, $data[$columns[0]]);
            break;
        case 'array':
            $data[$columns[0]] = $columns[3][$data[$columns[0]]];
            break;
        case 'bool':
            $data[$columns[0]] = $data[$columns[0]] == 0 ? '是' : '否';
            break;
    }
    return $data;
}

/**
 * 获取角色列表
 */
function getAuthGroup() {
    $lists = M('AuthGroup')->field('id,title')->select();
    return $lists;
}

/**
 * 返回地区全部父级所有ID
 */
function getParentAreaId( $id ,$data = array(), $i = 3) {
    $fields = array('prov' , 'city' , 'dist' , 'circle');
    $area_model = M('city');
    $area_info = $area_model->where(array('id' => $id))->field('id,pid')->find();
    if( $area_info ) {
        $data[$fields[$i]] = $area_info['id'];
        if( $area_info['pid'] > 0 ) {
            $i--;
            $data = getParentAreaId( $area_info['pid'] ,$data , $i );
        }
    }
    return $data;
}
//二维数组去掉重复值 并保留键值
function array_unique_fb($array2D)
{
    foreach ($array2D as $k=>$v)
    {
        $v = join(",",$v); //降维,也可以用implode,将一维数组转换为用逗号连接的字符串
        $temp[$k] = $v;
    }
    $temp = array_unique($temp); //去掉重复的字符串,也就是重复的一维数组
    foreach ($temp as $k => $v)
    {
        $array=explode(",",$v); //再将拆开的数组重新组装
        $temp2[$k]["owner_name"] =$array[0];
        $temp2[$k]["owner_tel"] =$array[1];
        $temp2[$k]["spouse_name"] =$array[2];
        $temp2[$k]["spouse_tel"] =$array[3];
        $temp2[$k]["house_id"] =$array[4];
    }
    return $temp2;
}