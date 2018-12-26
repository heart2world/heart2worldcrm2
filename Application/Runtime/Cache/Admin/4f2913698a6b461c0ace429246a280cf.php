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
        <form action="#" method="get" class="sidebar-form">
            <div class="input-group">
                <input type="text" name="q" class="form-control" placeholder="Search...">
                    <span class="input-group-btn">
                        <button type="submit" name="search" id="search-btn" class="btn btn-flat"><i class="fa fa-search"></i></button>
                    </span>
            </div>
        </form>
        <!-- /.search form -->
        <!--菜单管理-->
        <ul class="sidebar-menu" data-widget="tree">
            <?php if(is_array($menu_data)): $i = 0; $__LIST__ = $menu_data;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i; if(!empty($vo['level_data'])): ?><li <?php if(($vo['is_show']) == "1"): ?>class="treeview active"<?php else: ?>class="treeview"<?php endif; ?> >
                    <a href="http://www.baidu.com">
                        <?php switch($vo['type']): case "1": ?><i class="fa fa-files-o"></i><?php break;?>
                            <?php case "2": ?><i class="fa fa-gear"></i><?php break;?>
                            <?php case "3": ?><i class="fa fa-user"></i><?php break;?>
                            <?php default: ?><i class="fa fa-files-o"></i><?php endswitch;?>
                        <span><?php echo ($vo['title']); ?></span>
                            <span class="pull-right-container">
                              <i class="fa fa-angle-left pull-right"></i>
                            </span>
                    </a>
                    <ul class="treeview-menu">
                        <?php if(is_array($vo['level_data'])): $i = 0; $__LIST__ = $vo['level_data'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$item): $mod = ($i % 2 );++$i;?><li <?php if(($item['is_show']) == "1"): ?>class="active"<?php endif; ?> ><a href="<?php echo ($item['url']); ?>"><i class="fa fa-circle-o"></i><?php echo ($item['title']); ?></a></li><?php endforeach; endif; else: echo "" ;endif; ?>
                    </ul>
                    </li>
                <?php else: ?>
                    <li >
                    <a href="<?php echo ($vo['url']); ?>">
                        <?php switch($vo['type']): case "1": ?><i class="fa fa-files-o"></i><?php break;?>
                            <?php case "2": ?><i class="fa fa-gear"></i><?php break;?>
                            <?php case "3": ?><i class="fa fa-user"></i><?php break;?>
                            <?php default: ?><i class="fa fa-files-o"></i><?php endswitch;?>
                        <span><?php echo ($vo['title']); ?></span>
                    </a>
                    </li><?php endif; endforeach; endif; else: echo "" ;endif; ?>
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
                <form class="fire-form">
                	<div class="clearfix">
                		<div class="left">
                			公海名称
                		</div>
                		<div class="right">
                			<input type="text" />
                		</div>
                	</div>
                	<div class="clearfix">
                		<div class="left">
                			公海管理员
                		</div>
                		<div class="right">
                			<a class="chose" onclick="addAdmin()">请选择管理员</a>
                		</div>
                	</div>
                	<div class="clearfix">
                		<div class="left">
                			公海成员
                		</div>
                		<div class="right">
                			<a class="chose" onclick="addPerson()">请选择成员</a>
                		</div>
                	</div>
                	<div class="clearfix">
                		<div class="left">
                			公海规则
                		</div>
                		<div class="right">
                			<div class="inlines">
                				<input type="radio" id="test" name="type" style="margin-right:5px;top:2px;position:relative;" />
                				<label for="test">统一规则</label>
                			</div>
                			<div class="inlines">
                				所有客户在<input type="text" class="in-input" />天未跟进情况下，自动掉入公海
                			</div>
                			<div class="inlines">
                				<input type="radio" id="test1" name="type" style="margin-right:5px;top:2px;position:relative;" />
                				<label for="test1">根据客户类型</label>
                			</div>
                			
                			<div class="inlines" style="margin-bottom:25px;">
                				<select class="typeSelect">
                					<option>A类</option>
                					<option>B类</option>
                				</select>
                				类型客户在<input type="text" class="in-input" />天未跟进情况下，自动掉入公海
                			</div>
                			<div class="inlines" style="margin-bottom:25px;">
                				<select class="typeSelect">
                					<option>A类</option>
                					<option>B类</option>
                				</select>
                				类型客户在<input type="text" class="in-input" />天未跟进情况下，自动掉入公海
                			</div>
                			<div class="inlines" style="margin-bottom:25px;">
                				<select class="typeSelect">
                					<option>A类</option>
                					<option>B类</option>
                				</select>
                				类型客户在<input type="text" class="in-input" />天未跟进情况下，自动掉入公海
                			</div>
                			
                			<div class="inlines" style="margin-bottom:25px;">
                				<select class="typeSelect">
                					<option>A类</option>
                					<option>B类</option>
                				</select>
                				类型客户在<input type="text" class="in-input" />天未跟进情况下，自动掉入公海
                			</div>
                			<div class="inlines" style="margin-bottom:25px;">
                				<select class="typeSelect">
                					<option>A类</option>
                					<option>B类</option>
                					<option selected="">无效</option>
                				</select>
                				类型客户在<input type="text" class="in-input" />天未跟进情况下，自动掉入公海
                			</div>
                			
                			
                			<div class="inlines" style="margin-bottom:25px;">
                				系统每天的<input type="text" class="in-input" />点将未跟进客户划入公海
                			</div>
                			<div class="inlines">
                				抢回限制&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="text" class="in-input" />天以内不能抢回统一客户
                			</div>
                		</div>
                	</div>
                	<button class="save">保存设置</button>
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

	<script>
		function addAdmin(){
			layer.open({
				title:"公海管理员",
				btn:"添加",
				content:'<div>类型客户在<select class="typeSelect" style="margin-left:20px;width:220px;">\
	                					<option>A类</option>\
	                					<option>B类</option>\
	                				</select>\
	                				</div>',
			})
		}
		function addPerson(){
			layer.open({
				title:"公海成员",
				btn:"添加",
				content:'<div class="layerPerson clearfix">\
					<div>\
						<input type="checkbox" id="che1" />\
                		<label for="che1">李先生</label>\
                	</div>\
                	<div>\
						<input type="checkbox" id="che2" />\
                		<label for="che2">黄先生</label>\
                	</div>\
                	<div>\
						<input type="checkbox" id="che3" />\
                		<label for="che3">孙先生</label>\
                	</div>\
                	<div>\
						<input type="checkbox" id="che4" />\
                		<label for="che4">高先生</label>\
                	</div>\
				</div>',
			})
		}
	</script>


</body>
</html>