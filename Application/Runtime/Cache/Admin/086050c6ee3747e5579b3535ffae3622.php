<?php if (!defined('THINK_PATH')) exit(); if(is_array($info['lists'])): $i = 0; $__LIST__ = $info['lists'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><tr>
        <td align="center" style="white-space: nowrap;"><input type="checkbox" name="ids[]" class="ids" value="<?php echo ($vo['id']); ?>"></td>
        <?php if(is_array($vo['data'])): $i = 0; $__LIST__ = $vo['data'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$item): $mod = ($i % 2 );++$i;?><td style="white-space: nowrap;" title="<?php echo ((isset($item) && ($item !== ""))?($item):''); ?>">
                <?php if(mb_strlen($item,'UTF8') > 16){ echo mb_substr($item,0,16,'utf-8').'...'; }else{ echo $item; } ?>
            </td><?php endforeach; endif; else: echo "" ;endif; ?>
        <td style="white-space: nowrap;">
            <a class="btn btn-info btn-xs" onclick="pop_show(this)" url="<?php echo U('edit',array('id'=>$vo['id']));?>" title="编辑客户" w="600" h="500">编辑</a>
            <a class="btn btn-info btn-xs" onclick="pop_show(this)" url="<?php echo U('add_follow',array('customer_id'=>$vo['id'],'into_type'=>1));?>" title="写跟进" w="600" h="500">写跟进</a>
            <a class="btn btn-info btn-xs" href="<?php echo U('info',array('id'=>$vo['id']));?>" target="_blank" title="客户详情" w="600" h="500">详情</a>
        </td>
    </tr><?php endforeach; endif; else: echo "" ;endif; ?>