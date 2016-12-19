<?php
	$con=mysqli_connect("127.0.0.1","root","^&^^%)%%","avl","3306");
	mysqli_query($con,'SET NAMES UTF8');
	// Check connection
	if (mysqli_connect_errno($con)){
		echo "Failed to connect to MySQL: " . mysqli_connect_error();
	}
?>
