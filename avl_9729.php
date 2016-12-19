<?php
	header("Content-Type: text/html;charset=utf-8");
	include_once "./ezsql/ez_sql_core.php";
	include_once "./ezsql/ez_sql_pdo.php";
	error_reporting(E_ALL & ~E_NOTICE);
	$PORT = 9729;
	$serv = new swoole_server("0.0.0.0", $PORT);
	$serv->set(array(
		'worker_num' => 2,
		'task_worker_num' => 2,
		'daemonize' => 1, //backend excute.
		'log_file' => '/var/www/html/avl_log/avl'.date("Y-m-d").'.html',//输出以日期命名的日志文件
		'heartbeat_idle_time' => 600,
		'heartbeat_check_interval' => 300
	));
	$db = new ezSQL_pdo('mysql:host=localhost;dbname=avl', 'root', '^&^^%)%%');
	$db->query("SET NAMES UTF8"); //会报异常，看看运行如果没有乱码就这样
	function my_onStart($serv)//本系统初始启动时
	{
		global $argv;
		swoole_set_process_name("php {$argv[0]}: master");
		echo "MasterPid={$serv->master_pid}|Manager_pid={$serv->manager_pid}\n <br />";
		echo "Server: start.version is [".SWOOLE_VERSION."]\n <br />";
		echo "machine service IP:".my_ip()."\n <br />";
	}
	function my_ip($dest='64.0.0.0', $port=8099)//貌似只是为了获取本机IP
	{
		$socket = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);
		socket_connect($socket, $dest, $port);
		socket_getsockname($socket, $addr, $port);
		socket_close($socket);
		return $addr;
	}
	function my_onWorkerStart($serv, $worker_id)//看得到的作用是添加了定时器
	{
		global $argv;
		global $interval_set;
		if($worker_id >= $serv->setting['worker_num']) {
			swoole_set_process_name("php {$argv[0]}: task_worker");
		} else {
			swoole_set_process_name("php {$argv[0]}: worker");
		}
		echo "WorkerStart|MasterPid={$serv->master_pid}|Manager_pid={$serv->manager_pid}|WorkerId=$worker_id\n <br />";
		if($worker_id==0)
		{
			$interval=10000;
			echo date('d/m/Y H:i:s')."start timer:$interval ms <br />";
			if(!$serv->addtimer($interval))
			{
				echo "Add timer failed \n";
			}
		}
	}
	function my_onWorkerStop($serv, $worker_id)//不知道做什么用
	{
		echo "WorkerStop[$worker_id]|pid=".posix_getpid().".\n";
	}
	function my_onShutdown($serv)//系统关闭的时候做一些操作
	{
		echo date('Y-m-d H:i:s')."Server: onShutdown\n <br />";
		echo "Offline All Devices\n <br />";
		$db  = $GLOBALS['db'];
		$sql1="update device set fd='-1'";
		$db->query($sql1);
	}	
	function my_onTimer($serv, $interval)//定时器，定时做一些事情
	{
		global $interval_set;

		if($interval == 10000)//每10秒钟发送一次指令
		{
			SendCommand($serv);//发送查询指令
		}else if($interval == $interval_set){
			echo "setting interval \n <br>";
			SendCommand($serv);//发送查询指令
		}
		else{
			echo "ERROR: UNKNOW timer \n <br>";
		}
	}
	function my_onClose($serv, $fd, $from_id)
	{
		echo date('Y-m-d H:i:s')." Client: fd=$fd is closed <br />";
		$db  = $GLOBALS['db'];
		$sql="update device set fd='-1' where fd='".$fd."'";//这样的话是dtu挂了,thread设置为-1
		$result = mysqli_query($con,$sql) or die(mysql_error());
	}
	function my_onConnect($serv, $fd, $from_id)
	{
		//设备连了上来，知道了thread，但是还不知道该设备的名称
		echo date('Y-m-d H:i:s')." Client id=".$fd.":from_id=".$from_id.":client_ip=".$serv->connection_info($fd)['remote_ip']."\n <br />";
		$remote_ip=$serv->connection_info($fd)["remote_ip"];
	}
	function my_onReceive(swoole_server $serv, $fd, $from_id, $data)//当DTU返回了数据，或者是注册包、心跳包、数据包
	{
		$db1 = new ezSQL_pdo('mysql:host=localhost;dbname=avl', 'root', '^&^^%)%%');
		$db1->query("SET NAMES UTF8"); //会报异常，看看运行如果没有乱码就这样
		$cmd = trim($data);
		$recv1=bin2hex($data);
		echo date('Y-m-d H:i:s')." fd=$fd,from_id=$from_id,receive data=".$recv1." \n <br />";
		if(substr($recv1,0,14)=="52656365697665"){//52656365697665是ascii的Receive，也就是上位机发送了指令给avl11后，avl11的返回recieve
			$send_command=substr($recv1,18,6);//取出来指令，以备之后扩展功能
			$ok=substr($recv1,26,4);//指令执行的结果，正确的话是OK(4f4b)
			if($ok=='4f4b'){
				$A_B=substr($recv1,strlen($recv1)-8,2);
				$A_B_value=substr($recv1,strlen($recv1)-3,1);
				$sql="update out_set set status='1' where device_ID=(select ID from device where fd='".$fd."')";
				$db1->query($sql);
				if($A_B=='41'){
					$sql="update device set out1='".$A_B_value."' where fd='".$fd."'";
				}else if($A_B=='42'){
					$sql="update device set out2='".$A_B_value."' where fd='".$fd."'";
				}
				$db1->query($sql);
			}
		}
		else if(substr($recv1,0,4)=="545a"){//545a是一个固定的数据包开头，ascii码是TZ两个字母
			$start_mark=substr($recv1,0,4);
			$length=substr($recv1,4,4);
			$protocol=substr($recv1,8,4);
			$hardware=substr($recv1,12,4);
			$firmware=substr($recv1,16,8);
			$IMEI=substr($recv1,25,15);
			$dtu=substr($recv1,24,16);
			$time=substr($recv1,40,12);
			$time_year='20'.hexdec(substr($time,0,2));
			$time_mon=hexdec(substr($time,2,2));
			$time_day=hexdec(substr($time,4,2));
			$time_hour=hexdec(substr($time,6,2));
			if ($time_hour+8>23){
				$time_hour=hexdec(substr($time,6,2))-16;
			}
			$time_min=hexdec(substr($time,8,2));
			$time_sec=hexdec(substr($time,10,2));
			$time_pin=$time_year."/".$time_mon."/".$time_day." ".$time_hour.":".$time_min;
			$gps_lenght=substr($recv1,52,4);
			$satellite=substr($recv1,56,2);
			$lat=hexdec(substr($recv1,58,8))/600000;
			$lon=hexdec(substr($recv1,66,8))/600000;
			$gps_time=substr($recv1,74,12);
			$gps_speed=substr($recv1,86,4);
			$mileage=substr($recv1,90,6);
			$angle=substr($recv1,96,4);
			$lbs_length=hexdec(substr($recv1,100,4));
			$now=104+$lbs_length*2;
			if ($lbs_length>0){
				$lac=substr($recv1,104,4);
				$cell=substr($recv1,108,4);
			}
			$status_length=hexdec(substr($recv1,$now,4));
			$now=$now+4;
			if ($status_length>0){
				$Alarm_type=substr($recv1,$now,2);
				$termiral=substr($recv1,$now+2,2);
				$IO=base_convert(substr($recv1,$now+4,4),16,2);
				while (strlen($IO)<10){
					$IO="0".$IO;
				}
				$out2=substr($IO,0,1);
				$out1=substr($IO,1,1);
				$IN1=substr($IO,5,1);
				$SOS=substr($IO,9,1);
				$GSM1=substr($recv1,$now+8,2);
				$GSM2=substr($recv1,$now+10,2);
				$battery=hexdec(substr($recv1,$now+12,4))/100;
				$power=hexdec(substr($recv1,$now+16,4))/100;
				$ADA=substr($recv1,$now+20,4);
				$temp1=substr($recv1,$now+24,4);
			}
			$now=$now+$status_length*2;
			$k125=substr($recv1,$now,4);
			$UHF=substr($recv1,$now+4,4);
			$M1=substr($recv1,$now+8,4);
			$now=$now+12;
			$card_length=hexdec(substr($recv1,$now,4))*2;
			if ($card_length!=0){
				$card_num=hexdec(substr($recv1,$now+4,2));
				$for_len=($card_length-4)/$card_num;
				$data_use=substr($recv1,$now+10,$card_num*$for_len);
			}
			$sql="select ID from device where imei='".$IMEI."'";
			$data1 = $db1->get_row($sql);
			if(sizeof($data1)==0){
				$sql="insert into device values (null,'','".$IMEI."','','','".$lac."','".$cell."','','','".$battery."','".$power."','','0','0','0','".time()."','0','0','".$IN1."','".$SOS."','".$out1."','".$out2."','".$ADA."','".$fd."')";
			}else{
				$sql="update device set lac='".$lac."',cell='".$cell."',battery='".$battery."',battery_v='".$power."',last_time='".time()."',`in`='".$IN1."',sos='".$SOS."',out1='".$out1."',out2='".$out2."',ada='".$ADA."',fd='".$fd."' where imei='".$IMEI."'";
			}
			$db1->query($sql);
			for($i=0;$i<strlen($data_use);$i=$i+$for_len){
				$sql='';
				$tag_bat='';
				$tag_num=substr($data_use,$i,8);
				$tag_temp=hexdec(substr($data_use,$i+8,4))/100;
				$tag_hum=hexdec(substr($data_use,$i+12,2));
				if($for_len>16){
					$tag_bat=hexdec(substr($data_use,$i+14,4))/1000;
				}
				
				$value.="(null,(select ID from device where imei='".$IMEI."'),'".$tag_num."','".$tag_temp."','".$tag_hum."','".$tag_bat."','".time()."','','',''),";
			}
			$value=rtrim($value,',');
			if (strlen($value)>10){
				$sql="insert into data_temp values ".$value." on duplicate key update D_Data=values(D_Data),D_Data_hum=values(D_Data_hum),D_Data_v=values(D_Data_v),D_ID=values(D_ID),Real_Time=values(Real_Time),tag_num=values(tag_num)";
				$db1->query($sql);
			}
			
		}else{
			if($cmd == "reload")//下位机发送给server一个重启指令？
			{
				$serv->reload($serv);
			}
			elseif($cmd == "task")
			{
				$task_id = $serv->task("hello world", 0);
				echo "Dispath AsyncTask: id=$task_id\n <br />";
			}
			elseif($cmd == "taskwait")
			{
				$result = $serv->taskwait("hello world");
				echo "SyncTask: result=$result\n <br />";
			}
			elseif($cmd == "info")
			{
				$info = $serv->connection_info($fd);
				$serv->send($fd, 'Info: '.var_export($info, true).PHP_EOL);
			}
			elseif($cmd == "broadcast")
			{
				$start_fd = 0;
				while(true)
				{
					$conn_list = $serv->connection_list($start_fd, 10);
					if($conn_list === false)
					{
						break;
					}
					$start_fd = end($conn_list);
					foreach($conn_list as $conn)
					{
						if($conn === $fd) continue;
					}
				}
			} 
			//这里故意调用一个不存在的函数
			elseif($cmd == "error")
			{
				//  hello_no_exists();
			}
			elseif($cmd == "shutdown")
			{
				$serv->shutdown();
			}
			elseif($cmd == "timer")
			{
				//do nothing.
			}
			else
			{
				if(substr($cmd, 0,2)=='ZG'){
					$zcb=substr($cmd, 2);
					$remote_ip=$serv->connection_info($fd)["remote_ip"];
					$sql="update dtu set ip='".$remote_ip."' where zcb='".$cmd."'";
					echo $sql;
					$result = mysqli_query($con,$sql);
					$sql="select mode from dtu where ip='".$remote_ip."'";
					echo $sql;
					$result = mysqli_query($con,$sql);
					if($result){
						while($row= mysqli_fetch_array($result,MYSQLI_ASSOC)){
							$mode=$row["mode"];
						}
						echo $mode;
						if($mode){
							if($mode=='1'){
								$sql="update device set device_thread='".$fd."',device_status='1' where DTU=(select ID from dtu where ip='".$remote_ip."')";
								echo $sql;
								$result = mysqli_query($con,$sql);
							}
						}

					}
				}
				$data = trim($data);
				$length = strlen($data);
				if($data === "1111")
				{
					echo "Heart beat from:".$fd."\n <br />";
					return;
				}
				
				if(strlen($data) == 5)
				{
					echo "Register command ".$data."\n <br />";
					register_device($data,$fd);
					// SendCommand($serv,$fd,$data);
					return;
				}
				if(strlen($data) > 5)
				{
					$head = substr($data, 0, 3);
					if($head === "GET")
					{
						file_put_contents("get.txt",$_GET["name"]);
						echo "receiv info from web \r <br>";
						return;
					}
					$buffer[$fd].=$data1;
					if(strlen($buffer[$fd])/2>=hexdec(substr($buffer[$fd],0,2))){
						dataprocess($buffer[$fd]);
						$buffer[$fd]='';
					}else{
						echo "need splitt";
					}
					return;
				}
				echo "ERROR:should not be here\n <br />";
			}
		}
	}
