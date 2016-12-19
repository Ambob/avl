<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<?php
include("./action/step.php");
include("./action/step_wx.php");
?>
<?php
	$company=$_GET["company"];
	if($company=='liuchang'){
		
	}else{
		$company='hbd';
	}
	$sql="select appid,secret,access_token,(UNIX_TIMESTAMP()-last_time) as las from wechat where company_name='".$company."'";
	$data=$db_wx->get_row($sql);
	if($data){
		$appid = $data->appid;
		$secret=$data->secret;
		$access_token=$data->access_token;
		$las=$data->las; 
	}
	$sql="select company_more,ID from company where company='".$company."'";
	$data=$db->get_row($sql);
	$company_more=$data->company_more;
	$company_ID=$data->ID;
	$code = $_GET["code"];
	$get_token_url = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid='.$appid.'&secret='.$secret.'&code='.$code.'&grant_type=authorization_code';
	$content =file_get_contents($get_token_url);
	$arr = json_decode($content,true);
	$openid = $arr['openid'];
	if($openid){
		$sql="select p1.ID,p1.nick_name,p1.head_img,from_unixtime(p1.real_time) as real_time,(select company_more from company where ID=p1.company) as company_more from user as p1 where p1.openid='".$openid."'";
		$data=$db->get_row($sql);
		session_start();
		if($data){
			$_SESSION["nickname"]=$data->nick_name;
			$_SESSION["headimgurl"]=$data->head_img;
			$_SESSION["reg_time"]=$data->real_time;
			$_SESSION["ID"]=$data->ID;
			$_SESSION["company_more"]=$data->company_more;
			$sql="update user set last_time='".time()."' where openid='".$openid."'";
			$db->query($sql);
			$sql='insert into user_log values (null,(select ID from user where openid="'.$openid.'"),"'.time().'")';
			$db->query($sql);
			header("Location:device_list.php");
		}else{
			$access_token=$arr['access_token'];
			$get_token_ur='https://api.weixin.qq.com/sns/userinfo?access_token='.$access_token.'&openid='.$openid.'&lang=zh_CN';
			$content1 =file_get_contents($get_token_ur);
			$arr1 = json_decode($content1,true);
			$nickname=$arr1['nickname'];
			$headimgurl=$arr1['headimgurl'];
			$_SESSION["nickname"]=$nickname;
			$_SESSION["headimgurl"]=$headimgurl;
			$_SESSION["reg_time"]=date('Y-m-d H:i:s', time());
			$_SESSION["company_more"]=$company_more;
			$sql='insert into user values (null,"'.$nickname.'","'.$openid.'","'.time().'","'.$headimgurl.'","","","'.time().'","1","","","'.$company_ID.'")';
			$data=$db->query($sql);
			$insert_id=$db->insert_id;
			$sql='insert into user_device values (null,"'.$insert_id.'","79","1","1")';
			$data=$db->query($sql);
			$_SESSION["ID"]=$db->insert_id;
			$sql='insert into user_log values (null,(select ID from user where openid="'.$openid.'"),"'.time().'")';
			$db->query($sql);
			header("Location:device_list.php");
		}
	}else{
		echo "hehe";
	}
	
?>