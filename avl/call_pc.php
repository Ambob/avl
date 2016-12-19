<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<?php
include("./action/step.php");
?>
<?php    
	$appid = "wxf5cfdaed8cf97363";  
	$secret = "d84dba2e7bb0183a7ebaac7b9396aab1";  
	$code = $_GET["code"];
	$get_token_url = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid='.$appid.'&secret='.$secret.'&code='.$code.'&grant_type=authorization_code';  
	$content =file_get_contents($get_token_url);
	$arr = json_decode($content,true);
	$openid = $arr['openid'];
	if($openid){
		$sql="select ID,nick_name,head_img,from_unixtime(real_time) as real_time from user where openid='".$openid."'";
		$data=$db->get_row($sql);
		var_dump($data);
		session_start();
		if($data){
			$_SESSION["nickname"]=$data->nick_name;
			$_SESSION["headimgurl"]=$data->head_img;
			$_SESSION["reg_time"]=$data->real_time;
			$_SESSION["ID"]=$data->ID;
			$sql="update user set last_time='".time()."' where openid='".$openid."'";
			$db->query($sql);
			$sql='insert into user_log values (null,(select ID from user where openid="'.$openid.'"),"'.time().'")';
			$db->query($sql);
			header("Location:device_list.php");
		}else{
			echo "string";
			$access_token=$arr['access_token'];
			$get_token_ur='https://api.weixin.qq.com/sns/userinfo?access_token='.$access_token.'&openid='.$openid.'&lang=zh_CN';
			$content1 =file_get_contents($get_token_ur);
			$arr1 = json_decode($content1,true);
			$nickname=$arr1['nickname'];
			$headimgurl=$arr1['headimgurl'];
			$_SESSION["nickname"]=$nickname;
			$_SESSION["headimgurl"]=$headimgurl;
			$_SESSION["reg_time"]=date('Y-m-d H:i:s', time());
			$sql='insert into user values (null,"'.$nickname.'","'.$openid.'","'.time().'","'.$headimgurl.'","","","'.time().'","1")';
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