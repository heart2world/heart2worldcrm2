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


    <!-- DataTables -->
    <link rel="stylesheet" href="/Public/css/dataTables.bootstrap.min.css">
    <style>
        label{
            color:#333;
            font-weight: normal;
        }
        .content-wrapper{
            background:#FFF;
        }
        .fire-form{
            width:740px;
            background:#FFF;
            padding:20px 20px;
        }
        .fire-form .clearfix{
            margin-bottom:10px;
        }
        .fire-form .clearfix .left{
            float:left;
            width:90px;
            font-size:14px;
            height:38px;
            line-height:38px;
        }
        .fire-form .clearfix .right{
            float:right;
            width:calc(100% - 100px);
        }
        .fire-form .clearfix .right>input[type='text']{
            line-height:38px;
            height:38px;
            border-radius:2px;
            outline: none;
            padding:0px 10px;
            width:100%;
            display:block;
            border:1px solid #e6e6e6;
        }
        .chose{
            line-height:38px;
            height:38px;
            color:#3c8dbc;
            cursor: pointer;
        }
        .inlines{
            height:38px;
            line-height:38px;
            margin-bottom:10px;
        }
        .in-input{
            border-radius:2px;
            outline: none;
            padding: 0px 10px;
            display: inline-block;
            border: 1px solid #e6e6e6;
            margin:0px 15px;
            width: 100px;;
        }
        .typeSelect{
            height:38px;
            line-height:38px;
            padding:0px 9px;
            margin-right:10px;
            border: 1px solid #e6e6e6;
            border-radius:2px;
            width:80px;
            outline: none;
        }
        .save{
            width: 153px;
            height: 40px;
            line-height: 40px;
            border: 0;
            background: #3c8dbc;
            color: #FFF;
            border-radius: 2px;
            float: right;
            outline: none;
            margin-top:20px;
        }
        .content{
            padding-bottom:80px;
        }
        .layerPerson{

        }
        .layerPerson>div{
            float:left;
            width:50%;
        }
        .layerPerson>div input{
            top:2px;
            position:relative;
            margin-right:6px;
        }
    </style>

</head>
<body class="hold-transition skin-blue sidebar-mini">


    <div class="wrapper">
        <!--菜单-->
        <!--上部-->
<header class="main-header">
    <!-- Logo -->
    <a href="<?php echo U('Index/index');?>" class="logo">
        <!-- mini logo for sidebar mini 50x50 pixels -->
        <span class="logo-mini"><b>宇</b></span>
        <!-- logo for regular state and mobile devices -->
        <span class="logo-lg"><b>重庆心联宇</b></span>
    </a>
    <!-- Header Navbar: style can be found in header.less -->
    <nav class="navbar navbar-static-top">
        <!-- Sidebar toggle button-->
        <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">
            <span class="sr-only">Toggle navigation</span>
        </a>

        <div class="navbar-custom-menu">
            <ul class="nav navbar-nav">
                <!-- User Account: style can be found in dropdown.less -->
                <li class="dropdown user user-menu">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <img src="/Public/img/head.jpg" class="user-image" alt="User Image">
                        <span class="hidden-xs"><?php echo ($user['name']); ?></span>
                    </a>
                    <ul class="dropdown-menu">
                        <!-- User image -->
                        <li class="user-header">
                            <img src="/Public/img/head.jpg" class="img-circle" alt="User Image">

                            <p>
                                <?php echo ($user['name']); ?>
                                <small><?php echo ($user['role_text']); ?></small>
                            </p>
                        </li>
                        <!-- Menu Footer-->
                        <li class="user-footer">
                            <div class="pull-left">
                                <a href="#" class="btn btn-default btn-flat popshow" url="<?php echo U('Users/update_password',array('id'=>$vo['id'],'name'=>$user['name']));?>" title="重置密码" w="600" h="400">修改密码</a>
                            </div>
                            <div class="pull-right">
                                <a href="<?php echo U('Public/logout');?>" class="btn btn-default btn-flat">退出</a>
                            </div>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </nav>
