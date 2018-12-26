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
    <!-- jquery-ui -->
    <link rel="stylesheet" href="/Public/css/jquery-ui.min.css">
    <style>
        .move{cursor: move;}
        .mid{text-align: center;}
    </style>

</head>
<body class="hold-transition skin-blue sidebar-mini">


    <div class="wrapper" style="padding-bottom:50px;">

        <!--内容页面-->
        <div class="content-wrapper" style="margin-left: 0px;">

            <!-- Main content -->
            <section class="content">
                <div class="row">
                    <div class="col-xs-12">
                        <!-- /.box -->

                        <div class="box">
                            <!-- /.box-header -->
                            <div class="box-body">
                                <div class="dataTables_wrapper form-inline dt-bootstrap no-footer">
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <form method="post" action="<?php echo U('edit_fields_screen');?>" class="form-tagging">
                                                <table class="table table-bordered table-striped">
                                                    <thead>
                                                    <tr>
                                                        <th class="mid">是否显示</th>
                                                        <th width="60%">字段名称</th>
                                                        <th class="mid">拖动排序</th>
                                                    </tr>
                                                    </thead>
                                                    <tbody id="sortable">
                                                    <?php if(is_array($lists)): $i = 0; $__LIST__ = $lists;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><tr>
                                                            <td class="mid">
                                                                <input type="checkbox" name="choices[]" value="<?php echo ($vo['fields_text']); ?>" <?php if(($vo['status']) == "1"): ?>checked<?php endif; ?> />
                                                                <input type="hidden" name="sorts[]" value="<?php echo ($vo['fields_text']); ?>" />
                                                            </td>
                                                            <td><?php echo ($vo['title']); ?></td>
                                                            <td class="mid"><i class="fa fa-fw fa-bars move"></i></td>
                                                        </tr><?php endforeach; endif; else: echo "" ;endif; ?>
                                                    </tbody>
                                                </table>
                                                <div class="fixedBotton">
                                                	<button type="submit" class="btn btn-primary ajax-post" target_form="form-tagging">保存</button>
                                            	</div>
                                            </form>
                                        </div>
                                    </div>
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
    </div>


<!-- jQuery 3 -->
<script src="/Public/js/jquery.min.js"></script>
<!-- Bootstrap 3.3.7 -->
<script src="/Public/js/bootstrap.min.js"></script>
<!-- AdminLTE App -->
<script src="/Public/js/adminlte.min.js"></script>

<script src="/Public/layer/layer.js"></script>
<script src="/Public/js/common.js?r=<?php echo NOW_TIME;?>"></script>

    <!-- jquery-ui -->
    <script src="/Public/js/jquery-ui.min.js"></script>
    <script>
        $(function() {
            $( "#sortable" ).sortable({ handle: '.move' });
            $( "#sortable" ).disableSelection();
        });
    </script>


</body>
</html>