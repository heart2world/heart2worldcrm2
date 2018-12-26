<?php if (!defined('THINK_PATH')) exit(); if(is_array($info['lists'])): $i = 0; $__LIST__ = $info['lists'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><tr url="<?php echo U('index/info',array('id'=>$vo['id']));?>">
        <td align="center" style="white-space: nowrap;"><input type="checkbox" name="ids[]" class="ids" value="<?php echo ($vo['id']); ?>"></td>
        <?php if(is_array($vo['data'])): $i = 0; $__LIST__ = $vo['data'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$item): $mod = ($i % 2 );++$i;?><td style="white-space: nowrap;" title="<?php echo ((isset($item) && ($item !== ""))?($item):''); ?>">
                <?php if(mb_strlen($item,'UTF8') > 16){ echo mb_substr($item,0,16,'utf-8').'...'; }else{ echo $item; } ?>
            </td><?php endforeach; endif; else: echo "" ;endif; ?>
        <td style="white-space: nowrap; text-align: center;">
            <a class="btn btn-info btn-xs" onclick="ajax_get(this)" url="<?php echo U('grab',array('id'=>$vo['id']));?>" title="抢客户" msg='确定"抢到"该客户？'>抢</a>
        </td>
    </tr><?php endforeach; endif; else: echo "" ;endif; ?>