</header>
<!--左部-->
<aside class="main-sidebar">
    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">
        <!-- Sidebar user panel -->
        <div class="user-panel">
            <div class="pull-left image">
                <img src="/Public/img/head.jpg" class="img-circle" alt="User Image">
            </div>
            <div class="pull-left info" style="line-height: 45px;">
                <p><?php echo ($user['name']); ?></p>
                <!--<a href="#"><i class="fa fa-circle text-success"></i> 在线</a>-->
            </div>
        </div>
        <!-- search form -->
        <!--<form action="#" method="get" class="sidebar-form">-->
            <!--<div class="input-group">-->
                <!--<input type="text" name="q" class="form-control" placeholder="Search...">-->
                    <!--<span class="input-group-btn">-->
                        <!--<button type="submit" name="search" id="search-btn" class="btn btn-flat"><i class="fa fa-search"></i></button>-->
                    <!--</span>-->
            <!--</div>-->
        <!--</form>-->
        <!-- /.search form -->
        <!--菜单管理-->
        <ul class="sidebar-menu" data-widget="tree">
            <?php if(is_array($menu_data)): $i = 0; $__LIST__ = $menu_data;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><li <?php if(($vo['is_show']) == "1"): ?>class="treeview active"<?php else: ?>class="treeview"<?php endif; ?> >
                <a>
                    <?php switch($vo['type']): case "1": ?><i class="fa fa-files-o"></i><?php break;?>
                        <?php case "2": ?><i class="fa fa-gear"></i><?php break;?>
                        <?php case "3": ?><i class="fa fa-user"></i><?php break;?>
                        <?php case "4": ?><i class="fa fa-creative-commons"></i><?php break;?>
                        <?php default: ?><i class="fa fa-files-o"></i><?php endswitch;?>
                    <span><?php echo ($vo['title']); ?></span>
                        <span class="pull-right-container">
                          <i class="fa fa-angle-left pull-right"></i>
                        </span>
                </a>
                <ul class="treeview-menu">
                    <?php if(is_array($vo['level_data'])): $i = 0; $__LIST__ = $vo['level_data'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$item): $mod = ($i % 2 );++$i;?><li <?php if(($item['is_show']) == "1"): ?>class="active"<?php endif; ?> ><a href="<?php echo ($item['url']); ?>"><i class="fa fa-circle-o"></i><?php echo ($item['title']); ?></a></li><?php endforeach; endif; else: echo "" ;endif; ?>
                </ul>
                </li><?php endforeach; endif; else: echo "" ;endif; ?>
        </ul>
    </section>
    <!-- /.sidebar -->
</aside>

        <!--内容页面-->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <section class="content-header">
                <h1>
                    新增公海
                </h1>
            </section>

            <!-- Main content -->
            <section class="content">
                <form class="fire-form" method="post" action="<?php echo U('setting/edit_waters');?>">
                    <div class="clearfix">
                        <div class="left">
                            公海规则
                        </div>
                        <div class="right">
                            <div class="inlines">
                                <input type="radio" id="test" name="type" value="1" <?php if(($info['type']) == "1"): ?>checked<?php endif; ?> style="margin-right:5px;top:2px;position:relative;" />
                                <label for="test">统一规则</label>
                            </div>
                            <div class="inlines">
                                所有客户在<input type="text" class="in-input" name="time[0]" value="<?php echo ($info['time'][0]); ?>" maxlength="4" onblur="checkInter(this)" />天未跟进情况下，自动掉入公海
                            </div>
                            <div class="inlines">
                                <input type="radio" id="test1" name="type" value="2" <?php if(($info['type']) == "2"): ?>checked<?php endif; ?> style="margin-right:5px;top:2px;position:relative;" />
                                <label for="test1">根据客户类型</label>
                            </div>

                            <div class="inlines" style="margin-bottom:25px;">
                                <!--<select class="typeSelect">-->
                                    <!--<option>A类</option>-->
                                    <!--<option>B类</option>-->
                                <!--</select>-->
                                A类类型客户在<input type="text" class="in-input" name="time[1]" value="<?php echo ($info['time'][1]); ?>" maxlength="4" onblur="checkInter(this)" />天未跟进情况下，自动掉入公海
                            </div>
                            <div class="inlines" style="margin-bottom:25px;">
                                B类类型客户在<input type="text" class="in-input" name="time[2]" value="<?php echo ($info['time'][2]); ?>" maxlength="4" onblur="checkInter(this)" />天未跟进情况下，自动掉入公海
                            </div>
                            <div class="inlines" style="margin-bottom:25px;">
                                C类类型客户在<input type="text" class="in-input" name="time[3]" value="<?php echo ($info['time'][3]); ?>" maxlength="4" onblur="checkInter(this)" />天未跟进情况下，自动掉入公海
                            </div>

                            <div class="inlines" style="margin-bottom:25px;">
                                D类类型客户在<input type="text" class="in-input" name="time[4]" value="<?php echo ($info['time'][4]); ?>" maxlength="4" onblur="checkInter(this)" />天未跟进情况下，自动掉入公海
                            </div>
                            <div class="inlines" style="margin-bottom:25px;">
                                无效类型客户在<input type="text" class="in-input" name="time[5]" value="<?php echo ($info['time'][5]); ?>" maxlength="4" onblur="checkInter(this)" />天未跟进情况下，自动掉入公海
                            </div>
                        </div>
                    </div>
                    <button class="save ajax-post" type="submit" target_form="fire-form">保存设置</button>
                </form>
            </section>
            <!-- /.content -->
        </div>

        <!--尾部-->
        <footer class="main-footer">
	<div class="pull-right hidden-xs">
		<!--<b>Version</b> 1.0.0-->
	</div>
	<strong>Copyright &copy; 2016-2018 <a href="#">重庆心联宇</a> </strong> 版权所有
</footer>
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