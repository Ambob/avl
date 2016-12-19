<?php
	header("Content-Type: text/html;charset=utf-8");
	include_once "ez_sql_core.php";
	include_once "ez_sql_pdo.php";
	$db = new ezSQL_pdo('mysql:host=127.0.0.1;dbname=avl', 'root', '^&^^%)%%');
	// $db = new ezSQL_mysql('root','^&^^%)%%','animal_hospital','localhost');
	$db->query("SET NAMES UTF8"); //会报异常，看看运行如果没有乱码就这样
	// $current_time = $db->get_var("SELECT " . $db->sysdate());

?>