function register_device($message,$fd)//暂时无用
{
	echo "register_device\n <br />";
}
function sms($a,$b,$c,$d){//暂时无用
	echo "sms</br>";
	if($c==1){
		$c="高于上限";
	}
	if($c==-1){
		$c="低于下限";
	}
	$reg1=urlencode("#contens#=".$a."的".$b.$c."数值为".$d);
	$db  = $GLOBALS['db'];
	$sql= "select phone from user where (unix_timestamp()-`status`)>600 and `sms`=1";
	$result = mysqli_query($con,$sql);
	if($result){
		while($row= mysqli_fetch_array($result,MYSQLI_ASSOC))
		{
			$phone=$row["phone"];
			$url='http://v.juhe.cn/sms/send?key=6a0b100c3139443a486f23dd4c0ad818&mobile='.$phone.'&tpl_id=6516&tpl_value='.$reg1;
			$html = file_get_contents($url);
			$jsonstr = json_decode($html,true);
			if($jsonstr["error_code"]==0){
				$sql="update user set status='".time()."' where phone='".$phone."'";
				$result = mysqli_query($con,$sql);
			}
		}
	}
}
function SendCommand($serv){
	echo "send_command</br>";
	$db  = $GLOBALS['db'];
	$sql= "select ID,(select fd from device where ID=device_ID) as fd,code from out_set where status=0 and (select fd from device where ID=device_ID)!='-1'";
	$result = $db->get_results($sql);
	if($result){
		foreach ($result as $data){
			$send_command=$data->code;
			if($send_command=='*reload#'){
				$serv->reload(true);
			}else{
				$ID=$data->ID;
				$fd1=$data->fd;
				$send_out1=$serv->send($fd1,$send_command);
				if($send_out1=='1'){
					$sql1="update out_set set send_count=send_count+1 where ID=".$ID;
					$db->query($sql1);
				}
			}

		}
	}
	
}
function Send_order_Command($serv)
{
	
}
function dataprocess($data){

}
function my_onTask(swoole_server $serv, $task_id, $from_id, $data)//还没搞懂
{
	var_dump($data);
	$fd = str_replace('task-', '', $data);
	$serv->send($fd, str_repeat('A', 8192*2));
	$serv->send($fd, str_repeat('B', 8192*2));
	$serv->send($fd, str_repeat('C', 8192*2));
	$serv->send($fd, str_repeat('D', 8192*2));
	return;
	if ($data == "hellotask"){
		broadcast($serv, 0, "hellotask");
	}
	else{
		echo "AsyncTask[PID=".$serv->worker_pid."]: task_id=$task_id.".PHP_EOL;
		return $data;
	}
}
//功能函数，不涉及业务
function String2Hex($string){
	$hex='';
	for ($i=0; $i < strlen($string); $i++){
		$hex .= dechex(ord($string[$i]));
	}
	return $hex;
}
function crc16($data) {
    $string = pack('H*', $data);
	$crc = 0xFFFF;
	for ($x = 0; $x < strlen ($string); $x++) {
		$crc = $crc ^ ord($string[$x]);
		for ($y = 0; $y < 8; $y++) {
			if (($crc & 0x0001) == 0x0001) {
				$crc = (($crc >> 1) ^ 0x8408);
			} else { $crc = $crc >> 1; }
		}
	}
	// $crc;
	$cs= str_pad(dechex($crc%256),2,'0',STR_PAD_LEFT).str_pad((dechex(floor($crc/256))),2,'0',STR_PAD_LEFT);
	return $cs;
}
function checkcrc($message)
{	
	$crc = crc16(substr($message,0,strlen($message)-4));
	if(substr($message,-4)==$crc||substr($message, -2)==substr($crc,0,2))
	return true;
	else
	return false;
}
function my_onFinish(swoole_server $serv, $task_id, $data)
{
	list($str, $fd) = explode('-', $data);
	$serv->send($fd, 'taskok');
	var_dump($str, $fd);
	echo "AsyncTask Finish: result={$data}. PID=".$serv->worker_pid.PHP_EOL;
}
function my_onWorkerError(swoole_server $serv, $worker_id, $worker_pid, $exit_code)
{
	echo "worker abnormal exit. WorkerId=$worker_id|Pid=$worker_pid|ExitCode=$exit_code\n <br />";
}
function broadcast(swoole_server $serv, $fd = 0, $data = "hello")
{
	$start_fd = 0;
	echo "broadcast\n";
	while(true)
	{
		$conn_list = $serv->connection_list($start_fd, 10);
		if($conn_list === false)
		{
			break;
		}
		$start_fd = end($conn_list);
		foreach($conn_list as $conn)
		{
			if($conn === $fd) continue;
			$ret1 = $serv->send($conn, $data);
		}
	}
}
$serv->on('Start', 'my_onStart');
$serv->on('Connect', 'my_onConnect');
$serv->on('Receive', 'my_onReceive');
$serv->on('Close', 'my_onClose');
$serv->on('Shutdown', 'my_onShutdown');
$serv->on('Timer', 'my_onTimer');
$serv->on('WorkerStart', 'my_onWorkerStart');
$serv->on('WorkerStop', 'my_onWorkerStop');
$serv->on('Task', 'my_onTask');
$serv->on('Finish', 'my_onFinish');
$serv->on('WorkerError', 'my_onWorkerError');
$serv->on('ManagerStart', function($serv) {
	global $argv;
	swoole_set_process_name("php {$argv[0]}: manager");
});
$serv->on('Packet',function($serv,$data,$clientInfo){
	echo var_dump($data);
});
$serv->start();