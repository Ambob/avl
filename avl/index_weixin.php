<?php  
	$company=$_GET["company"];
	if($company=='liuchang'){
		$appid="wxffa39ad4c8f65606";
	}else{
		$appid = "wxf5cfdaed8cf97363";
	}
    $url = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid='.$appid.'&redirect_uri=http%3a%2f%2ftemp.hbd.so%2favl%2fcall.php?company='.$company.'&response_type=code&scope=snsapi_userinfo&state=#wechat_redirect'; 
    header("Location:".$url); 
?>
