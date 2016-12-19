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
    <div class="wrapper">
      <?php include "./header.php" ?>
      <div class="content-wrapper">
        <section class="content">
          <div class="box box-solid bg-teal-gradient">
            <div class="box-header">
              <i class="fa fa-th"></i>
              <h3 class="box-title">对比曲线</h3>
            </div>
            <div class="box-body border-radius-none">
              <div class="chart" id="line-chart" style="height: 250px;"></div>
            </div><!-- /.box-body -->
          </div>
        </section><!-- /.content -->
        <div id="for_append">

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
    function isWeiXin(){
      var ua = window.navigator.userAgent.toLowerCase();
      if(ua.match(/MicroMessenger/i) == 'micromessenger'){
          return true;
      }else{
          return false;
      }
    }
    function init(){
      $.getJSON("./action/ddata.php",{"handle":"user_device"},function(data){
        if(data){
          html='';
          for(var i=0;i<data.length;i++){
            id=data[i]["ID"];
            html+='<div class="col-md-6">';
              html+='<div class="box box-warning">';
                  html+='<div class="box-header with-border">';
                    html+='<h3 class="box-title">'+data[i]["d_name"]+'</h3>';
                  html+='</div>';
                  html+='<div class="box-body">';
                    html+='<div class="checkbox" id="device_'+id+'" style="height:150px">';
                    html+='</div>';
                  html+='</div>';
              html+='</div>';
            html+='</div>';
          }
          $("#for_append").empty();
          $("#for_append").append(html);
        }
        $.getJSON("./action/ddata.php",{"handle":"user_device_tag"},function(data){
          if(data){
            for(var i=0;i<data.length;i++){
              tag_num=data[i]["tag_num"];
              device_id=data[i]["D_ID"];
              htmll='';
              htmll+='<label>';
                htmll+='<input type="checkbox" onclick="all_checked()" id="'+tag_num+'_'+device_id+'">'+tag_num;
              htmll+='</label>';
              $("#device_"+device_id).append(htmll);
            }
          }
        })
      })
    }
    var tag_nums='';
    function all_checked(){
      tag_nums=new Array();
      for(var i=0;i<$("#for_append").find("input").length;i++){
        var value=$("#for_append").find("input")[i].checked;
        if(value==true){
          var tag_num=$("#for_append").find("input")[i].id;
          tag_nums+=tag_num+',';
        }
      }
      init1();
    }
    function init1(){
      wx=0;
      if(isWeiXin()){
        wx=1;
      }else{
        wx=0;
      }
      $.getJSON("./action/ddata.php",{"handle":"device_tag_list_duibi","tag_num":tag_nums,"wx":wx},function(data){
        if(data){
          i=0;
          labels="[";
          ykeys="[";
          color="[";
          for (var d in data[0]){
            if(i>0){
              labels+="'"+d+"',";
              ykeys+="'"+d+"',";
              se='#'+('00000'+(Math.random()*0x1000000<<0).toString(16)).slice(-6);//产生随机颜色
              color+="'"+se+"',";
            }
            i=i+1;
          }
          labels=labels.substr(0,labels.length-1);
          ykeys=ykeys.substr(0,ykeys.length-1);
          color=color.substr(0,color.length-1);
          labels+="]";
          ykeys+="]";
          color+="]";
          labels=eval(labels);
          ykeys=eval(ykeys);
          color=eval(color);
          $("#line-chart").empty();
          var line = new Morris.Line({
            element: 'line-chart',
            resize: true,
            data: data,
            xkey: 'y',
            ykeys: ykeys,
            labels: labels,
            lineColors: color,
            lineWidth: 2,
            hideHover: false,
            gridTextColor: "#fff",
            gridStrokeWidth: 0.4,
            pointSize: 4,
            pointStrokeColors: ["#efefef"],
            gridLineColor: "#efefef",
            gridTextFamily: "Open Sans",
            gridTextSize: 10,
            postUnits:""
          });
        }
      })
    }
    // setInterval('init()',60000);
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
