<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>登录</title>
  <!-- Tell the browser to be responsive to screen width -->
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <!-- Bootstrap 3.3.7 -->
  <link rel="stylesheet" href="/Public/css/bootstrap.min.css">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="/Public/css/font-awesome.min.css">
  <!-- Ionicons -->
  <link rel="stylesheet" href="/Public/css/ionicons.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="/Public/css/AdminLTE.min.css">
  <!-- iCheck -->
  <link rel="stylesheet" href="/Public/css/blue.css">
</head>
<body class="hold-transition login-page">
<div class="login-box">
  <div class="login-logo">
    <b>心联宇客户管理系统</b>
  </div>
  <!-- /.login-logo -->
  <div class="login-box-body">
    <p class="login-box-msg"></p>

    <form action="<?php echo U('Public/login');?>" class="form-tagging" method="post">
      <div class="form-group has-feedback">
        <input type="text" name="login_name" class="form-control" placeholder="账号">
        <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
      </div>
      <div class="form-group has-feedback">
        <input type="password" class="form-control" name="password" placeholder="密码">
        <span class="glyphicon glyphicon-lock form-control-feedback"></span>
      </div>
      <div class="form-group has-feedback">
        <input type="text" class="form-control" name="code" placeholder="验证码" autocomplete="off">
        <span class="form-control-feedback" style="width: 100px;"></span>
        <img style="cursor: pointer;" src="<?php echo U('Public/verify_code');?>" id="verify" title="获取验证码" />
      </div>

      <div class="row">
        <!-- /.col -->
        <div class="col-xs-12">
          <button type="submit" class="btn btn-primary btn-block btn-flat ajax-post" target_form="form-tagging">登录</button>
        </div>
        <!-- /.col -->
      </div>
    </form>

    <!--<div class="social-auth-links text-center">-->
      <!--<p>- OR -</p>-->
      <!--<a href="#" class="btn btn-block btn-social btn-facebook btn-flat">登录</a>-->
      <!--<a href="#" class="btn btn-block btn-social btn-google btn-flat"><i class="fa fa-google-plus"></i> Sign in using-->
        <!--Google+</a>-->
    <!--</div>-->
    <!--&lt;!&ndash; /.social-auth-links &ndash;&gt;-->

    <!--<a href="#">I forgot my password</a><br>-->
    <!--<a href="register.html" class="text-center">Register a new membership</a>-->

  </div>
  <!-- /.login-box-body -->
</div>
<!-- /.login-box -->

<!-- jQuery 3 -->
<script src="/Public/js/jquery.min.js"></script>
<!-- Bootstrap 3.3.7 -->
<script src="/Public/js/bootstrap.min.js"></script>
<!-- iCheck -->
<script src="/Public/js/icheck.min.js"></script>
<script src="/Public/layer/layer.js"></script>
<script src="/Public/js/common.js?r=<?php echo NOW_TIME;?>"></script>
<script>
  $(function () {
    $('input').iCheck({
      checkboxClass: 'icheckbox_square-blue',
      radioClass: 'iradio_square-blue',
      increaseArea: '20%' /* optional */
    });
  });
  //获取验证码
  $("#verify").click(function(){
    var url = "<?php echo U('Public/verify_code');?>";
    url += '?t=' + Math.random();
    $(this).attr("src",url);
  });
</script>
</body>
</html>