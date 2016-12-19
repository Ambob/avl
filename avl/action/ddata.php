<?php
    // header("Content-Type: text/html;charset=utf-8");
    require_once('./PAPI.php');
    include_once('./step.php');
    include_once('./ch2py_class.php');
     //require_once(__DIR__.'/../../PhpConsole/__autoload.php');
    date_default_timezone_set('Asia/Shanghai');
     //$handler_console = PhpConsole\Handler::getInstance();
     //$handler_console->start();
    session_start();
    $user_id=$_SESSION["ID"];
    $handle=PAPI_GetSafeParam("handle", 0, XH_PARAM_TXT);
    if($handle=="all_device"){
        // $open_id=PAPI_GetSafeParam("open_id", 0, XH_PARAM_TXT);
        $sql="select p1.ID,p1.d_name,p1.serial,p1.battery,p1.battery_v,p1.img,(select max(D_Data) from data_temp where D_ID=p1.ID) as max_data,(select max(D_Data_hum) from data_temp where D_ID=p1.ID) as max_data_hum,(select min(D_Data) from data_temp where D_ID=p1.ID) as min_data,(select min(D_Data_hum) from data_temp where D_ID=p1.ID) as min_data_hum,(select FROM_UNIXTIME(max(Real_Time)) from data_temp where D_ID=p1.ID) as last_time,p1.user_enable,p2.user,p2.quanxian,p1.fd from device as p1 left join user_device as p2 on p2.device=p1.ID where enable='1'";//查找出来所有enable=1即设备已连接，user_enable=0尚未有用户注册的设备
        $data=$db->get_results($sql);
        echo json_encode($data);
    }else if($handle=="device_tag"){
        $D_ID=PAPI_GetSafeParam("D_ID", 0, XH_PARAM_TXT);
        $sql="select tag_num,D_Data,D_Data_hum,FROM_UNIXTIME(Real_Time) as real_time,UNIX_TIMESTAMP()-Real_Time as las,tag_name from data_temp where D_ID='".$D_ID."' order by Real_Time desc";
        $data=$db->get_results($sql);
        echo json_encode($data);
    }else if($handle=="user_device"){
        $sql="select ID,d_name from device where ID in (select device from user_device where user='".$user_id."')";
        $data=$db->get_results($sql);
        echo json_encode($data);
    }else if($handle=="user_device_tag"){
        $sql="select D_ID,tag_num,tag_name from data_temp where D_ID in (select device from user_device where user='".$user_id."')";
        $data=$db->get_results($sql);
        echo json_encode($data);
    }
    else if($handle=='device_tag_list_duibi'){
        $tag_num=PAPI_GetSafeParam("tag_num", 0, XH_PARAM_TXT);
        $wx=PAPI_GetSafeParam("wx", 0, XH_PARAM_TXT);
        $device_id=explode('_',explode(',',$tag_num)[1])[1];
        $tag_num_s=explode(',',$tag_num);
        $tag_num_pin='';
        for ($i=0;$i<sizeof($tag_num_s)-1;$i++){
            $sum.="sum(case tag_num when '".explode('_',$tag_num_s[$i])[0]."' then D_Data else 0 end) '".explode('_',$tag_num_s[$i])[0]."',";
        }
        $sum=rtrim($sum,',');
        if($wx==0){
            $limit=100;
        }else{
            $limit=10;
        }
        $sql="select real_time as y,".$sum." from (select p2.ID,p2.tag_num,p2.D_Data,p2.D_Data_hum,date_format(p2.Real_Time,'%Y-%m-%d %h:%i') as real_time,(select tag_name from data_temp where tag_num=p2.tag_num) as tag_name,(select top from data_temp where tag_num=p2.tag_num) as alarm_top,(select buttom from data_temp where tag_num=p2.tag_num) as alarm_buttom  from data_min_".$device_id." as p2 order by ID desc limit 0,".$limit.") as p1 GROUP by p1.Real_Time order by Real_Time asc";
        $data=$db->get_results($sql);
        echo json_encode($data);
    }
    // else if($handle=='device_tag_list_duibi'){
    //     $tag_num=PAPI_GetSafeParam("tag_num", 0, XH_PARAM_TXT);
    //     $wx=PAPI_GetSafeParam("wx", 0, XH_PARAM_TXT);
    //     $device_id=explode('_',explode(',',$tag_num)[1])[1];
    //     $tag_num_s=explode(',',$tag_num);
    //     $tag_num_pin='';
    //     for ($i=0;$i<sizeof($tag_num_s);$i++){
    //         $tag_num_pin.="or tag_num='".explode('_',$tag_num_s[$i])[0]."' ";
    //     }
    //     $tag_num_pin=rtrim($tag_num_pin,',');
    //     if($wx==0){
    //         $limit=100;
    //     }else{
    //         $limit=10;
    //     }
    //     $sql="select real_time as y,D_Data as wendu,(case  when D_Data_hum>100 then null else D_Data_hum end) as shidu,tag_name,alarm_top,alarm_buttom from (select p2.ID,p2.D_Data,p2.D_Data_hum,date_format(p2.Real_Time,'%Y-%m-%d %h:%i') as real_time,(select tag_name from data_temp where tag_num=p2.tag_num) as tag_name,(select top from data_temp where tag_num=p2.tag_num) as alarm_top,(select buttom from data_temp where tag_num=p2.tag_num) as alarm_buttom  from data_min_".$device_id." as p2 where tag_num='1' ".$tag_num_pin." order by ID desc limit 0,".$limit.") as p1 order by Real_Time asc";
    //     $data=$db->get_results($sql);
    //     echo json_encode($data);
    // }
    else if($handle=='device_tag_list'){
        $D_ID=PAPI_GetSafeParam("D_ID", 0, XH_PARAM_TXT);
        $tag_num=PAPI_GetSafeParam("tag_num", 0, XH_PARAM_TXT);
        $wx=PAPI_GetSafeParam("wx", 0, XH_PARAM_TXT);
        if($wx==0){
            $limit=100;
        }else{
            $limit=10;
        }
        $sql="select real_time as y,D_Data as wendu,(case  when D_Data_hum>100 then null else D_Data_hum end) as shidu,tag_name,alarm_top,alarm_buttom from (select p2.ID,p2.D_Data,p2.D_Data_hum,date_format(p2.Real_Time,'%Y-%m-%d %h:%i') as real_time,(select tag_name from data_temp where tag_num=p2.tag_num) as tag_name,(select top from data_temp where tag_num=p2.tag_num) as alarm_top,(select buttom from data_temp where tag_num=p2.tag_num) as alarm_buttom  from data_min_".$D_ID." as p2 where tag_num=".$tag_num." order by ID desc limit 0,".$limit.") as p1 order by Real_Time asc";
        $data=$db->get_results($sql);
        echo json_encode($data);
    }
    else if($handle=='zhiling_action'){
        $zl=PAPI_GetSafeParam("zl", 0, XH_PARAM_TXT);
        $D_ID=PAPI_GetSafeParam("D_ID", 0, XH_PARAM_TXT);
        $send_command='*'.$zl.'#';
        $sql="insert into out_set (device_ID,code,real_time) values ('".$D_ID."','".$send_command."','".time()."')";
        $data=$db->query($sql);
        echo json_encode($data);
    }
    else if($handle=='out_action'){
        $a=PAPI_GetSafeParam("a", 0, XH_PARAM_TXT);
        $b=PAPI_GetSafeParam("b", 0, XH_PARAM_TXT);
        $D_ID=PAPI_GetSafeParam("D_ID", 0, XH_PARAM_TXT);
        if($a=='1'){
            $send_command='*000000,025,A,'.$b.'#';
            $sql="insert into out_set (device_ID,code,real_time) values ('".$D_ID."','".$send_command."','".time()."')";
        }else if($a=='2'){
            $send_command='*000000,025,B,'.$b.'#';
            $sql="insert into out_set (device_ID,code,real_time) values ('".$D_ID."','".$send_command."','".time()."')";
        }
        $data=$db->query($sql);
        echo json_encode($data);
    }
    else if($handle=='update_tag_name'){
        $D_ID=PAPI_GetSafeParam("D_ID", 0, XH_PARAM_TXT);
        $tag_num=PAPI_GetSafeParam("tag_num", 0, XH_PARAM_TXT);
        $tag_name=PAPI_GetSafeParam("tag_name", 0, XH_PARAM_TXT);
        $alarm_top=PAPI_GetSafeParam("alarm_top", 0, XH_PARAM_TXT);
        $alarm_buttom=PAPI_GetSafeParam("alarm_buttom", 0, XH_PARAM_TXT);
        $sql="select quanxian from user_device where device='".$D_ID."' and user='".$user_id."' limit 1";
        $data=$db->get_row($sql);
        $quanxian=$data->quanxian;
        if($quanxian==2){
            $sql="update data_temp set tag_name='".$tag_name."',top='".$alarm_top."',buttom='".$alarm_buttom."' where D_ID='".$D_ID."' and tag_num='".$tag_num."'";
            $data=$db->query($sql);
        }else{
            $data='no';
        }
        echo json_encode($data);
    }else if($handle=='device_name'){
        $D_ID=PAPI_GetSafeParam("D_ID", 0, XH_PARAM_TXT);
        $sql="select p1.d_name,p1.serial,p1.img,p1.user_enable,p1.out1,p1.out2,p1.in,p1.sos,p1.ada,p2.battery,p2.power,p2.lac,p2.cell from device as p1 left join device_info as p2 on p2.D_ID=p1.ID where p1.ID='".$D_ID."' ORDER BY p2.ID desc limit 1";
        $data=$db->get_results($sql);
        echo json_encode($data);
    }else if($handle=='update_device_name'){
        $D_ID=PAPI_GetSafeParam("D_ID", 0, XH_PARAM_TXT);
        $device_name=PAPI_GetSafeParam("device_name", 0, XH_PARAM_TXT);
        $device_icon=PAPI_GetSafeParam("device_icon", 0, XH_PARAM_TXT);
        $device_serial=PAPI_GetSafeParam("device_serial", 0, XH_PARAM_TXT);
        $sql="update device set d_name='".$device_name."',img='".$device_icon."',user_enable='".$user_id."' where ID='".$D_ID."' and serial='".$device_serial."'";
        $data=$db->query($sql);
        if($data){
            $sql="insert into user_device values (null,'".$user_id."','".$D_ID."',2)";
            $data1=$db->query($sql);
        }
        echo json_encode($data);
    }else if($handle=='esc'){
        $D_ID=PAPI_GetSafeParam("D_ID", 0, XH_PARAM_TXT);
        $user_id=PAPI_GetSafeParam("user_id", 0, XH_PARAM_TXT);
        $sql="update user_device set quanxian='0' where user='".$user_id."' and device='".$D_ID."'";
        $data=$db->query($sql);
        echo json_encode($data);
    }else if($handle=='all_shenqing'){
        $sql="select p1.ID,(select nick_name from user where ID=p1.user) as shenqing_name,(select d_name from device where ID=p1.device) as device from user_device as p1 where (select user from user_device where quanxian=2 and device=p1.device limit 1)='".$user_id."' and p1.quanxian=0";
        $data=$db->get_results($sql);
        echo json_encode($data);
    }else if($handle=='agree_shenqing'){
        $ID=PAPI_GetSafeParam("ID", 0, XH_PARAM_TXT);
        $yes_no=PAPI_GetSafeParam("yes_no", 0, XH_PARAM_TXT);
        if($yes_no=='1'){
            $sql="update user_device set quanxian=1 where ID='".$ID."'";
        }else{
            $sql="update user_device set quanxian=9 where ID='".$ID."'";
        }
        $data=$db->query($sql);
        echo json_encode($data);
    }else if($handle=='submit_shenqing'){
        $user_id=PAPI_GetSafeParam("user_id", 0, XH_PARAM_TXT);
        $device=PAPI_GetSafeParam("device", 0, XH_PARAM_TXT);
        $sql="select ID from user_device where device=(select ID from device where serial='".$device."') and user='".$user_id."'";
        $data=$db->get_row($sql);
        if(sizeof($data)==0){
            $sql="insert into user_device values (null,'".$user_id."',(select ID from device where serial='".$device."'),0,0)";
            $data=$db->query($sql);
        }else{
            $data='no';
        }
        echo json_encode($data);
    }else if($handle=='person_info'){
        $sql="select * from user where ID='".$user_id."'";
        $data=$db->get_results($sql);
        echo json_encode($data);
    }else if($handle=='edit_person'){
        $ID=PAPI_GetSafeParam("ID", 0, XH_PARAM_TXT);
        $phone=PAPI_GetSafeParam("phone", 0, XH_PARAM_TXT);
        $alarm=PAPI_GetSafeParam("alarm", 0, XH_PARAM_TXT);
        $user_name=PAPI_GetSafeParam("user_name", 0, XH_PARAM_TXT);
        $pass=PAPI_GetSafeParam("pass", 0, XH_PARAM_TXT);
        $email=PAPI_GetSafeParam("email", 0, XH_PARAM_TXT);
	$sql="select ID from user where user_name='".$user_name."'";
        $data=$db->get_row($sql);
        if(sizeof($data)>0){
            $sql="update user set email='".$email."',phone='".$phone."',alarm='".$alarm."',user_name='".$user_name."',pass='".$pass."' where ID='".$ID."'";
		$data=$db->query($sql);
        }else{
            $data='no';
        }
        echo json_encode($data);
    }else if($handle=='login'){
        $user_name=PAPI_GetSafeParam("user_name", 0, XH_PARAM_TXT);
        $pass=PAPI_GetSafeParam("pass", 0, XH_PARAM_TXT);
        $company=PAPI_GetSafeParam("company", 0, XH_PARAM_TXT);
        $sql="select p1.*,p2.company_more,p2.url from user as p1 left join company as p2 on p2.ID=p1.company where p1.user_name='".$user_name."' and p1.pass='".$pass."' and p1.company=(select ID from company where company='".$company."')";
        //$handler_console->debug($sql)
        //echo $sql;
        $data=$db->get_row($sql);
            $_SESSION["nickname"]=$data->nick_name;
            $_SESSION["headimgurl"]=$data->head_img;
            $_SESSION["reg_time"]=$data->real_time;
            $_SESSION["ID"]=$data->ID;
            $_SESSION["company_more"]=$data->company_more;
            $_SESSION["url"]=$data->url;
            echo json_encode($data);
    }
?>
