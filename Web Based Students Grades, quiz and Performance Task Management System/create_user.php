<?php
	header('Content-Type:application/json');
	$host='sql209.infinityfree.com';
	$username='if0_40541077';
	$pass='NqoZSkOjWWEF9Y';
	$db='if0_40541077_system';
	
	$conn= new mysqli($host,$username,$pass,$db);
	$response= [];
	
	if($conn->connect_error){
		$response['msg'] ='Connection Failed'. $conn->connect_error;
		echo json_encode($response);
		exit();
		
	}
	try{
		$email_address	= $_POST['email_address'];
		$password		= $_POST['password'];
		$first_name		= $_POST['first_name'];
		$middle_name	= $_POST['middle_name'];
		$last_name		= $_POST['last_name'];
		
		$stmt=$conn->prepare('INSERT INTO users(email_address,password,first_name,
								middle_name,last_name)
								VALUES(?,?,?,?,?)'); 
		$stmt->bind_param("sssss",$email_address,$password,$first_name,
								$middle_name,$last_name);
		$stmt->execute();
		$response['msg'] = 'User succesfully registered';
		
		$stmt->close();
		$conn->close();
	}
	catch (Exception $e){
		$response['msg']=$e->getMessage();
	}
	echo json_encode($response);




?>