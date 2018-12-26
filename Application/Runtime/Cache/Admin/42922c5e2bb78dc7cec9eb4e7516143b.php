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
        .screen{line-height: 30px;margin-bottom: 10px;}
        .one_option_title{width: 120px;}
        .one_option{padding: 2px 10px;color:#444;}
        .button_show{width: 50%;float: left;}
        .search{width: 50%;float: right;text-align: right;}
        .option_on{background-color: #3c8dbc;color: #fff !important;}
        .activeTd{background:#cee3ff!important;}
        @media (min-width: 767px){.pageFixed{position: fixed; bottom: 0; right: 0; width: calc(100% - 230px); background: #FFFF; padding: 5px 20px; padding-bottom: 0px; box-shadow: 0px -2px 3px #f1f1f1;z-index:9999998;}}
        .buttonFixed{ position: absolute; right: 0; height: 0; width: 0; top: 0; background: #FFF; z-index: 9999; display:none; }
        .buttonFixed>div:nth-child(1) div{ font-weight: bold;border-bottom-width: 3px; height:44px;line-height: 40px; }
        /*.buttonFixed>div:nth-child(1) div{margin-top:0px!important;}*/    
        .buttonFixed>div:nth-of-type(even) { background-color: #f9f9f9; }
        .buttonFixed>div div{ padding:0 8px; border: 1px solid #f4f4f4;margin-top: -1px;height:43px; vertical-align: top; font-size:14px; color: #333; line-height: 43px; overflow:hidden; float:left; text-align:center; }
        .buttonFixed>div div:nth-child(1){ border-right:0; }
        .buttonFixed>div div:nth-child(2){ padding-right: 30px; text-align:left; }
        .main-footer{display:none;}
    	.table > tbody > tr > td, .table > tbody > tr > th, .table > tfoot > tr > td, .table > tfoot > tr > th, .table > thead > tr > td, .table > thead > tr > th{line-height:24px;height:42px;box-sizing: border-box;}
    	.wrapper{min-height:100vh!important;}
    	.scoll{padding:0;overflow:auto;}
	    .content-wrapper{ min-height:initial!important; height:calc(100vh - 50px - 30px); }
    </style>

</head>
<body class="hold-transition skin-blue sidebar-mini">


    <div class="wrapper" style="background:rgb(236, 240, 245);">
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
                    全部客户
                </h1>
            </section>

            <!-- Main content -->
            <section class="content">
                <div class="row">
                    <div class="col-xs-12">
                        <!-- /.box -->

                        <div class="box">
                            <div class="box-header">
                                <!--筛选-->
                                <div class="screen">
                                    <?php if(is_array($info['screen_data']['choice'])): $k = 0; $__LIST__ = $info['screen_data']['choice'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($k % 2 );++$k;?><div class="ones_option" name="<?php echo ($vo['fields_text']); ?>" style="display:<?php if($k > 3): ?>none<?php endif; ?>">
	                                        <label class="one_option_title"><?php echo ($vo['title']); ?>：</label>
	                                        <a href="#" class="one_option option_on" value="">全部</a>
	                                        <?php if(is_array($vo['extra'])): $i = 0; $__LIST__ = $vo['extra'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$item): $mod = ($i % 2 );++$i; if(!empty($item['id'])): ?><a href="#" class="one_option" value="<?php echo ($item['id']); ?>" <?php if(($item['other_type']) == "time"): ?>other_type="time"<?php endif; ?> ><?php echo ($item['title']); ?></a>
	                                                <?php if(($item['other_type']) == "time"): ?><input type="text" name="time_<?php echo ($vo['fields_text']); ?>" id="time_<?php echo ($vo['fields_text']); ?>" readonly style="height: 24px;display: none;"/><?php endif; ?>
	                                            <?php else: ?>
	                                                <a href="#" class="one_option" value="<?php echo ($item['title']); ?>" ><?php echo ($item['title']); ?></a><?php endif; endforeach; endif; else: echo "" ;endif; ?>
	                                    </div>
	                                    <?php if($k > 3 && count($info['screen_data']['choice']) == $k): ?><a href="javaScript:void(0)" onclick="openMore(this)">展开更多</a><?php endif; endforeach; endif; else: echo "" ;endif; ?>
                                </div>
                                <!--搜索-->
                                <div>
                                    <!--按钮显示-->
                                    <div class="button_show">
                                        <a class="btn btn-primary popshow" url="<?php echo U('edit');?>" title="新增客户" w="600" h="500">新增客户</a>
                                        <a class="btn btn-primary transfer">转移</a>
                                        <a class="btn btn-primary waters">转移至公海</a>
                                        <a class="btn btn-primary" id="excel" href="#">导出excel</a>
                                        <a class="btn btn-primary popshow" url="<?php echo U('edit_fields_screen');?>" title="自定义筛选字段" w="600" h="500">筛选设置</a>
                                    </div>
                                    <!--搜索显示-->
                                    <div class="search">
                                        <select class="select2 search_mark" style="width: 100px;" name="search">
                                            <?php if(is_array($info['screen_data']['text'])): $i = 0; $__LIST__ = $info['screen_data']['text'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><option value="<?php echo ($vo['fields_text']); ?>" ><?php echo ($vo['title']); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
                                        </select>
                                        <input type="text" name="search_title" class="form-control search_title" style="display: inline;width: 30%; vertical-align: bottom" value="" maxlength="20" placeholder="请输入">
                                        <button type="button" class="btn btn-primary" id="search">查询</button>
                                    </div>
                                </div>
                            </div>


                            <div class="box-body">
                                <div class="dataTables_wrapper form-inline dt-bootstrap no-footer">
									<div class="">
                                        <div class="col-sm-12 scoll">
                                            <table class="table table-bordered table-striped dataTable">
                                                <thead>
                                                <tr>
                                                    <td align="center" style="white-space: nowrap;"><input type="checkbox" class="check_all"></td>
                                                    <?php if(is_array($info['show_data'])): $i = 0; $__LIST__ = $info['show_data'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><th style="white-space: nowrap;" class="sorting" name="<?php echo ($vo['fields_text']); ?>" onclick="sort(this)"><?php echo ($vo['title']); ?></th><?php endforeach; endif; else: echo "" ;endif; ?>
                                                    <td style="white-space: nowrap;"><a href="#" class="popshow"  url="<?php echo U('edit_fields_show');?>" title="自定义显示列" w="600" h="500"><i class="fa fa-fw fa-cog"></i></a></td>
                                                </tr>
                                                </thead>
                                                <tbody>
                                            	
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <!--分页-->
                                    <div class="page pageFixed"></div>
                                </div>
                            </div>
                            <!-- /.box-body -->
                        </div>
                        <!-- /.box -->
                    </div>
                    <!-- /.col -->
                </div>
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

	<!-- Select2 -->
    <script src="/Public/js/select2.full.min.js"></script>
    <!--laydate-->
    <script src="/Public/laydate/laydate.js"></script>
    <script>
		
        //下拉选择样式
        $('.select2').select2();
        //弹出层
        function pop_show(obj){
            layer_show($(obj).attr('title'),$(obj).attr('url'),$(obj).attr('w'),$(obj).attr('h'));
        }
        //自定义时间
        var time_data = <?php echo ($info['time_ids']); ?>;
        for(var i in time_data){
            laydate.render({
                elem: '#time_'+time_data[i],
                theme: '#3c8dbc',
                trigger: 'click',
                range: true,
                done:function(){
                    hanle_data();
                }
            });
        }
        //转移
        $(".transfer").click(function(){
            var ids = new Array();
            $(".ids").each(function(){
                if($(this).is(':checked')){
                    ids.push($(this).val());
                }
            });
            ids = ids.join('_');
            if(!ids){
                layer.msg('请选择需要转移的客户', {icon: 2,time:1500});
                return false;
            }
            var url = "<?php echo U('customer_transfer');?>";
            url += '?customer_id=' + ids;
            layer_show('转移客户',url,'600','400');
        });
        //转移至公海
        $(".waters").click(function(){
            var ids = new Array();
            $(".ids").each(function(){
                if($(this).is(':checked')){
                    ids.push($(this).val());
                }
            });
            ids = ids.join('_');
            if(!ids){
                layer.msg('请选择需要转移至公海的客户', {icon: 2,time:1500});
                return false;
            }
            var url = "<?php echo U('Waters/into');?>";
            url += '?id=' + ids;
            layer.confirm('确定要将客户转移至公海？', {
                btn: ['确定','关闭'],//按钮
                title:'信息'
            }, function(){
                //确定
                $.ajax({
                    type:"get",
                    url:url,
                    success:function(data){
                        if(data.status == 1){
                            layer.msg(data.info, {icon: 1,time:2000},function(){
                                if(data.url){
                                    window.location.href = data.url;
                                }else{
                                    window.location.reload();
                                }
                            });
                        }else{
                            layer.msg(data.info, {icon: 2,time:2000});
                        }
                    }
                });
            }, function(){
                //取消
            });
        });
        //全选
        $("body").on('click','.check_all',function(){
            if($(this).is(":checked")){
                $(".ids").prop('checked',true);
                $(".dataTables_wrapper tbody tr,.buttonFixed>div").addClass("activeTd")
            }else{
                $(".ids").prop('checked',false);
                $(".dataTables_wrapper tbody tr,.buttonFixed>div").removeClass("activeTd")
            }
            $(".buttonFixed>div").eq(0).removeClass("activeTd")
        });
        //单选
        $("body").on("click",".buttonFixed>div .ids",function(e){
            if($(this).is(":checked")){
            	$(".dataTable tbody tr").eq($(this).parents(".clearfix").index()-1).addClass("activeTd")
				$(".buttonFixed>div").eq($(this).parents(".clearfix").index()).addClass("activeTd");
            }else{
            	$(".check_all").prop('checked',false);
            	$(".dataTable tbody tr").eq($(this).parents(".clearfix").index()-1).removeClass("activeTd")
				$(".buttonFixed>div").eq($(this).parents(".clearfix").index()).removeClass("activeTd");
            }
            e.stopPropagation();
        });
        var timers=null;
        $("body").on("click",".buttonFixed>div div:nth-child(2)",function(e){
        	var that = $(this);
        	clearTimeout(timers);
			timers = setTimeout(function(){
	        	if(that.hasClass(".ids")){
	        		var index = that.parents(".clearfix").index();
	        	}else{
	        		var index = that.parents().index();
	        	}
	            if(!$(".buttonFixed>div").eq(index).find(".ids")[0].checked){
	                $(".dataTable tbody tr").eq(index-1).addClass("activeTd")
					$(".buttonFixed>div").eq(index).find("input[type='checkbox']").prop("checked", true);
					$(".buttonFixed>div").eq(index).addClass("activeTd");
	            }else{
	            	$(".check_all").prop('checked',false);
	            	$(".dataTable tbody tr").eq(index-1).removeClass("activeTd")
					$(".buttonFixed>div").eq(index).find("input[type='checkbox']").prop("checked", false);
					$(".buttonFixed>div").eq(index).removeClass("activeTd");
	            }
            },200)
        });
   		$("body").on("dblclick",".buttonFixed>div div:nth-child(2)",function(){
			clearTimeout(timers);
			console.log($().eq($(this).parents(".clearfix").index()-1))
			//open($(".dataTable tbody tr").eq($(this).parents(".clearfix").index()-1).find("a").eq(2).attr("href"),"_blank");
		})
        
		//单机选中任一一行
		var timer=null;
		$(".dataTables_wrapper tbody").on("click","tr",function(){
			var that = $(this);
			clearTimeout(timer);
			timer = setTimeout(function(){
			    if($(".buttonFixed>div").eq(that.index()+1).find(".ids")[0].checked){
					that.removeClass("activeTd")
					//that.find("input[type='checkbox']").prop("checked", false);
					$(".buttonFixed>div").eq(that.index()+1).find(".ids").prop("checked", false);
					$(".buttonFixed>div").eq(that.index()+1).removeClass("activeTd");
					$(".check_all").prop('checked',false);
				}else{
					that.addClass("activeTd")
					//that.find("input[type='checkbox']").prop("checked", true);
					$(".buttonFixed>div").eq(that.index()+1).find(".ids").prop("checked", true);
					$(".buttonFixed>div").eq(that.index()+1).addClass("activeTd");
				} 
			},200)
		})
		$(".dataTables_wrapper tbody").on("click","a",function(e){
			e.stopPropagation();
		})
		//双击打开详情页
		$(".dataTables_wrapper tbody").on("dblclick","tr",function(){
			clearTimeout(timer);
			open($(this).find("td").eq($(this).find("td").length-1).find("a").eq(2).attr("href"),"_blank");
		})
        //自动加载数据
        $(function(){
            $.ajax({
                type:"get",
                url:"<?php echo ($url); ?>",
                success:function(html){
                    if(html.lists){
                        $("tbody").html(html.lists);
                        $(".page").html(html.page);
                        $(".buttonFixed").remove();
                        $(".dataTable").append('<div class="buttonFixed"></div>');
                        fixed();
                    }else{
                        $("tbody").html('<tr><td colspan="50" align="center">暂无数据</td></tr>')
                        $(".page,.buttonFixed").remove();
                    }
                }
            });
        });
        //处理数据
        function hanle_data(order,num){
            if(!order){
                if($(".sorting_desc").length != 0 ){
                    order = $(".sorting_desc").attr('name') + ' desc';
                }else if($(".sorting_asc").length != 0 ){
                    order = $(".sorting_asc").attr('name') + ' asc';
                }
            }
            var data = '';
            var val="";
            var name="";
            if(!num){
                num = 1;
            }
            var search = $(".search_mark").val();
            var search_title = $(".search_title").val();
            if(search && search_title){
                data += 'search=' + search + '&search_title=' + search_title;
            }
            if(order){
                if(data){
                    data += '&order=' + order;
                }else{
                    data += 'order=' + order;
                }
            }
            $(".option_on").each(function(){
                val = $(this).attr('value');
                if(val){
                    name = $(this).parent(".ones_option").attr("name");
                    if(data){
                        data += '&' + name + '=' + val;
                    }else{
                        data += name + '=' + val;
                    }
                    //时间段
                    if($(this).attr('other_type') == 'time' && $(this).next("input").val()){
                        data += '&time_' + name + '=' + $(this).next("input").val();
                    }
                }
            });
            if(data){
                data += '&p=' + num;
            }else{
                data += 'p=' + num;
            }
            $.ajax({
                type:"post",
                url:"<?php echo ($url); ?>",
                data:data,
                success:function(html){
                    if(html.lists){
                        $("tbody").html(html.lists);
                        $(".page").html(html.page);
                        $(".buttonFixed").remove();
                        $(".dataTable").append('<div class="buttonFixed"></div>');
                        fixed();
                    }else{
                        $("tbody").html('<tr><td colspan="50" align="center">暂无数据</td></tr>');
                        $(".page,.buttonFixed").remove();
                    }
                }
            });
        }
        //筛选
        $("#search").click(function(){
            hanle_data();
        });
        document.onkeydown = function (e) {
            if (!e) e = window.event;
            if ((e.keyCode || e.which) == 13) {
                hanle_data();
            }
        }
        function test(){
            alert('abc');
        }
        //筛选
        $(".one_option").click(function(){
            $(this).addClass("option_on");
            $(this).siblings("a").removeClass("option_on");
            if($(this).attr("other_type") == "time"){
                //时间段
                $(this).next("input").show();
                return false;
            }else{
                $(this).parent(".ones_option").find("input").hide();
            }
            hanle_data();
        });
        //分页
        function ajax_page(obj){
            var num = $(obj).attr("num");
            hanle_data('',num);
        }
        //排序
        function sort(obj){
            if($(obj).hasClass("sorting")){
                $(obj).siblings("th").removeClass("sorting_desc");
                $(obj).siblings("th").removeClass("sorting_asc");
                $(obj).siblings("th").addClass("sorting");
                $(obj).removeClass("sorting");
                $(obj).addClass("sorting_asc");
                var order = $(obj).attr('name') + ' asc';
                hanle_data(order);
            }else if($(obj).hasClass("sorting_desc")){
                $(obj).removeClass("sorting_desc");
                $(obj).addClass("sorting_asc");
                var order = $(obj).attr('name') + ' asc';
                hanle_data(order);
            }else if($(obj).hasClass("sorting_asc")){
                $(obj).removeClass("sorting_asc");
                $(obj).addClass("sorting_desc");
                var order = $(obj).attr('name') + ' desc';
                hanle_data(order);
            }
        }
        //excel导出
        $("#excel").click(function(){
            var data = '';
            var val="";
            var name="";
            var order = "";
            var num = 1;
            var search = $(".search_mark").val();
            var search_title = $(".search_title").val();
            if(search && search_title){
                data += 'search=' + search + '&search_title=' + search_title;
            }
            if(order){
                if(data){
                    data += '&order=' + order;
                }else{
                    data += 'order=' + order;
                }
            }
            $(".option_on").each(function(){
                val = $(this).attr('value');
                if(val){
                    name = $(this).parent(".ones_option").attr("name");
                    if(data){
                        data += '&' + name + '=' + val;
                    }else{
                        data += name + '=' + val;
                    }
                    //时间段
                    if($(this).attr('other_type') == 'time' && $(this).next("input").val()){
                        data += '&time_' + name + '=' + $(this).next("input").val();
                    }
                }
            });
            if(data){
                data += '&p=' + num+'&is_my=<?php echo ($is_my); ?>';
            }else{
                data += 'p=' + num+'&is_my=<?php echo ($is_my); ?>';
            }
            var url = "<?php echo U('Excel/customer_excel');?>";
            url += '?' + data;
            window.location.href = url;
        });
        
        function fixed(t){
        	//宽度已适应
        	$(".buttonFixed").html("");
        	var td = $(".dataTable thead tr>*");
        	var width = td.eq(0).outerWidth() + td.eq(1).outerWidth()+1;
        	//if(t){width = width+2}
        	var height = td.eq(0).outerHeight()*$(".dataTable tr").length;
        	var top = td.eq(td.length - 1).offset().top - 109;
        	$(".buttonFixed").css({"width":width,"height":height,"top":"7px","left":"0","display":"block"}).html("");
        	//alert(parseFloat(td.eq(6).outerHeight()))
        	
        	for(var i = 0;i<$(".dataTable tr").length;i++){
        		var html = $(".dataTable tr").eq(i).find(">*").eq(0).html();
        		var html2 = $(".dataTable tr").eq(i).find(">*").eq(1).html();
        		var is = $(".dataTable tr").eq(i).hasClass("activeTd");
        		
        		$(".buttonFixed").append('<div class="clearfix '+ (is==true?'activeTd':'') +'"><div style="width:'+ (td.eq(0).outerWidth()) +'px;">'+html+'</div><div style="width:'+(td.eq(1).outerWidth())+'px;">'+html2+'</div></div>');
        		
        		
        		if(is&&$(".buttonFixed>div").eq(i).find(".ids").length>0){
        			console.log($(".buttonFixed>div").eq(i).find(".ids"))
    				$(".buttonFixed>div").eq(i).find(".ids").prop('checked',true);
	    		}
        	}
        	//让表格高度适应屏幕
        	$(".scoll").css("height",$(".content-wrapper").height() - $(".box-header").outerHeight() - 40 - 30 - 41)
        	
        }
        $(window).resize(function(){
        	fixed();
        })
        $(".scoll").scroll(function(e){
        	$(".buttonFixed").css("left",$(this).scrollLeft())
        })
        
        function openMore(t){
        	if($(t).html()=="展开更多"){
        		$(t).html("收起");
        		$(".ones_option").show();
        		fixed();
        	}else{
        		$(t).html("展开更多");
        		for(var i = 0;i<$(".ones_option").length;i++){
        			if(i>2){
        				$(".ones_option").eq(i).hide();
        			}
        		}
        		fixed(1);
			}
        }
    </script>


</body>
</html>