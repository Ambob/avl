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
    $D_ID=$_GET["D_ID"];
    echo "<input type='hidden' value='".$D_ID."' id='D_ID'>";
  ?>
    <div class="wrapper">

      <?php include "./header.php" ?>

      <!-- Content Wrapper. Contains page content -->
      <div class="content-wrapper">
        <!-- Main content -->
        <section class="content">
<!--           <div class="row" id="IO">
            <a class="btn btn-app" style="min-width: 60px">
              <i class="fa"></i> in1
            </a>
            <a class="btn btn-app" style="min-width: 60px">
              <i class="fa fa-play"></i> in2
            </a>
            <a class="btn btn-app" style="min-width: 60px">
              <i class="fa fa-play"></i> ADA
            </a>
            <a class="btn btn-app" style="min-width: 60px">
              <i class="fa fa-play"></i> out1
            </a>
            <a class="btn btn-app" style="min-width: 60px">
              <i class="fa fa-play"></i> out2
            </a>
          </div> -->
          <!-- Small boxes (Stat box) -->
          <div class="row" id="main">
            
          </div><!-- /.row -->
        </section><!-- /.content -->
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
    function init(){
      D_ID=$("#D_ID").val();
      $.getJSON("./action/ddata.php",{"handle":"device_tag","D_ID":D_ID},function(data){
        if(data){
          html='';
          for(var i=0;i<data.length;i++){
            tag_num=data[i]["tag_num"];
            D_Data=data[i]["D_Data"];
            D_Data_hum=data[i]["D_Data_hum"];
            real_time=data[i]["real_time"];
            las=data[i]["las"];
            tag_name=data[i]["tag_name"];
            if(tag_name){

            }else{
              tag_name='暂无';
            }
            html+='<div class="col-lg-3 col-xs-6">';
            if(las>660){
              html+='<div class="small-box bg-yellow">';
            }else{
              html+='<div class="small-box bg-aqua">';
            }
                html+='<div class="inner">';
                if(D_Data_hum>100){
                  html+='<p><font size="3">'+D_Data+'℃</font></p>';
                }else{
                  html+='<p><font size="3">'+D_Data+'℃/'+D_Data_hum+'%</font></p>';
                }
                if(tag_name.length>6){
                  tag_name=tag_name.substring(0,6);
                }
                  html+='<p>'+tag_num+'/'+tag_name+'</p>';
                  html+='<p>'+real_time+'</p>';
                html+='</div>';
                html+='<div class="icon">';
                  html+='<i class="ion"></i>';
                html+='</div>';
                html+='<a href="./device_tag_more.php?D_ID='+D_ID+'&tag_num='+tag_num+'" class="small-box-footer">更多<i class="fa fa-arrow-circle-right"></i></a>';
              html+='</div>';
            html+='</div>';
          }
          $("#main").empty();
          $("#main").append(html);
        }
      })
    }
    setInterval('init()',5000);
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
