<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE HTML>
<html>
<head>

<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<title>客户管理系统</title>
<!-- Tell the browser to be responsive to screen width -->
<meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
<!-- Bootstrap 3.3.7 -->
<link rel="stylesheet" href="/Public/css/bootstrap.min.css">
<!-- Font Awesome -->
<link rel="stylesheet" href="/Public/css/font-awesome.min.css">
<!-- Ionicons -->
<link rel="stylesheet" href="/Public/css/ionicons.min.css">
<!-- Select2 -->
<link rel="stylesheet" href="/Public/css/select2.min.css">
<!-- Theme style -->
<link rel="stylesheet" href="/Public/css/AdminLTE.min.css">
<!-- AdminLTE Skins. Choose a skin from the css/skins
     folder instead of downloading all of them to reduce the load. -->
<link rel="stylesheet" href="/Public/css/_all-skins.min.css">

<!-- Google Font -->
<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">


    <link rel="stylesheet" href="/Public/css/layui.css">
    <style>
        .upfile{position: relative;overflow: hidden;}
        .upfile>input{position: absolute;top:0;left:0;right: 0;bottom:0;opacity: 0;}
        .picBox{overflow: hidden;}
        .picBox>div{float: left;margin: 10px;}
        .picBox>div>div{position: relative;width: 100px;height: 100px;border: 1px solid #eee; -webkit-border-radius:5px;-moz-border-radius:5px;border-radius:5px;text-align: center;vertical-align: middle;}
        .picBox>div>p{text-align: center;}
        .picBox>div>div>i{position: absolute;right: -5px;top:-5px;display: inline-block;width: 15px;height:15px;-webkit-border-radius:50%;-moz-border-radius:50%;border-radius:50%;background: #ccc;color:#fff;text-align: center;}
        .picBox>div.file .filepic{font-size: 50px;line-height: 100px;color: #3c8dbc;}
    </style>

</head>
<body class="hold-transition skin-blue sidebar-mini">


    <div class="wrapper" style="padding-bottom:70px;background:#ecf0f5;">
        <div class="content-wrapper" style="margin-left: 0px;">
            <section class="content">
                <div class="row">
                    <div class="col-xs-12">
                        <div class="box box-primary">
                            <form method="post" action="<?php echo U('Index/edit');?>" class="form-tagging">
                                <div class="box-body">
                                    <div class="form-group">
                                        <label><font color="#f00">*</font>客户名称</label>
                                        <input type="text" name="name" class="form-control" value="<?php echo ($info['name']); ?>" maxlength="20" placeholder="请输入客户名称">
                                    </div>
                                    <div class="form-group">
                                        <label><font color="#f00">*</font>手机号</label>
                                        <input type="text" name="mobile" class="form-control" value="<?php echo ($info['mobile']); ?>" maxlength="11" placeholder="请输入手机号" onblur="checkInter(this);">
                                    </div>
                                    <!--自定义字段-->
                                    <?php if(is_array($configs['fields_data'])): $i = 0; $__LIST__ = $configs['fields_data'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i; switch($vo['type']): case "1": ?><!--单行文本-->
                                                <div class="form-group">
                                                    <label><?php if(($vo['is_must']) == "1"): ?><font color="#f00">*</font><?php endif; echo ($vo['title']); ?></label>
                                                    <input type="text" name="fields_data[<?php echo ($vo['id']); ?>]" class="form-control" value="<?php echo ($info[$vo['id']]); ?>" maxlength="20" placeholder="<?php echo ((isset($vo['tips']) && ($vo['tips'] !== ""))?($vo['tips']):''); ?>">
                                                </div><?php break;?>
                                            <?php case "2": ?><!--多行文本-->
                                                <div class="form-group">
                                                    <label><?php if(($vo['is_must']) == "1"): ?><font color="#f00">*</font><?php endif; echo ($vo['title']); ?></label>
                                                    <textarea class="form-control" name="fields_data[<?php echo ($vo['id']); ?>]" rows="3" maxlength="200" placeholder="<?php echo ((isset($vo['tips']) && ($vo['tips'] !== ""))?($vo['tips']):''); ?>"><?php echo ($info[$vo['id']]); ?></textarea>
                                                </div><?php break;?>
                                            <?php case "3": ?><!--单选下拉列表-->
                                                <div class="form-group">
                                                    <label><?php if(($vo['is_must']) == "1"): ?><font color="#f00">*</font><?php endif; echo ($vo['title']); ?></label>
                                                    <select class="form-control select2" name="fields_data[<?php echo ($vo['id']); ?>]">
                                                        <?php if(is_array($vo['extra'])): $i = 0; $__LIST__ = $vo['extra'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$item): $mod = ($i % 2 );++$i;?><option value="<?php echo ($item['title']); ?>" <?php if(($item['title']) == $info[$vo['id']]): ?>selected<?php endif; ?> ><?php echo ($item['title']); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
                                                    </select>
                                                </div><?php break;?>
                                            <?php case "4": ?><!--多选下拉列表-->
                                                <div class="form-group">
                                                    <label><?php if(($vo['is_must']) == "1"): ?><font color="#f00">*</font><?php endif; echo ($vo['title']); ?></label>
                                                    <div class="checkbox">
                                                        <?php if(is_array($vo['extra'])): $i = 0; $__LIST__ = $vo['extra'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$item): $mod = ($i % 2 );++$i;?><label style="margin-right: 50px;">
                                                                <input type="checkbox" name="fields_data[<?php echo ($vo['id']); ?>][]" value="<?php echo ($item['title']); ?>" <?php if(in_array(($item['title']), is_array($info[$vo['id']])?$info[$vo['id']]:explode(',',$info[$vo['id']]))): ?>checked<?php endif; ?> ><?php echo ($item['title']); ?>
                                                            </label><?php endforeach; endif; else: echo "" ;endif; ?>
                                                    </div>
                                                </div><?php break;?>
                                            <?php case "5": ?><!--数字-->
                                                <div class="form-group">
                                                    <label><?php if(($vo['is_must']) == "1"): ?><font color="#f00">*</font><?php endif; echo ($vo['title']); ?></label>
                                                    <input type="text" name="fields_data[<?php echo ($vo['id']); ?>]" class="form-control" value="<?php echo ($info[$vo['id']]); ?>" maxlength="20" placeholder="<?php echo ((isset($vo['tips']) && ($vo['tips'] !== ""))?($vo['tips']):''); ?>" onblur="checkNum(this);">
                                                </div><?php break;?>
                                            <?php case "6": ?><!--图片-->
                                                <div class="form-group">
                                                    <label><?php if(($vo['is_must']) == "1"): ?><font color="#f00">*</font><?php endif; echo ($vo['title']); ?></label>
                                                    <div class="upfile btn btn-primary upload_pic_<?php echo ($vo['id']); ?>">请上传</div>
                                                    <div class="picBox picBox_<?php echo ($vo['id']); ?>">
                                                        <?php if(is_array($info[$vo['id']])): $i = 0; $__LIST__ = $info[$vo['id']];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$item): $mod = ($i % 2 );++$i;?><div class="pic">
                                                                <div><img style="width: 100%;height: 100%;" src="<?php echo getFileUrl($item);?>" /><i class="fa fa-times" onclick="pic_del(this);"></i></div>
                                                                <input type="hidden" name="fields_data[<?php echo ($vo['id']); ?>][]" value="<?php echo ($item); ?>" />
                                                            </div><?php endforeach; endif; else: echo "" ;endif; ?>
                                                    </div>
                                                </div><?php break;?>
                                            <?php case "7": ?><!--时间-->
                                                <div class="form-group">
                                                    <label><?php if(($vo['is_must']) == "1"): ?><font color="#f00">*</font><?php endif; echo ($vo['title']); ?></label>
                                                    <div class="input-group date">
                                                        <div class="input-group-addon">
                                                            <i class="fa fa-calendar"></i>
                                                        </div>
                                                        <input type="text" class="form-control pull-right" name="fields_data[<?php echo ($vo['id']); ?>]" value="<?php echo ($info[$vo['id']]); ?>" placeholder="<?php echo ((isset($vo['tips']) && ($vo['tips'] !== ""))?($vo['tips']):''); ?>" readonly style="cursor: pointer;" id="time_<?php echo ($vo['id']); ?>">
                                                    </div>
                                                </div><?php break; endswitch; endforeach; endif; else: echo "" ;endif; ?>


                                    <div class="form-group">
                                        <label><font color="#f00">*</font>客户类型</label>
                                        <select class="form-control select2" name="type">
                                            <?php if(is_array($configs['customer_type'])): $i = 0; $__LIST__ = $configs['customer_type'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><option value="<?php echo ($vo['id']); ?>" <?php if(($vo['id']) == $info['type']): ?>selected<?php endif; ?> ><?php echo ($vo['title']); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label><font color="#f00">*</font>跟进状态</label>
                                        <select class="form-control select2" name="follow_status">
                                            <?php if(is_array($configs['follow_status'])): $i = 0; $__LIST__ = $configs['follow_status'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><option value="<?php echo ($vo['id']); ?>" <?php if(($vo['id']) == $info['follow_status']): ?>selected<?php endif; ?> ><?php echo ($vo['title']); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label><font color="#f00">*</font>下次跟进时间</label>
                                        <div class="input-group date">
                                            <div class="input-group-addon">
                                                <i class="fa fa-calendar"></i>
                                            </div>
                                            <input type="text" class="form-control pull-right" name="next_follow_time" value="<?php echo ($info['next_follow_time']); ?>" placeholder="请输入下次跟进时间" readonly style="cursor: pointer;" id="next_follow_time">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label><font color="#f00">*</font>负责人</label>
                                        <select class="form-control select2" name="person_id">
                                            <?php if(is_array($configs['person'])): $i = 0; $__LIST__ = $configs['person'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><option value="<?php echo ($vo['id']); ?>" <?php if(($vo['id']) == $info['person_id']): ?>selected<?php endif; ?> ><?php echo ($vo['title']); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label>附件上传</label>
                                        <div class="upfile btn btn-primary upload">请上传</div>
                                        <div class="picBox picBox_file">
                                            <!--<div class="pic">-->
                                                <!--<div><img /><i class="fa fa-times"></i></div>-->
                                                <!--<p>名称1111</p>-->
                                            <!--</div>-->
                                            <?php if(is_array($info['file_data'])): $i = 0; $__LIST__ = $info['file_data'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><div class="file">
                                                <div><span class="fa fa-file-text-o filepic"></span><i class="fa fa-times" onclick="file_del(this);"></i></div>
                                                <input type="hidden" name="file_data[]" value="<?php echo ($vo['sha1']); ?>" />
                                                <p><?php echo ($vo['name']); ?></p>
                                            </div><?php endforeach; endif; else: echo "" ;endif; ?>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label><font color="#f00">*</font>备注信息</label>
                                        <textarea class="form-control" name="remark" rows="3" maxlength="200" placeholder="请输入备注信息"><?php echo ($info['remark']); ?></textarea>
                                    </div>
                                </div>
                                <div class="box-footer fixedBotton">
                                    <input type="hidden" name="id" value="<?php echo ($info['id']); ?>" />
                                    <button type="submit" class="btn btn-primary ajax-post" target_form="form-tagging">保存</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>


<!-- jQuery 3 -->
<script src="/Public/js/jquery.min.js"></script>
<!-- Bootstrap 3.3.7 -->
<script src="/Public/js/bootstrap.min.js"></script>
<!-- AdminLTE App -->
<script src="/Public/js/adminlte.min.js"></script>

<script src="/Public/layer/layer.js"></script>
<script src="/Public/js/common.js?r=<?php echo NOW_TIME;?>"></script>

    <!-- Select2 -->
    <script src="/Public/js/select2.full.min.js"></script>
    <script src="/Public/laydate/laydate.js"></script>
    <script src="/Public/js/layui.js"></script>
    <?php if(is_array($configs['fields_data'])): $i = 0; $__LIST__ = $configs['fields_data'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i; switch($vo['type']): case "6": ?><script>
                    layui.use('upload', function(){
                        var $ = layui.jquery ,upload = layui.upload;
                        //附件上传
                        upload.render({
                            elem: ".upload_pic_<?php echo ($vo['id']); ?>",
                            multiple: true,
                            url: "<?php echo U('Files/Image/upload');?>",
                            done: function(res){
                                if(res.code == 200){
                                    var html = '<div class="pic">';
                                    html += '<div><img style="width: 100%;height: 100%;" src="' + res.path + '" /><i class=" fa fa-times" onclick="pic_del(this);"></i></div>';
                                    html += '<p>'+res.title+'</p>';
                                    html += '<input type="hidden" name="fields_data['+<?php echo ($vo['id']); ?>+'][]" value="'+res.data+'" />';
                                    html += '</div>';
                                    $(".picBox_<?php echo ($vo['id']); ?>").append(html);
                                }else{
                                    return layer.msg('上传失败');
                                }
                            },
                            error: function(){
                                return layer.msg('上传失败');
                            }
                        });
                    });
                </script><?php break; endswitch; endforeach; endif; else: echo "" ;endif; ?>
    <script>
        layui.use('upload', function(){
            var $ = layui.jquery ,upload = layui.upload;
            //附件上传
            upload.render({
                elem: '.upload',
                multiple: true,
                accept: 'file',
                url: "<?php echo U('Files/File/upload');?>",
                done: function(res){
                    if(res.code == 200){
                        var html = '<div class="file">';
                        html += '<div><span class="fa fa-file-text-o filepic"></span><i class="fa fa-times" onclick="file_del(this);"></i></div>';
                        html += '<p>'+res.title+'</p>';
                        html += '<input type="hidden" name="file_data[]" value="'+res.data+'" />';
                        html += '</div>';
                        $(".picBox_file").append(html);
                    }else{
                        return layer.msg('上传失败');
                    }
                },
                error: function(){
                    return layer.msg('上传失败');
                }
            });
        });
        //自定义时间
        var time_data = <?php echo ($configs['time_ids']); ?>;
        for(var i in time_data){
            laydate.render({
                elem: '#time_'+time_data[i],
                type:'datetime',
                theme: '#3c8dbc',
                trigger: 'click',
            });
        }
        //下次跟进时间
        laydate.render({
            elem: '#next_follow_time',
            type:'datetime',
            theme: '#3c8dbc',
            trigger: 'click',
            min: "<?php echo date('Y-m-d H:i:s');?>",
        });
        //文件删除
        function file_del(obj){
            $(obj).parents(".file").remove();
        }
        //图片删除
        function pic_del(obj){
            $(obj).parents(".pic").remove();
        }
        //下拉样式
        $('.select2').select2();
    </script>


</body>
</html>