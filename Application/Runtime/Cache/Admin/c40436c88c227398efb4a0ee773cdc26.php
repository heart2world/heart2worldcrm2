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
                            <form method="post" action="<?php echo U('');?>" class="form-tagging" autocomplete="off">
                                <div class="box-body">
                                    <div class="form-group">
                                        <label>姓名&nbsp;&nbsp;&nbsp;&nbsp;</label><?php echo ($_GET['name']); ?>
                                    </div>
                                    <div class="form-group">
                                        <label>新密码</label>
                                        <input type="password" name="password" class="form-control" maxlength="20" placeholder="请输入新密码" autocomplete="off">
                                    </div>
                                    <div class="form-group">
                                        <label>确认新密码</label>
                                        <input type="password" name="repassword" class="form-control" maxlength="20" placeholder="请输入确认新密码" autocomplete="off">
                                    </div>
                                </div>
                                <div class="box-footer fixedBotton">
                                    <input type="hidden" name="id" value="<?php echo ($_GET['id']); ?>" />
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



</body>
</html>