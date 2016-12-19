<?php
session_start();
?>
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
  </head>
  <!--修改skin-blue  改变皮肤风格-->
  <body class="hold-transition skin-blue sidebar-mini">
    <div class="wrapper">
      <?php include "./header.php" ?>
      <div class="content-wrapper">
        <section class="content">
          <div class="row" id="main">
          </div>
        </section>
      </div>
      <?php include "./footer.php" ?>
      <div class="control-sidebar-bg"></div>
    </div>
    <?php
      $user_id=$_SESSION["ID"];
      echo "<input type='hidden' value='".$user_id."' id='user_id'>";
    ?>
    <script src="plugins/jQuery/jQuery-2.1.4.min.js"></script>
    <script type='text/javascript'>
      $(document).ready(function(){
        init();
      })
      function init(){
        $.getJSON("./action/ddata.php",{"handle":"person_info"},function(data){
          if(data){
            html='';
            for(var i=0;i<data.length;i++){
              ID=data[i]["ID"];
              nick_name=data[i]["nick_name"];
              phone=data[i]["phone"];
	      email=data[i]["email"];
              alarm=data[i]["alarm"];
              user_name=data[i]["user_name"];
              pass=data[i]["pass"];
              html+='<div class="box box-info">';
                html+='<div class="box-header with-border">';
                  html+='<h3 class="box-title">个人信息</h3>';
                html+='</div>';
                  html+='<div class="box-body">';
                    html+='<div class="form-group">';
                      html+='<label for="inputEmail3" class="col-sm-2 control-label">昵称</label>';
                      html+='<div class="col-sm-10">';
                        html+='<input type="text" disabled class="form-control" id="nick_name" value="'+nick_name+'" placeholder="">';
                      html+='</div>';
                    html+='</div>';
                    html+='<div class="form-group">';
                      html+='<label for="inputPassword3" class="col-sm-2 control-label">联系电话</label>';
                      html+='<div class="col-sm-10">';
                        html+='<input type="text" class="form-control" id="phone" value="'+phone+'" placeholder="">';
                      html+='</div>';
                    html+='</div>';
		    html+='<div class="form-group">';
                      html+='<label for="inputMail" class="col-sm-2 control-label">E_MAIL</label>';
                      html+='<div class="col-sm-10">';
                        html+='<input type="text" class="form-control" id="email" value="'+email+'" placeholder="">';
                      html+='</div>';
                    html+='</div>';
                    html+='<div class="form-group">';
                      html+='<label for="inputPassword3" class="col-sm-2 control-label">用户名</label>';
                      html+='<div class="col-sm-10">';
                        html+='<input type="text" class="form-control" id="user_name" value="'+user_name+'" placeholder="电脑登陆用">';
                      html+='</div>';
                    html+='</div>';
                    html+='<div class="form-group">';
                      html+='<label for="inputPassword3" class="col-sm-2 control-label">密码</label>';
                      html+='<div class="col-sm-10">';
                        html+='<input type="password" class="form-control" id="pass" value="'+pass+'" placeholder="电脑登陆用">';
                      html+='</div>';
                    html+='</div>';
                    html+='<div class="form-group">';
                      html+='<label for="inputPassword3" class="col-sm-2 control-label">是否接收报警信息</label>';
                      html+='<div class="col-sm-10">';
                        html+='<select class="form-control" id="alarm" style="width: 100%;">';
                        if(alarm=='0'){
                          html+='<option selected="selected" value="0">不接收</option>';
                          html+='<option value="1">接收</option>';
                        }else{
                          html+='<option  value="0">不接收</option>';
                          html+='<option selected="selected" value="1">接收</option>';
                        }
                        html+='</select>';
                      html+='</div>';
                    html+='</div>';
                  html+='</div>';
                  html+='<div class="box-footer">';
                    html+='<button type="text" class="btn btn-default" onclick="submit_it('+ID+')">提交</button>';
                  html+='</div>';
              html+='</div>';
            }
            $("#main").empty();
            $("#main").append(html);
          }
        })
      }
      function submit_it(a){
        phone=$("#phone").val();
	email=$("#email").val();
        user_name=$("#user_name").val();
        pass=$("#pass").val();
        alarm=document.getElementById("alarm").value;
        $.getJSON("./action/ddata.php",{"handle":"edit_person","ID":a,"phone":phone,'email':email,"alarm":alarm,"user_name":user_name,"pass":pass},function(data){
          if(data){
            if(data=='no'){
              alert("这个用户名已经被占用了，请修改!");
            }else{
              alert("提交成功");
              window.open("./person.php","_self");
            }
          }
        })
      }
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
    <!-- <script src="dist/js/pages/dashboard.js"></script> -->
    <!-- AdminLTE for demo purposes -->
    <!-- <script src="dist/js/demo.js"></script> -->
  </body>
</html>
