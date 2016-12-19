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
      function GetQueryString(name){
           var reg = new RegExp("(^|&)"+ name +"=([^&]*)(&|$)");
           var r = window.location.search.substr(1).match(reg);
           if(r!=null)return  unescape(r[2]); return null;
      }
      function init(){
        $.getJSON("./action/ddata.php",{"handle":"all_device"},function(data){
          if(data){
            html='';
            for(var i=0;i<data.length;i++){
              name=data[i]["d_name"];
              serial=data[i]["serial"];
              user_enable=data[i]["user_enable"];
              battery=data[i]["battery"];
              battery_v=data[i]["battery_v"];
              last_time=data[i]["last_time"];
              max_data=data[i]["max_data"];
              min_data=data[i]["min_data"];
              max_data_hum=data[i]["max_data_hum"];
              min_data_hum=data[i]["min_data_hum"];
              device_icon=data[i]["img"];
              user=data[i]["user"];
              user_enable=data[i]["user_enable"];
              quanxian=data[i]["quanxian"];
              D_ID=data[i]["ID"];
              fd=data[i]["fd"];
              user_id=$("#user_id").val();
              if(user_id==user  || user_enable=='0'){
                html+='<div class="col-md-3 col-sm-6 col-xs-12">';
                  html+='<div class="info-box">';
                    html+='<span class="info-box-icon bg-aqua" onclick="device_edit('+D_ID+','+user_enable+')"><img src="./images/'+device_icon+'.png"/></span>';
                    html+='<div class="info-box-content" onclick="device_tag_list('+D_ID+','+user+','+quanxian+')">';
                      html+='<span class="info-box-text">'+name+'</span>';
                      html+='<span class="info-box-text">------------------------</span>';
                      if(fd=='-1'){
                        html+='<span class="info-box-text">最近数据(当前离线):'+last_time+'</span>';
                      }else{
                        html+='<span class="info-box-text">最近数据:'+last_time+'</span>';
                      }
                      if(max_data_hum>100){
                        html+='<span class="info-box-text">↑:'+max_data+'<small>℃</small>↓:'+min_data+'<small>℃</small></span>';
                      }else{
                        html+='<span class="info-box-text">↑:'+max_data+'<small>℃/'+max_data_hum+'%</small>↓:'+min_data+'<small>℃/'+min_data_hum+'%</small></span>';
                      }
                    html+='</div>';
                  html+='</div>';
                html+='</div>';
              }
            }
            $("#main").empty();
            $("#main").append(html);
          }
        })
      }
      setInterval('init()',60000);
      function device_tag_list(a,b,c){
        // window.open('./device_tag_list.php?D_ID='+a,'_self');
        //b是user_enable ，不为0就是有用户注册过了
        if(c=='0'){
          alert('请耐心等待审核');
        }else{
          if(b!=0){
            window.open('./device_tag_list.php?D_ID='+a,'_self');
          }else{
            alert("请点击左侧图标验证该设备,或者提交查看申请");
          }
        }
        
      }
      function device_edit(a,b){
        window.open('./device_edit.php?D_ID='+a+'&user_enable='+b,'_self');
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
