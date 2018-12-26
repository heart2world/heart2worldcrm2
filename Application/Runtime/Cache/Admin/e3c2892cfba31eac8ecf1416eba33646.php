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


    <div class="wrapper" style="padding-bottom:50px;">
        <div class="content-wrapper" style="margin-left: 0px;">
            <section class="content">
                <div class="row">
                    <div class="col-xs-12">
                        <div class="box box-primary">
                            <form method="post" action="<?php echo U('add_follow');?>" class="form-tagging">
                                <div class="box-body">
                                    <div class="form-group">
                                        <label>跟进方式</label>
                                        <select class="form-control select2" name="type">
                                            <?php if(is_array($configs['follow_type'])): $i = 0; $__LIST__ = $configs['follow_type'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><option value="<?php echo ($vo['id']); ?>"><?php echo ($vo['title']); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label>跟进时间</label>
                                        <div class="input-group date">
                                            <div class="input-group-addon">
                                                <i class="fa fa-calendar"></i>
                                            </div>
                                            <input type="text" class="form-control pull-right" name="follow_time" placeholder="请输入跟进时间" readonly style="cursor: pointer;" id="follow_time">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label>跟进详情</label>
                                        <textarea class="form-control" name="content" rows="3" maxlength="200" placeholder="请输入跟进详情"><?php echo ($info['content']); ?></textarea>
                                    </div>
                                    <div class="form-group">
                                        <label>图片上传</label>
                                        <div class="upfile btn btn-primary upload">请上传</div>
                                        <div class="picBox">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label>跟进状态</label>
                                        <select class="form-control select2" name="status">
                                            <?php if(is_array($configs['follow_status'])): $i = 0; $__LIST__ = $configs['follow_status'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><option value="<?php echo ($vo['id']); ?>" <?php if(($vo['id']) == $configs['status']): ?>selected<?php endif; ?> ><?php echo ($vo['title']); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label>下次跟进时间</label>
                                        <div class="input-group date">
                                            <div class="input-group-addon">
                                                <i class="fa fa-calendar"></i>
                                            </div>
                                            <input type="text" class="form-control pull-right" name="next_follow_time" placeholder="请输入下次跟进时间" readonly style="cursor: pointer;" id="next_follow_time">
                                        </div>
                                    </div>
                                </div>
                                <div class="box-footer fixedBotton">
                                    <input type="hidden" name="customer_id" value="<?php echo ($_GET['customer_id']); ?>" />
                                    <button type="submit" class="btn btn-primary ajax-post-new" target_form="form-tagging" jump_type="1">保存</button>
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
    <!--laydate-->
    <script src="/Public/laydate/laydate.js"></script>
    <script src="/Public/js/layui.js"></script>
    <script>
        layui.use('upload', function(){
            var $ = layui.jquery ,upload = layui.upload;
            //附件上传
            upload.render({
                elem: '.upload',
                multiple: true,
                url: "<?php echo U('Files/Image/upload');?>",
                done: function(res){
                    if(res.code == 200){
                        var html = '<div class="pic">';
                        html += '<div><img style="width: 100%;height: 100%;" src="' + res.path + '" /><i class=" fa fa-times" onclick="file_del(this);"></i></div>';
                        html += '<p>'+res.title+'</p>';
                        html += '<input type="hidden" name="pics[]" value="'+res.data+'" />';
                        html += '</div>';
                        $(".picBox").append(html);
                    }else{
                        return layer.msg('上传失败');
                    }
                },
                error: function(){
                    return layer.msg('上传失败');
                }
            });
        });
        //图片删除
        function file_del(obj){
            $(obj).parents(".pic").remove();
        }
        laydate.render({
            elem: '#follow_time',
            type:'datetime',
            theme: '#3c8dbc',
            trigger: 'click',
            max: "<?php echo date('Y-m-d H:i:s');?>",
            value:"<?php echo date('Y-m-d H:i:s');?>"
        });
        laydate.render({
            elem: '#next_follow_time',
            type:'datetime',
            theme: '#3c8dbc',
            trigger: 'click',
            min: "<?php echo date('Y-m-d H:i:s');?>",
            value:"<?php echo date('Y-m-d H:i:s',strtotime('+1 day'));?>"
        });
        $('.select2').select2();

        /**
         * post提交
         */
        $(".ajax-post-new").click(function(){
            var obj = this;
            var target_form = $(this).attr("target_form");
            var into_type = "<?php echo I('get.into_type');?>";
            var url = $("."+target_form).attr("action");
            var data = $("."+target_form).serialize();
            var con = $(window.parent.document);
            $(obj).attr("disabled",true);
            $.ajax({
                type:"post",
                url:url,
                data:data,
                success:function(data){
                    if(data.status == 1){
                        layer.msg(data.info, {icon: 1,time:1500},function(){
                            if(into_type == 1){
                                con.find("#search").click();
                                layer_close();
                            }else{
                                if(data.url){
                                    window.location.href = data.url;
                                }else{
                                    window.parent.location.reload();
                                    //window.location.href = document.referrer;
                                }
                            }
                        });
                    }else{
                        layer.msg(data.info, {icon: 2,time:1500});
                        $(obj).attr("disabled",false);
                    }
                }
            });
            return false;
        });
    </script>


</body>
</html>