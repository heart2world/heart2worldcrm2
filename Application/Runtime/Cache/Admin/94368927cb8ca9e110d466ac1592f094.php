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



</head>
<body class="hold-transition skin-blue sidebar-mini">


    <div class="wrapper" style="padding-bottom:50px;">
        <div class="content-wrapper" style="margin-left: 0px;">
            <section class="content">
                <div class="row">
                    <div class="col-xs-12">
                        <div class="box box-primary">
                            <form method="post" action="<?php echo U('edit_fields');?>" class="form-tagging">
                                <div class="box-body">
                                    <div class="form-group">
                                        <label>字段类型</label>
                                        <select class="form-control select2" name="type" id="type">
                                            <?php if(is_array($type_data)): $i = 0; $__LIST__ = $type_data;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><option value="<?php echo ($vo['id']); ?>" <?php if(($vo['id']) == $info['type']): ?>selected<?php endif; ?> ><?php echo ($vo['title']); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label>字段分类</label>
                                        <select class="form-control select2" name="category_id">
                                            <?php if(is_array($category_data)): $i = 0; $__LIST__ = $category_data;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><option value="<?php echo ($vo['id']); ?>" <?php if(($vo['id']) == $info['category_id']): ?>selected<?php endif; ?> ><?php echo ($vo['title']); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label>字段名称</label>
                                        <input type="text" name="title" class="form-control" value="<?php echo ($info['title']); ?>" maxlength="20" placeholder="请输入字段名称">
                                    </div>
                                    <div class="form-group">
                                        <label>提示语</label>
                                        <input type="text" name="tips" class="form-control" value="<?php echo ($info['tips']); ?>" maxlength="20" placeholder="请输入提示语">
                                    </div>
                                    <div class="form-group">
                                        <label>是否启用</label>
                                        <div class="radio">
                                            <label style="margin-right: 50px;">
                                                <input type="radio" name="is_open" value="1" <?php if(($info['is_open']) == "1"): ?>checked<?php endif; ?> >是
                                            </label>
                                            <label>
                                                <input type="radio" name="is_open" value="2" <?php if(($info['is_open']) == "2"): ?>checked<?php endif; ?> >否
                                            </label>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label>是否必填</label>
                                        <div class="radio">
                                            <label style="margin-right: 50px;">
                                                <input type="radio" name="is_must" value="1" <?php if(($info['is_must']) == "1"): ?>checked<?php endif; ?> >是
                                            </label>
                                            <label>
                                                <input type="radio" name="is_must" value="2" <?php if(($info['is_must']) == "2"): ?>checked<?php endif; ?> >否
                                            </label>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label>排序序号</label>
                                        <input type="text" name="sort" class="form-control" value="<?php echo ($info['sort']); ?>" maxlength="4" placeholder="序号越小越靠前" onkeyup="this.value=this.value.replace(/\D/g,'')">
                                    </div>
                                    <div class="form-group" id="option_button_show" <?php if(!in_array(($info['type']), explode(',',"3,4"))): ?>style="display: none;"<?php endif; ?> >
                                        <button type="button" class="btn btn-primary" id="add_option">增加选择项</button>
                                    </div>
                                    <div id="option_show">
                                        <?php if(is_array($info['extra'])): $i = 0; $__LIST__ = $info['extra'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><div class="row option_fields">
                                                <div class="col-xs-7">
                                                    <input type="text" class="form-control" name="extra_data[title][]" value="<?php echo ((isset($vo['title']) && ($vo['title'] !== ""))?($vo['title']):''); ?>" maxlength="20" placeholder="请输入选择项">
                                                </div>
                                                <div class="col-xs-3">
                                                    <input type="text" class="form-control" name="extra_data[sort][]" value="<?php echo ((isset($vo['sort']) && ($vo['sort'] !== ""))?($vo['sort']):''); ?>" maxlength="4" placeholder="请输入排序" onkeyup="this.value=this.value.replace(/\D/g,'')">
                                                </div>
                                                <div class="col-xs-1" style="line-height: 34px;">
                                                    <a class="btn btn-danger btn-xs" onclick="del_option(this)">x</a>
                                                </div>
                                            </div><?php endforeach; endif; else: echo "" ;endif; ?>
                                    </div>
                                </div>
                                <div class="box-footer fixedBotton">
                                    <input type="hidden" name="id" value="<?php echo ($info['id']); ?>" />
                                    <button type="submit" class="btn btn-primary ajax-post" target_form="form-tagging">保存</button>
                                </div>
                            </form>
                            <div id="option_data" style="display: none;">
                                <div class="row option_fields">
                                    <div class="col-xs-7">
                                        <input type="text" class="form-control" name="extra_data[title][]" maxlength="20" placeholder="请输入选择项">
                                    </div>
                                    <div class="col-xs-3">
                                        <input type="text" class="form-control" name="extra_data[sort][]" maxlength="4" placeholder="请输入排序">
                                    </div>
                                    <div class="col-xs-1" style="line-height: 34px;">
                                        <a class="btn btn-danger btn-xs" onclick="del_option(this)">x</a>
                                    </div>
                                </div>
                            </div>
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
<script>
    $(function(){
        //选择字段类型
        $("#type").change(function(){
            var val = $(this).val();
            if(val == 3 || val == 4){
                $("#option_button_show").show();
            }else{
                $("#option_button_show").hide();
                $("#option_show").html('');
            }
        });
        //增加选项
        $("#add_option").click(function(){
            $("#option_show").append($("#option_data").html());
        });
        $('.select2').select2();
    });
    //删除选择
    function del_option(obj){
        $(obj).parents(".option_fields").remove();
    }
</script>


</body>
</html>