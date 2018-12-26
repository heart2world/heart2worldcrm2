/**
 * get提交
 */
$(".ajax-get").click(function(){
    var url = $(this).attr("url");
    var msg = $(this).attr("msg");
    if(!msg){
        msg = "确认要操作该条记录吗？";
    }
    layer.confirm(msg, {
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

function ajax_get(obj){
    var url = $(obj).attr("url");
    var msg = $(obj).attr("msg");
    var title = $(obj).attr("title");
    if(!msg){
        msg = "确认要操作该条记录吗？";
    }
    if(!title){
        title = "信息";
    }
    layer.confirm(msg, {
        btn: ['确定','关闭'],//按钮
        title:title
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
}

/**
 * post提交
 */
$(".ajax-post").click(function(){
    var obj = this;
    var target_form = $(this).attr("target_form");
    var jump_type = $(this).attr("jump_type");//跳转类型 1只刷新当前页
    var url = $("."+target_form).attr("action");
    var data = $("."+target_form).serialize();
    $(obj).attr("disabled",true);
    $.ajax({
        type:"post",
        url:url,
        data:data,
        success:function(data){
            if(data.status == 1){
                layer.msg(data.info, {icon: 1,time:1500},function(){
                    if(jump_type == 1){
                        window.location.reload();
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

/**
 * 弹出层
 */
$(".popshow").click(function(){
    var title = $(this).attr("title");
    var url = $(this).attr("url");
    var w = $(this).attr("w");
    var h = $(this).attr("h");

    layer_show(title,url,w,h);
});

/**
 * 弹出层
 */
function layer_show(title,url,w,h){
    if(w){
        w = w + "px";
    }
    if(h){
        h = h + "px";
    }
    var patt = /(phone|pad|pod|iPhone|iPod|ios|iPad|Android|Mobile|BlackBerry|IEMobile|MQQBrowser|JUC|Fennec|wOSBrowser|BrowserNG|WebOS|Symbian|Windows Phone)/i;
    if ((navigator.userAgent.match(patt))) {
        /*window.location.href="你的手机版地址";*/
        var index = layer.open({
            type:2,
            title:title,
            content: url,
            area:[w, h],
            maxmin:true,
            scrollbar:false
        });
        layer.full(index);
    }
    else {
        /*window.location.href="你的电脑版地址";    */
        layer.open({
            type:2,
            title:title,
            content: url,
            area:[w, h],
            maxmin:true,
            scrollbar:false
        });
    }
}

/**
 * 关闭弹出层
 */
function layer_close(){
    var index = parent.layer.getFrameIndex(window.name);
    parent.layer.close(index);
}


//检测数字和小数
function checkNum(obj){
    var num = $(obj).val();
    var patt = /^(([1-9]+\d*)|(\d+\.\d+)|0)$/;
    if(!patt.test(num)){
        $(obj).val('');
    }
}
//检测数字
function checkInter(obj){
    var num = $(obj).val();
    var patt = /^\d+$/;
    if(!patt.test(num)){
        $(obj).val('');
    }
}