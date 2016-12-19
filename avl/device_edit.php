<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>温湿度采集系统</title>
    <!-- Tell the browser to be responsive to screen width -->
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <!-- Bootstrap 3.3.5 -->
    <link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="dist/css/font-awesome.min.css">
    <!-- Ionicons -->
    <link rel="stylesheet" href="dist/css/ionicons.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="dist/css/AdminLTE.min.css">
    <!-- AdminLTE Skins. Choose a skin from the css/skins
         folder instead of downloading all of them to reduce the load. -->
    <link rel="stylesheet" href="dist/css/skins/_all-skins.min.css">
    <!-- iCheck -->
    <link rel="stylesheet" href="plugins/iCheck/flat/blue.css">
    <!-- Morris chart -->
    <link rel="stylesheet" href="plugins/morris/morris.css">
    <!-- jvectormap -->
    <link rel="stylesheet" href="plugins/jvectormap/jquery-jvectormap-1.2.2.css">
    <!-- Date Picker -->
    <link rel="stylesheet" href="plugins/datepicker/datepicker3.css">
    <!-- Daterange picker -->
    <link rel="stylesheet" href="plugins/daterangepicker/daterangepicker-bs3.css">
    <!-- bootstrap wysihtml5 - text editor -->
    <link rel="stylesheet" href="plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css">

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
        <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
  </head>
  <body class="hold-transition skin-blue sidebar-mini">
  <?php
    session_start();
    $user_id=$_SESSION["ID"];
    $D_ID=$_GET["D_ID"];
    echo "<input type='hidden' value='".$D_ID."' id='D_ID'>";
    echo "<input type='hidden' value='".$user_id."' id='user_id'>";
  ?>
	<div class="wrapper">
	<?php include "./header.php" ?>
		<div class="content-wrapper">
			<div class="box-header with-border">
				<h3 class="box-title">设备名称</h3>
			</div>
			<form role="form">
				<div class="box-body">
					<div class="form-group">
						<label>设备图标</label>
						<select class="form-control" id="device_icon" style="width: 100%;">
							<option selected="selected" value="0">请选择</option>
							<option value="shebei">各类设备</option>
							<option value="jifang">机房环境</option>
							<option value="hwg">危险区域</option>
						</select>
					</div>
					<div class="form-group">
						<label for="tag_num_input">设备序列号</label>
						<input type="text" class="form-control" id="device_num_input" disabled="disabled" placeholder="输入您设备正面的序列号">
					</div>
					<div class="form-group">
						<label for="tag_name_input">安装位置</label>
						<input type="text" class="form-control" id="device_name_input" placeholder="安装位置，起一个容易记住的名字">
					</div>
				</div>
				<div class="box-footer">
					<button type="button" class="btn btn-primary" onclick="submit_device_name()">提交修改</button>
					<button type="button" class="btn btn-primary" onclick="esc()">取消关注</button>
				</div>
			</form>
			<form role="form">
				<div class="box-body">
					<div class="form-group">
						<label for="tag_name_input">开关量输出</label>
						<div class="btn-group">
							<button type="button" class="btn btn-default" id="out1_button">输出1</button>
							<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
								<span class="caret"></span>
								<span class="sr-only">Toggle Dropdown</span>
							</button>
							<ul class="dropdown-menu" role="menu" id="out1_ul">
								<li id="out1_li1"><a onclick="out_action(1,1)">启动</a></li>
								<li id="out1_li2"><a onclick="out_action(1,0)">停止</a></li>
							</ul>
						</div>
						<div class="btn-group">
							<button type="button" class="btn btn-default" id="out2_button">输出2</button>
							<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
								<span class="caret"></span>
								<span class="sr-only">Toggle Dropdown</span>
							</button>
							<ul class="dropdown-menu" role="menu" id="out2_ul">
								<li id="out2_li1"><a onclick="out_action(2,1)">启动</a></li>
								<li id="out2_li2"><a onclick="out_action(2,0)">停止</a></li>
							</ul>
						</div>
					</div>
					<div class="form-group">
						<label for="tag_name_input">其他指令</label>
						<input type="text" class="form-control" id="zhiling_input" placeholder="其他要下发的指令">
						<button type="button" class="btn btn-primary" onclick="submit_zhiling()">提交下发</button>
					</div>
				</div>
			</form>
			</div>
	    <?php include "./footer.php" ?>
	    <div class="control-sidebar-bg"></div>
    </div><!-- ./wrapper -->
    <script src="plugins/jQuery/jQuery-2.1.4.min.js"></script>
    <script type="text/javascript">
		$(document).ready(function(){
			init();
		});
		function out_action(a,b){
			c=$("#D_ID").val();
			$.getJSON("./action/ddata.php",{"handle":"out_action","a":a,"b":b,"D_ID":c},function(data){

			})
		}
		function submit_zhiling(){
			zl=$("#zhiling_input").val();
      if(zl=='reload'){

      }else{
        zl=zl.split("#")[0];//去掉指令后面的#
        zl=zl.split("*")[1];//去掉指令后面的*
      }
			
			console.log(zl);
			c=$("#D_ID").val();
			if(zl){
				$.getJSON("./action/ddata.php",{"handle":"zhiling_action","zl":zl,"D_ID":c},function(data){

				})
			}
		}
		function esc(){
			D_ID=$("#D_ID").val();
			user_id=$("#user_id").val();
			$.getJSON("./action/ddata.php",{"handle":"esc","D_ID":D_ID,"user_id":user_id},function(data){
				if(data==1){
					alert("取消成功");
				}
			})
		}
    function submit_device_name(){
      D_ID=$("#D_ID").val();
      device_icon=$("#device_icon").val();
      var device_name=$("#device_name_input").val();
      var device_serial=$("#device_num_input").val();
      $.getJSON("./action/ddata.php",{"handle":"update_device_name","D_ID":D_ID,"device_name":device_name,"device_icon":device_icon,"device_serial":device_serial},function(data){
          if(data==1){
            alert("提交成功");
            init();
          }else{
            alert("请检查您输入的serial编码");
          }
      })
    }
    function init(){
      $("#out1_ul").empty();
      html1='<li id="out1_li1"><a onclick="out_action(1,1)">启动</a></li><li id="out1_li2"><a onclick="out_action(1,0)">停止</a></li>';
      $("#out1_ul").append(html1);
      $("#out2_ul").empty();
      html2='<li id="out2_li1"><a onclick="out_action(2,1)">启动</a></li><li id="out2_li2"><a onclick="out_action(2,0)">停止</a></li>';
      $("#out2_ul").append(html2);
      D_ID=$("#D_ID").val();
      device_name=$("#device_name_input").val();
      $.getJSON("./action/ddata.php",{"handle":"device_name","D_ID":D_ID},function(data){
        if(data){
          $("#device_icon").val(data[0]["img"]);
          $("#device_name_input").val(data[0]["d_name"]);
          if(data[0]["out1"]=='1'){
            document.getElementById("out1_button").innerHTML="输出1当前状态：输出";
            $("#out1_li1").remove();
          }else if(data[0]["out1"]=='0'){
            document.getElementById("out1_button").innerHTML="输出1当前状态：关闭";
            $("#out1_li2").remove();
          }
          if(data[0]["out2"]=='1'){
            document.getElementById("out2_button").innerHTML="输出2当前状态：输出";
            $("#out2_li1").remove();
          }else if(data[0]["out2"]=='0'){
            document.getElementById("out2_button").innerHTML="输出2当前状态：关闭";
            $("#out2_li2").remove();
          }
          if(data[0]["user_enable"]==0){
            document.getElementById("device_num_input").disabled='';
          }else{
            $("#device_num_input").val(data[0]["serial"]);
          }
        }
      })
    }
    setInterval("init()",5000);
    </script>
    <!-- jQuery UI 1.11.4 -->
    <script src="dist/js/jquery-ui.min.js"></script>
    <!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
    <script>
      $.widget.bridge('uibutton', $.ui.button);
    </script>
    <!-- Bootstrap 3.3.5 -->
    <script src="bootstrap/js/bootstrap.min.js"></script>
    <!-- Morris.js charts -->
    <script src="dist/js/raphael-min.js"></script>
    <script src="plugins/morris/morris.min.js"></script>
    <!-- Sparkline -->
    <script src="plugins/sparkline/jquery.sparkline.min.js"></script>
    <!-- jvectormap -->
    <script src="plugins/jvectormap/jquery-jvectormap-1.2.2.min.js"></script>
    <script src="plugins/jvectormap/jquery-jvectormap-world-mill-en.js"></script>
    <!-- jQuery Knob Chart -->
    <script src="plugins/knob/jquery.knob.js"></script>
    <!-- daterangepicker -->
    <script src="dist/js/moment.min.js"></script>
    <script src="plugins/daterangepicker/daterangepicker.js"></script>
    <!-- datepicker -->
    <script src="plugins/datepicker/bootstrap-datepicker.js"></script>
    <!-- Bootstrap WYSIHTML5 -->
    <script src="plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js"></script>
    <!-- Slimscroll -->
    <script src="plugins/slimScroll/jquery.slimscroll.min.js"></script>
    <!-- FastClick -->
    <script src="plugins/fastclick/fastclick.min.js"></script>
    <!-- AdminLTE App -->
    <script src="dist/js/app.min.js"></script>
    <!-- AdminLTE dashboard demo (This is only for demo purposes) -->

  </body>
</html>
