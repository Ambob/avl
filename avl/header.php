<?php 
  session_start();
?>
<header class="main-header">
    <nav class="navbar navbar-static-top" role="navigation">
      <!-- Sidebar toggle button-->
      <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
        <span class="sr-only">Toggle navigation</span>
      </a>
      <!--右上角的一群小图标-->
      <div class="navbar-custom-menu">
        <ul class="nav navbar-nav">
          <li class="dropdown messages-menu">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
              <i class="fa fa-envelope-o"></i>
              <span class="label label-success">1</span>
            </a>
            <ul class="dropdown-menu">
              <li class="header">你有1封新信息</li>
              <li>
                <ul class="menu">
                  <li>
                    <a href="#">
                      <div class="pull-left">
                        <img src="dist/img/user2-160x160.jpg" class="img-circle" alt="User Image">
                      </div>
                      <h4>
                        HBD欢迎您
                        <!-- <small><i class="fa fa-clock-o"></i> 5 mins</small> -->
                      </h4>
                      <p>欢迎使用HBD温湿度采集系统</p>
                    </a>
                  </li><!-- end message -->
                </ul>
              </li>
            </ul>
          </li>
          <li class="dropdown notifications-menu">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
              <i class="fa fa-bell-o"></i>
              <span class="label label-warning"></span>
            </a>
            <ul class="dropdown-menu">
 <!--              <li class="header">有1条报警信息需要处理</li>
              <li>
                <ul class="menu">
                  <li>
                    <a href="#">
                      <i class="fa fa-users text-aqua"></i> <b>变流仓</b>温度过高，超过上限25摄氏度，请及时处理
                    </a>
                  </li>
                </ul>
              </li> -->
              <li class="footer"><a href="#">查看所有</a></li>
            </ul>
          </li>
          <li class="dropdown user user-menu">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
            <?php
                $headimgurl=$_SESSION['headimgurl'];
                echo'<img src="'.$headimgurl.'" width="24px" class="img-circle" alt="User Image">';
              ?>
              <span class="hidden-xs"><?php echo $_GET["nickname"]?></span>
            </a>
            <ul class="dropdown-menu">
              <li class="user-header">
                <p>
                  <?php 
                    $headimgurl=$_SESSION['headimgurl'];
                    echo'<img src="'.$headimgurl.'" width="80px" class="img-circle" alt="User Image">';
                  ?>
                <p>
                  <?php echo $_SESSION["nickname"]?> - 普通用户
                  <small>注册时间: <?php echo $_SESSION["reg_time"]?></small>
                </p>
              </li>
        </ul>
      </div>
    </nav>
 </header>
 <aside class="main-sidebar">
    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">
      <!-- Sidebar user panel -->
      <div class="user-panel">
        <div class="pull-left image">
          <?php 
            $headimgurl=$_SESSION['headimgurl'];
            echo'<img src="'.$headimgurl.'" class="img-circle" alt="User Image">';
          ?>
        </div>
        <div class="pull-left info">
          <p><?php echo $_SESSION["nickname"]?></p>
          <a href="#"><i class="fa fa-circle text-success"></i> 在线</a>
        </div>
      </div>
      </form>
      <ul class="sidebar-menu">
        <li class="header">导航栏</li>
        <li class="active treeview">
          <a href="#">
            <i class="fa fa-dashboard"></i> <span>设备管理</span> <i class="fa fa-angle-left pull-right"></i>
          </a>
          <ul class="treeview-menu">
            <li class="active"><a href="./device_list.php"><i class="fa fa-circle-o"></i> 所有设备</a></li>
          </ul>
        </li>
        <li class="treeview">
          <a href="#">
            <i class="fa fa-dashboard"></i> <span>申请管理</span> <i class="fa fa-angle-left pull-right"></i>
          </a>
          <ul class="treeview-menu">
            <li class="active"><a href="./shenqing.php"><i class="fa fa-circle-o"></i>所有申请</a></li>
            <li class=""><a href="./add_shenqing.php"><i class="fa fa-circle-o"></i>添加申请</a></li>
          </ul>
        </li>
        <li class="treeview">
          <a href="#">
            <i class="fa fa-dashboard"></i> <span>数据分析</span> <i class="fa fa-angle-left pull-right"></i>
          </a>
          <ul class="treeview-menu">
            <li class="active"><a href="./duibi.php"><i class="fa fa-circle-o"></i>数据对比</a></li>
            <!-- <li class=""><a href="./add_shenqing.php"><i class="fa fa-circle-o"></i>添加申请</a></li> -->
          </ul>
        </li>
        <li class="treeview">
          <a href="#">
            <i class="fa fa-dashboard"></i> <span>个人中心</span> <i class="fa fa-angle-left pull-right"></i>
          </a>
          <ul class="treeview-menu">
            <li class="active"><a href="./person.php"><i class="fa fa-circle-o"></i>个人设置</a></li>
          </ul>
        </li>
        <!-- <li><a href="documentation/index.php"><i class="fa fa-book"></i> <span>帮助文档</span></a></li> -->
      </ul>
    </section>
    <!-- /.sidebar -->
</aside>
