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


    <div class="wrapper" style="">
        <div class="content-wrapper" style="margin-left: 0px;">
            <section class="content">
                <div class="row">
                    <div class="col-xs-12">
                        <div class="box box-primary">
                            <form method="post" action="<?php echo U('edit_users');?>" class="form-tagging">
                                <div class="box-body">
                                    <div class="form-group">
                                        <label>姓名</label>
                                        <input type="text" name="name" class="form-control" value="<?php echo ($info['name']); ?>" maxlength="20" placeholder="请输入角色名称">
                                    </div>
                                    <div class="form-group">
                                        <label>手机号</label>
                                        <input type="text" name="mobile" class="form-control" value="<?php echo ($info['mobile']); ?>" maxlength="11" placeholder="请输入手机号">
                                    </div>
                                    <div class="form-group">
                                        <label>登录账号</label>
                                        <input type="text" name="login_name" class="form-control" value="<?php echo ($info['login_name']); ?>" maxlength="20" placeholder="请输入登录账号">
                                    </div>
                                    <div class="form-group">
                                        <label>所属角色</label>
                                        <select class="form-control select2" name="role_id" style="width:100%">
                                            <?php if(is_array($role_data)): $i = 0; $__LIST__ = $role_data;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><option value="<?php echo ($vo['id']); ?>" <?php if(($vo['id']) == $info['role_id']): ?>selected<?php endif; ?> ><?php echo ($vo['name']); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
                                        </select>
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
    <script>
        $('.select2').select2();
    </script>


</body>
</html>