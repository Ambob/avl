<?php
session_start();
?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>HBD温湿度采集系统</title>
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
          <div class="box box-info">
                <div class="box-header with-border">
                  <h3 class="box-title">申请单</h3>
                </div>
                <form class="form-horizontal">
                  <div class="box-body">
                    <div class="form-group">
                      <label for="inputEmail3" class="col-sm-2 control-label">申请人</label>
                      <div class="col-sm-10">
                        <input type="text" disabled class="form-control" id="shenqing_name" value="" placeholder="">
                      </div>
                    </div>
                    <div class="form-group">
                      <label for="inputPassword3" class="col-sm-2 control-label">申请设备</label>
                      <div class="col-sm-10">
                        <input type="text"  class="form-control" id="device" value="" placeholder="请输入您需要申请的设备编码">
                      </div>
                    </div>
                  </div>
                  <div class="box-footer">
                    <button type="button" class="btn btn-default" onclick="submit_it()">提交</button>
                  </div>
                </form>
              </div>
          </div>
        </section>
      </div>
      <?php include "./footer.php" ?>
      <div class="control-sidebar-bg"></div>
    </div>
    <?php
      $user_id=$_SESSION["ID"];
      echo "<input type='hidden' value='".$user_id."' id='user_id'>";
      $user_name=$_SESSION["nickname"];
      echo "<input type='hidden' value='".$user_name."' id='user_name'>";
    ?>
    <script src="plugins/jQuery/jQuery-2.1.4.min.js"></script>
    <script type='text/javascript'>
      $(document).ready(function(){
        $("#shenqing_name").val($("#user_name").val());
      })

      function submit_it(){
        user_id=$("#user_id").val();
        device=$("#device").val();
        $.getJSON("./action/ddata.php",{"handle":"submit_shenqing","user_id":user_id,"device":device},function(data){
          if(data=='no'){
            alert("您应当是已经具备该设备的查看权了");
          }
          else{
            alert("请耐心等待审核");
            window.open("./device_list.php","_self");
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
