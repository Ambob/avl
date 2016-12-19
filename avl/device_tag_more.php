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

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
        <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
  </head>
  <body class="hold-transition skin-blue sidebar-mini">
  <?php
    $tag_num=$_GET["tag_num"];
    echo "<input type='hidden' value='".$tag_num."' id='tag_num'>";
    $D_ID=$_GET["D_ID"];
    echo "<input type='hidden' value='".$D_ID."' id='D_ID'>";
  ?>
    <div class="wrapper">

      <?php include "./header.php" ?>

      <!-- Content Wrapper. Contains page content -->
      <div class="content-wrapper">
        <!-- Main content -->
        <section class="content">
          <!-- Small boxes (Stat box) -->
          <div class="box box-solid bg-teal-gradient">
            <div class="box-header">
              <i class="fa fa-th"></i>
              <h3 class="box-title">实时曲线</h3>
            </div>
            <div class="box-body border-radius-none">
              <div class="chart" id="line-chart" style="height: 250px;"></div>
            </div><!-- /.box-body -->
          </div>
        </section><!-- /.content -->
        <div class="box box-primary">
          <div class="box-header with-border">
            <h3 class="box-title">标签名称</h3>
          </div><!-- /.box-header -->
          <!-- form start -->
          <form role="form">
            <div class="box-body">
              <div class="form-group">
                <label for="tag_num_input">标签号</label>
                <input type="text" class="form-control" id="tag_num_input" disabled="disabled" placeholder="Enter email">
              </div>
              <div class="form-group">
                <label for="tag_name_input">安装位置</label>
                <input type="text" class="form-control" id="tag_name_input" placeholder="安装位置，起一个容易记住的名字">
              </div>
              <div class="form-group">
                <label for="tag_name_input">温度报警上限</label>
                <input type="text" class="form-control" id="alarm_top" placeholder="超过本值将发出报警信息">
              </div>
              <div class="form-group">
                <label for="tag_name_input">温度报警下限</label>
                <input type="text" class="form-control" id="alarm_buttom" placeholder="低过本值将发出报警信息">
              </div>
            <div class="box-footer">
              <button type="button" class="btn btn-primary" onclick="submit_tag_name()">提交修改</button>
            </div>
          </form>
        </div><!-- /.box -->
      </div><!-- /.content-wrapper -->
      <?php include "./footer.php" ?>
      <!-- Add the sidebar's background. This div must be placed
           immediately after the control sidebar -->
      <div class="control-sidebar-bg"></div>
    </div><!-- ./wrapper -->
    <!-- jQuery 2.1.4 -->
    <script src="plugins/jQuery/jQuery-2.1.4.min.js"></script>
    <script type="text/javascript">
    $(document).ready(function(){
      init();
    });
    function submit_tag_name(){
      tag_num=$("#tag_num").val();
      D_ID=$("#D_ID").val();
      alarm_top=$("#alarm_top").val();
      alarm_buttom=$("#alarm_buttom").val();
      var tag_name=$("#tag_name_input").val();
      $.getJSON("./action/ddata.php",{"handle":"update_tag_name","D_ID":D_ID,"tag_num":tag_num,"tag_name":tag_name,"alarm_top":alarm_top,"alarm_buttom":alarm_buttom},function(data){
          if(data=='no'){
            alert("请联系管理员进行修改");
          }else{
            alert("修改成功");
            init();
          }
        })
    }
    function isWeiXin(){
      var ua = window.navigator.userAgent.toLowerCase();
      if(ua.match(/MicroMessenger/i) == 'micromessenger'){
          return true;
      }else{
          return false;
      }
  }
    function init(){
      wx=0;
      tag_num=$("#tag_num").val();
      D_ID=$("#D_ID").val();
      $("#tag_num_input").val(tag_num);
      if(isWeiXin()){
        wx=1;
      }else{
        wx=0;
      }
      $.getJSON("./action/ddata.php",{"handle":"device_tag_list","D_ID":D_ID,"tag_num":tag_num,"wx":wx},function(data){
        if(data){
          $("#tag_name_input").val(data[0]["tag_name"]);
          $("#alarm_top").val(data[0]["alarm_top"]);
          $("#alarm_buttom").val(data[0]["alarm_buttom"]);
          if(data[0]["shidu"]){
            labels=['温度','湿度'];
            ykeys=['wendu','shidu'];
          }else{
            labels=['温度'];
            ykeys=['wendu'];
          }
          var line = new Morris.Line({
            element: 'line-chart',
            resize: true,
            data: data,
            xkey: 'y',
            ykeys: ykeys,
            labels: labels,
            lineColors: ['#efefef'],
            lineWidth: 2,
            hideHover: 'auto',
            gridTextColor: "#fff",
            gridStrokeWidth: 0.4,
            pointSize: 4,
            pointStrokeColors: ["#efefef"],
            gridLineColor: "#efefef",
            gridTextFamily: "Open Sans",
            gridTextSize: 10
          });
        }
      })
    }
    setInterval('init()',60000);
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
