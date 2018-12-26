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


    <style>
        .info_style{width: 30%;}
        .logs_content{margin-left: 25px;}
        .logs_line{line-height: 30px;}
        table{border: 1px solid #000;width: 80%;}
        th,td{border:  1px solid #000;text-align: center;line-height: 30px;padding: 0 5px;}
        .nav-tabs-custom>.nav-tabs>li.header{font-size: 14px;line-height: 35px;padding-left: 20px;height: 42px;}
        .box-body{padding: 15px 20px;}
        .nav-tabs-custom{box-shadow: none;}
        .nav-tabs-custom:nth-child(4n){margin-right:0;}
        .timeline>li>.timeline-item{box-shadow: none;background: #f7f7f7;padding: 9px 12px;}
        .timeline>li>.timeline-item>.timeline-header{padding-top:6px;border-bottom: 0;font-size:14px;}
        .timeline-header a{margin-right:16px;font-size:16px;}
        .fileSpan{width:60%;}
        .fileSpan a{color:#FFF;display:;line-height:30px;}
        .imgsl{width:70px;height:70px;cursor:pointer;margin-right:10px;margin-bottom:10px;}
        .fa-envelope:before{display:none;}
        .timeline>li>.timeline-item>.timeline-body, .timeline>li>.timeline-item>.timeline-footer{padding-top:0;margin-top:-10px;}
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
            <section class="content-header">
                <?php if(($info['status']) == "2"): ?><!--公海详情-->
                    <a class="btn btn-primary" onclick="ajax_get(this)" url="<?php echo U('grab',array('id'=>$vo['id']));?>" title="抢客户" msg='确定"抢到"该客户？'>抢</a>
                <?php else: ?>
                    <!--客户详情-->
                    <a class="btn btn-primary popshow" url="<?php echo U('customer_transfer',array('customer_id'=>$info['id']));?>" title="转移客户" w="600" h="400">转移</a>
                    <a class="btn btn-primary popshow" url="<?php echo U('edit',array('id'=>$info['id']));?>" title="编辑客户" w="600" h="500">编辑</a>
                    <a class="btn btn-primary popshow" url="<?php echo U('add_follow',array('customer_id'=>$info['id']));?>" title="写跟进" w="600" h="500">写跟进</a><?php endif; ?>
            </section>

            <!-- Main content -->
            <section class="content">
                <div class="row">
					<section class="col-lg-5 connectedSortable">
                        <?php if(is_array($info['lists'])): $i = 0; $__LIST__ = $info['lists'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><div class="nav-tabs-custom">
                                <?php switch($vo['type']): case "1": ?><ul class="nav nav-tabs pull-right" style="background-color: #00a7d0;border-bottom: 0;">
                                            <li class="pull-left header" style="color:#FFF;"><?php echo ($vo['title']); ?></li>
                                        </ul>
                                        <div class="tab-content no-padding" style="background-color: #00c0ef"><?php break;?>
                                    <?php case "2": ?><ul class="nav nav-tabs pull-right" style="background-color: #db8b0b;border-bottom: 0;">
                                            <li class="pull-left header" style="color:#FFF;"><?php echo ($vo['title']); ?></li>
                                        </ul>
                                        <div class="tab-content no-padding" style="background-color: #f39c12"><?php break;?>
                                    <?php case "3": ?><ul class="nav nav-tabs pull-right" style="background-color: #008d4c;border-bottom: 0;">
                                            <li class="pull-left header" style="color:#FFF;"><?php echo ($vo['title']); ?></li>
                                        </ul>
                                        <div class="tab-content no-padding" style="background-color: #00a65a"><?php break;?>
                                    <?php case "4": ?><ul class="nav nav-tabs pull-right" style="background-color: #d33724;border-bottom: 0;">
                                            <li class="pull-left header" style="color:#FFF;"><?php echo ($vo['title']); ?></li>
                                        </ul>
                                        <div class="tab-content no-padding" style="background-color: #dd4b39"><?php break; endswitch;?>

                                    <div class="chart tab-pane active" style="position: relative;">
                                        <div class="box-body" style="color: #FFF;">
                                            <?php if(is_array($vo['data'])): $i = 0; $__LIST__ = $vo['data'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$item): $mod = ($i % 2 );++$i; switch($item['other_type']): case "image": ?><div class="form-group">
                                                            <label class="info_style">图片</label>
                                                            <span style="width:60%;display:inline-block;margin-left:30%;margin-top:-25px;">
                                                                <?php if(is_array($item['content'])): foreach($item['content'] as $key=>$pic_data): ?><img src="<?php echo ($pic_data); ?>" class="imgsl pic_show" /><?php endforeach; endif; ?>
                                                            </span>
                                                        </div><?php break;?>
                                                    <?php case "file": ?><div class="form-group">
                                                            <label class="info_style">附件</label>
                                                            <span class="fileSpan" <?php if(count($item['content']) > 1): ?>style="display:inline-block;margin-left:30%;margin-top:-30px;"<?php endif; ?> >
                                                                <?php if(is_array($item['content'])): foreach($item['content'] as $key=>$file_data): ?><a href="<?php echo ($file_data['url']); ?>" target="_blank" style="text-decoration: underline;"><?php echo ($file_data['name']); ?></a><br /><?php endforeach; endif; ?>
                                                            </span>
                                                        </div><?php break;?>
                                                    <?php default: ?>
                                                        <div class="form-group">
                                                            <label class="info_style"><?php echo ($item['title']); ?></label>
                                                            <span><?php echo ($item['content']); ?></span>
                                                        </div><?php endswitch; endforeach; endif; else: echo "" ;endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div><?php endforeach; endif; else: echo "" ;endif; ?>
                    </section>
                    
                    <section class="col-lg-7 connectedSortable">
                        <div class="nav-tabs-custom" style="width:100%;">
                            <ul class="nav nav-tabs pull-right">
                                <li class="pull-left header">跟进动态</li>
                            </ul>
                            <div class="tab-content no-padding" >
                                <!-- Morris chart - Sales -->
                                <div class="chart tab-pane active" style="position: relative;">
                                    <div class="box-body">
                                    	<ul class="timeline" style="margin-top:15px;">
                                    		<?php if(is_array($logs)): $i = 0; $__LIST__ = $logs;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><li class="time-label">
                                                    <?php switch($vo['pic_type']): case "1_1": ?><span class="bg-red" style="background:#00c1a3!important;"><?php echo ($vo['create_time']); ?></span><?php break;?>
                                                        <?php case "1_2": ?><span class="bg-red" style="background:#01abee!important;"><?php echo ($vo['create_time']); ?></span><?php break;?>
                                                        <?php case "1_3": ?><span class="bg-red" style="background:#0cce15!important;"><?php echo ($vo['create_time']); ?></span><?php break;?>
                                                        <?php case "1_4": ?><span class="bg-red" style="background:#ffa200!important;"><?php echo ($vo['create_time']); ?></span><?php break;?>
                                                        <?php case "1_5": ?><span class="bg-red" style="background:#55a3ff!important;"><?php echo ($vo['create_time']); ?></span><?php break;?>
                                                        <?php case "1_6": ?><span class="bg-red" style="background:#34c800!important;"><?php echo ($vo['create_time']); ?></span><?php break;?>
                                                        <?php case "1_7": ?><span class="bg-red" style="background:#2a70ff!important;"><?php echo ($vo['create_time']); ?></span><?php break;?>
                                                        <?php case "2": ?><span class="bg-red" style="background:#c046ff!important;"><?php echo ($vo['create_time']); ?></span><?php break;?>
                                                        <?php case "3": ?><span class="bg-red" style="background:#00b868!important;"><?php echo ($vo['create_time']); ?></span><?php break;?>
                                                        <?php case "4": ?><span class="bg-red" style="background:#ff2778!important;"><?php echo ($vo['create_time']); ?></span><?php break;?>
                                                        <?php case "5": ?><span class="bg-red" style="background:#00e1e9!important;"><?php echo ($vo['create_time']); ?></span><?php break;?>
                                                        <?php case "6": ?><span class="bg-red" style="background:#3d91b0!important;"><?php echo ($vo['create_time']); ?></span><?php break;?>
                                                        <?php case "7": ?><span class="bg-red" style="background:#7ba6ff!important;"><?php echo ($vo['create_time']); ?></span><?php break; endswitch;?>
											    </li>
											    <li>
											        <i class="fa" style="background: url(/public/img/ico<?php echo ($vo['pic_type']); ?>.png)no-repeat center center / 100% 100%;"></i>
											        <div class="timeline-item">
                                                        <h3 class="timeline-header"><a href="javaScript:void(0)" style="display:<?php if($vo['users_name']==null): ?>none;<?php endif; ?>;"><?php echo ($vo['users_name']); ?></a><?php echo ($vo['type_text']); ?></h3>
											            <?php switch($vo['type']): case "1": ?><div class="timeline-body"><br/><?php echo ($vo['content']['content']); ?></div>
		                                                        <?php if(!empty($vo['content']['follow_pic'])): ?><div class="logs_line" style="margin-left:11px;">
		                                                                <?php if(is_array($vo['content']['follow_pic'])): $i = 0; $__LIST__ = $vo['content']['follow_pic'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$item): $mod = ($i % 2 );++$i;?><img class="pic_show" style="width: 100px;height: 100px;margin-right: 5px;" src="<?php echo getFileUrl($item);?>" /><?php endforeach; endif; else: echo "" ;endif; ?>
		                                                            </div><?php endif; ?>
		                                                        <div class="logs_line" style="margin-left:11px;">写跟进的时间：<?php echo ($vo['content']['follow_time']); ?></div><?php break;?>
		                                                    <?php case "2": ?><div class="timeline-body">
			                                                        <?php if(is_array($vo['content'])): $i = 0; $__LIST__ = $vo['content'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$item): $mod = ($i % 2 );++$i;?><br/><span style="color: #929292;<?php if($item['title']!=null): ?>margin-right:11px;<?php endif; ?>display:inline-block;"><?php echo ($item['title']); ?></span><?php echo ($item['content']); endforeach; endif; else: echo "" ;endif; ?>
		                                                        </div><?php break;?>
		                                                    <?php default: ?>
                                                            <div class="timeline-body">
                                                                <?php if(!empty($vo['content'])): ?><br/><?php echo ($vo['content']); endif; ?>
                                                            </div><?php endswitch;?>
											        </div>
											    </li><?php endforeach; endif; else: echo "" ;endif; ?>
										</ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>
					
					
                </div>
            </section>
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
		$(".pic_show").click(function(){
			var that = $(this);
			layer.open({
			  type: 1,
			  title: false,
			  closeBtn: 0,
//			  area: '516px',
			  skin: 'layui-layer-nobg', //没有背景色
			  shadeClose: true,
              scrollbar:false,
			  content: '<img src="'+ that.attr("src") +'" style="width:100%;" />'
			});
		})
	</script>


</body>
</html>